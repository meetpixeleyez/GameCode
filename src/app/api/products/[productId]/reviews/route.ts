import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { z } from "zod";

interface RouteContext {
  params: Promise<{ productId: string }>;
}

// GET /api/products/[productId]/reviews — list reviews for a product
export async function GET(_req: NextRequest, ctx: RouteContext) {
  try {
    const { productId } = await ctx.params;

    const reviews = await db.review.findMany({
      where: { productId },
      include: {
        user: {
          select: {
            id: true,
            username: true,
            firstname: true,
            lastname: true,
            countryName: true,
          },
        },
      },
      orderBy: { createdAt: "desc" },
    });

    const avgRating =
      reviews.length > 0
        ? reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length
        : 0;

    return NextResponse.json({
      reviews,
      count: reviews.length,
      avgRating: avgRating.toFixed(1),
    });
  } catch (error) {
    console.error("GET reviews error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}

const createReviewSchema = z.object({
  rating: z.number().int().min(1).max(5),
  review: z.string().min(1, "Review text is required").max(5000),
  reviewCategoryId: z.string().optional(),
});

// POST /api/products/[productId]/reviews — create a review (must have purchased)
export async function POST(req: NextRequest, ctx: RouteContext) {
  try {
    const session = await getCurrentUser();
    if (!session) {
      return NextResponse.json(
        { error: "Please login to leave a review" },
        { status: 401 }
      );
    }

    const { productId } = await ctx.params;
    const body = await req.json();
    const parsed = createReviewSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json(
        { error: "Invalid input", details: parsed.error.flatten().fieldErrors },
        { status: 400 }
      );
    }

    // Check product exists + is approved
    const product = await db.product.findUnique({
      where: { id: productId },
      select: { id: true, userId: true, status: true, title: true },
    });
    if (!product || product.status !== 1) {
      return NextResponse.json({ error: "Product not found" }, { status: 404 });
    }

    // Cannot review own product
    if (product.userId === session.sub) {
      return NextResponse.json(
        { error: "You cannot review your own product" },
        { status: 400 }
      );
    }

    // Check user has purchased this product
    const hasPurchased = await db.orderItem.findFirst({
      where: {
        userId: session.sub,
        productId,
        order: { paymentStatus: 1 },
      },
    });
    if (!hasPurchased) {
      return NextResponse.json(
        { error: "You can only review products you have purchased" },
        { status: 403 }
      );
    }

    // Check user hasn't already reviewed this product
    const existing = await db.review.findFirst({
      where: { userId: session.sub, productId },
    });
    if (existing) {
      return NextResponse.json(
        { error: "You have already reviewed this product" },
        { status: 409 }
      );
    }

    // Create review
    const review = await db.review.create({
      data: {
        userId: session.sub,
        authorId: product.userId,
        productId,
        reviewCategoryId: parsed.data.reviewCategoryId || null,
        rating: parsed.data.rating,
        review: parsed.data.review,
      },
      include: {
        user: {
          select: {
            id: true,
            username: true,
            firstname: true,
            lastname: true,
            countryName: true,
          },
        },
      },
    });

    // Update product aggregates
    const allReviews = await db.review.findMany({
      where: { productId },
      select: { rating: true },
    });
    const totalReview = allReviews.length;
    const avgRating =
      totalReview > 0
        ? allReviews.reduce((s, r) => s + r.rating, 0) / totalReview
        : 0;

    await db.product.update({
      where: { id: productId },
      data: { totalReview, avgRating },
    });

    // Update author aggregates
    const authorReviews = await db.review.findMany({
      where: { authorId: product.userId },
      select: { rating: true },
    });
    const authorTotal = authorReviews.length;
    const authorAvg =
      authorTotal > 0
        ? authorReviews.reduce((s, r) => s + r.rating, 0) / authorTotal
        : 0;
    await db.user.update({
      where: { id: product.userId },
      data: { totalReview: authorTotal, avgRating: authorAvg },
    });

    return NextResponse.json({ review }, { status: 201 });
  } catch (error) {
    console.error("POST review error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
