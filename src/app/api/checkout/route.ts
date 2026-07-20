import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { getCartContext } from "@/lib/cart-session";
import { z } from "zod";

const checkoutSchema = z.object({
  gateway: z.enum(["razorpay", "paypal", "manual_upi", "wallet"]),
});

// POST /api/checkout/process — creates order + deposit record, marks as paid (dev mock)
// In production, this would redirect to gateway; for dev we mock payment success
export async function POST(req: NextRequest) {
  try {
    const user = await getCurrentUser();
    if (!user) {
      return NextResponse.json(
        { error: "Please login to checkout", redirectUrl: "/login?redirect=/checkout" },
        { status: 401 }
      );
    }

    const body = await req.json();
    const parsed = checkoutSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json(
        { error: "Invalid payment method" },
        { status: 400 }
      );
    }

    const { gateway } = parsed.data;
    const userId = user.sub;

    // Load cart items
    const cartItems = await db.cart.findMany({
      where: { userId },
      include: {
        product: {
          include: {
            user: { include: { authorLevels: true } },
            category: true,
          },
        },
      },
    });

    if (cartItems.length === 0) {
      return NextResponse.json({ error: "Cart is empty" }, { status: 400 });
    }

    // Check user is not buying own products
    const ownProductInCart = cartItems.some((item) => item.product.userId === userId);
    if (ownProductInCart) {
      return NextResponse.json(
        { error: "You cannot purchase your own products" },
        { status: 400 }
      );
    }

    // Calculate order amount
    const amount = cartItems.reduce((sum, item) => {
      const addonPrice =
        (item.reskinSelected ? item.product.reskinPrice : 0) +
        (item.publishSelected ? item.product.publishPrice : 0) +
        (item.storeOptimizationSelected ? item.product.storeOptimizationPrice : 0);
      return sum + item.price + item.buyerFee + item.extendedAmount + addonPrice;
    }, 0);

    // Generate transaction IDs (matches Laravel getTrx format: 12-char uppercase alphanumeric)
    const trx = generateTrx(12);
    const methodCode = gateway === "razorpay" ? 110 : gateway === "paypal" ? 101 : 1000;
    const methodCurrency = gateway === "razorpay" || gateway === "manual_upi" ? "INR" : "USD";

    // Create order + order items in a transaction
    const order = await db.$transaction(async (tx) => {
      // 1. Create order
      const newOrder = await tx.order.create({
        data: {
          userId,
          amount,
          discount: 0,
          trx,
          paymentStatus: 1, // mark paid immediately (dev mock)
        },
      });

      // 2. Create order items with seller_earning calculation
      for (const item of cartItems) {
        const author = item.product.user;
        const authorLevel = author?.authorLevels?.[0]; // highest tier (assumed sorted desc)

        // Seller fee = authorLevel.fee (%) * price
        const sellerFeePercent = authorLevel?.fee || 0;
        const sellerFee = (sellerFeePercent / 100) * item.price;

        // Addon services amount
        const addonAmount =
          (item.reskinSelected ? item.product.reskinPrice : 0) +
          (item.publishSelected ? item.product.publishPrice : 0) +
          (item.storeOptimizationSelected ? item.product.storeOptimizationPrice : 0);

        // Seller earning = (price - (seller_fee + discount)) + extended_amount + addon_amount
        const sellerEarning =
          (item.price - (sellerFee + item.discount)) + item.extendedAmount + addonAmount;

        // Generate unique purchase code (matches Laravel format: userId-productId-random-timestamp)
        const purchaseCode = `${userId.slice(-6)}-${item.productId.slice(-6)}-${generateTrx(6)}-${Date.now().toString(36)}`;

        await tx.orderItem.create({
          data: {
            orderId: newOrder.id,
            purchaseCode,
            userId,
            productId: item.productId,
            isExtended: item.isExtended,
            extendedAmount: item.extendedAmount,
            productPrice: item.price,
            sellerFee,
            buyerFee: item.buyerFee,
            quantity: 1,
            license: parseInt(item.license),
            discount: item.discount,
            sellerEarning,
            reskinSelected: item.reskinSelected,
            publishSelected: item.publishSelected,
            storeOptimizationSelected: item.storeOptimizationSelected,
          },
        });

        // Update product sales count
        await tx.product.update({
          where: { id: item.productId },
          data: { totalSold: { increment: 1 } },
        });

        // Credit seller balance + update counters
        if (author) {
          await tx.user.update({
            where: { id: author.id },
            data: {
              balance: { increment: sellerEarning },
              totalSold: { increment: 1 },
              totalSoldAmount: { increment: sellerEarning - sellerFee },
            },
          });

          // Create seller credit transaction
          await tx.transaction.create({
            data: {
              userId: author.id,
              amount: sellerEarning,
              charge: 0,
              postBalance: author.balance + sellerEarning,
              trxType: "+",
              trx,
              details: "Sale Amount Added",
              remark: "new_sale",
            },
          });

          // If seller fee > 0, create seller fee debit transaction
          if (sellerFee > 0) {
            await tx.transaction.create({
              data: {
                userId: author.id,
                amount: sellerFee,
                charge: 0,
                postBalance: author.balance + sellerEarning - sellerFee,
                trxType: "-",
                trx,
                details: "Seller Fee Subtracted",
                remark: "seller_fee",
              },
            });
          }
        }
      }

      // 3. Create deposit record (for audit trail)
      await tx.deposit.create({
        data: {
          userId,
          orderId: newOrder.id,
          methodCode,
          methodCurrency,
          amount,
          charge: 0,
          rate: 1,
          finalAmount: amount,
          trx: generateTrx(12),
          status: 1, // PAID (dev mock)
          successUrl: "/checkout/thank-you",
          failedUrl: "/checkout",
        },
      });

      // 4. Create buyer debit transaction
      await tx.transaction.create({
        data: {
          userId,
          amount,
          charge: 0,
          postBalance: 0, // would be user.balance - amount in real flow
          trxType: "-",
          trx,
          details: "Payment for Purchase Item",
          remark: "purchase",
        },
      });

      // 5. Clear cart
      await tx.cart.deleteMany({ where: { userId } });

      return newOrder;
    });

    return NextResponse.json({
      success: true,
      orderTrx: order.trx,
      redirectUrl: `/checkout/thank-you?trx=${order.trx}`,
    });
  } catch (error) {
    console.error("Checkout error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}

// Generate Laravel-style transaction ID (12-char uppercase alphanumeric)
function generateTrx(length: number = 12): string {
  const chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
  let result = "";
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  return result;
}
