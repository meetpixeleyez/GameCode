import Link from "next/link";
import Image from "next/image";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";
import { ShoppingCart, Trash2, ArrowRight } from "lucide-react";

export const dynamic = "force-dynamic";

export default async function CartPage() {
  const session = await getCurrentUser();

  // Get cart items — for logged-in users only (guest cart would use session_id + cookies)
  let cartItems: any[] = [];
  if (session?.userId) {
    cartItems = await db.cart.findMany({
      where: { userId: session.userId },
      include: { product: { include: { user: true } } },
    });
  }

  // Calculate totals
  const subtotal = cartItems.reduce((sum, item) => {
    const addonPrice =
      (item.reskinSelected ? item.product.reskinPrice : 0) +
      (item.publishSelected ? item.product.publishPrice : 0) +
      (item.storeOptimizationSelected ? item.product.storeOptimizationPrice : 0);
    return sum + item.price + item.buyerFee + item.extendedAmount + addonPrice;
  }, 0);

  const discount = 0; // coupon system TBD
  const total = subtotal - discount;

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-6">
        <h1 className="text-2xl font-bold">Cart</h1>
        <p className="text-sm text-muted-foreground mt-1">
          {cartItems.length === 0
            ? "Your cart is empty"
            : `${cartItems.length} item${cartItems.length === 1 ? "" : "s"} in your cart`}
        </p>
      </div>

      {cartItems.length === 0 ? (
        <div className="text-center py-16 border border-dashed border-border rounded-lg">
          <ShoppingCart className="h-12 w-12 mx-auto text-muted-foreground mb-4" />
          <p className="text-muted-foreground mb-4">No items in your cart</p>
          <Button asChild>
            <Link href="/products">
              Browse Products
              <ArrowRight className="ml-2 h-4 w-4" />
            </Link>
          </Button>
        </div>
      ) : (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Cart items */}
          <div className="lg:col-span-2 space-y-4">
            {cartItems.map((item) => {
              const addonPrice =
                (item.reskinSelected ? item.product.reskinPrice : 0) +
                (item.publishSelected ? item.product.publishPrice : 0) +
                (item.storeOptimizationSelected ? item.product.storeOptimizationPrice : 0);
              const itemTotal = item.price + item.buyerFee + item.extendedAmount + addonPrice;

              return (
                <div key={item.id} className="border border-border rounded-lg p-4 bg-card">
                  <div className="flex gap-4">
                    {/* Image */}
                    <Link
                      href={`/game-source-code/${item.product.slug}`}
                      className="shrink-0"
                    >
                      <div className="w-24 h-24 rounded-md overflow-hidden bg-muted relative">
                        <Image
                          src={item.product.inlinePreviewImage || item.product.thumbnail || "/products/placeholder.svg"}
                          alt={item.title}
                          fill
                          sizes="96px"
                          className="object-cover"
                        />
                      </div>
                    </Link>

                    {/* Details */}
                    <div className="flex-1 min-w-0">
                      <Link
                        href={`/game-source-code/${item.product.slug}`}
                        className="font-medium text-sm hover:text-primary line-clamp-2"
                      >
                        {item.title}
                      </Link>
                      <p className="text-xs text-muted-foreground mt-1">
                        License: {item.license === "1" ? "Personal" : "Commercial"}
                      </p>
                      {item.isExtended === 1 && (
                        <p className="text-xs text-muted-foreground">
                          + 12-month extended license
                        </p>
                      )}
                      <div className="flex flex-wrap gap-1 mt-2">
                        {item.reskinSelected === 1 && (
                          <span className="text-xs bg-secondary px-2 py-0.5 rounded">Reskin</span>
                        )}
                        {item.publishSelected === 1 && (
                          <span className="text-xs bg-secondary px-2 py-0.5 rounded">Publish</span>
                        )}
                        {item.storeOptimizationSelected === 1 && (
                          <span className="text-xs bg-secondary px-2 py-0.5 rounded">Store Opt</span>
                        )}
                      </div>
                    </div>

                    {/* Price + remove */}
                    <div className="flex flex-col items-end gap-2">
                      <div className="font-bold text-primary">${itemTotal.toFixed(2)}</div>
                      <form action={`/api/cart/remove?id=${item.id}`} method="POST">
                        <Button
                          type="submit"
                          variant="ghost"
                          size="icon"
                          className="h-8 w-8 text-muted-foreground hover:text-destructive"
                        >
                          <Trash2 className="h-4 w-4" />
                          <span className="sr-only">Remove</span>
                        </Button>
                      </form>
                    </div>
                  </div>
                </div>
              );
            })}
          </div>

          {/* Order summary */}
          <div className="lg:col-span-1">
            <div className="border border-border rounded-lg p-4 bg-card sticky top-24 space-y-3">
              <h3 className="font-semibold">Order Summary</h3>
              <Separator />
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Subtotal</span>
                  <span className="font-medium">${subtotal.toFixed(2)}</span>
                </div>
                {discount > 0 && (
                  <div className="flex justify-between text-primary">
                    <span>Discount</span>
                    <span>-${discount.toFixed(2)}</span>
                  </div>
                )}
                <div className="flex justify-between">
                  <span className="text-muted-foreground">Buyer Fee</span>
                  <span className="font-medium">
                    ${cartItems.reduce((s, i) => s + i.buyerFee, 0).toFixed(2)}
                  </span>
                </div>
              </div>
              <Separator />
              <div className="flex justify-between font-bold">
                <span>Total</span>
                <span className="text-primary text-lg">${total.toFixed(2)}</span>
              </div>
              <Button className="w-full" size="lg" asChild>
                <Link href="/checkout">
                  Checkout
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Link>
              </Button>
              <Button variant="outline" className="w-full" asChild>
                <Link href="/products">Continue Shopping</Link>
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
