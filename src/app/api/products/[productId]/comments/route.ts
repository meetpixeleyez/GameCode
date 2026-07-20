import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { z } from "zod";

interface RouteContext {
  params: Promise<{ productId: string }>;
}

// GET /api/products/[productId]/comments — list top-level comments + replies
export async function GET(_req: NextRequest, ctx: RouteContext) {
  try {
    const { productId } = await ctx.params;

    // Get top-level comments (parent_id is null, review_id is null)
    const comments = await db.comment.findMany({
      where: {
        productId,
        parentId: null,
      },
      include: {
        user: {
          select: {
            id: true,
            username: true,
            firstname: true,
            lastname: true,
          },
        },
        replies: {
          include: {
            user: {
              select: {
                id: true,
                username: true,
                firstname: true,
                lastname: true,
              },
            },
          },
          orderBy: { createdAt: "asc" },
        },
      },
      orderBy: { createdAt: "desc" },
    });

    return NextResponse.json({ comments, count: comments.length });
  } catch (error) {
    console.error("GET comments error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}

const createCommentSchema = z.object({
  text: z.string().min(1, "Comment is required").max(5000),
  parentId: z.string().optional(), // for replies
});

// POST /api/products/[productId]/comments — create a comment or reply
export async function POST(req: NextRequest, ctx: RouteContext) {
  try {
    const session = await getCurrentUser();
    if (!session) {
      return NextResponse.json(
        { error: "Please login to comment" },
        { status: 401 }
      );
    }

    const { productId } = await ctx.params;
    const body = await req.json();
    const parsed = createCommentSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json(
        { error: "Invalid input", details: parsed.error.flatten().fieldErrors },
        { status: 400 }
      );
    }

    // Check product exists
    const product = await db.product.findUnique({
      where: { id: productId },
      select: { id: true, commentDisable: true },
    });
    if (!product) {
      return NextResponse.json({ error: "Product not found" }, { status: 404 });
    }

    // Check comments not disabled
    if (product.commentDisable === 1) {
      return NextResponse.json(
        { error: "Comments are disabled on this product" },
        { status: 403 }
      );
    }

    // If replying, validate parent comment exists + belongs to same product
    if (parsed.data.parentId) {
      const parent = await db.comment.findUnique({
        where: { id: parsed.data.parentId },
      });
      if (!parent || parent.productId !== productId) {
        return NextResponse.json(
          { error: "Parent comment not found" },
          { status: 404 }
        );
      }
    }

    // Create comment
    const comment = await db.comment.create({
      data: {
        userId: session.sub,
        productId,
        parentId: parsed.data.parentId || null,
        text: parsed.data.text,
      },
      include: {
        user: {
          select: {
            id: true,
            username: true,
            firstname: true,
            lastname: true,
          },
        },
      },
    });

    return NextResponse.json({ comment }, { status: 201 });
  } catch (error) {
    console.error("POST comment error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
