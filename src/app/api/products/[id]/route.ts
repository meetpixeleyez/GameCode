import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { z } from "zod";

const updateProductSchema = z.object({
  title: z.string().min(3).max(255).optional(),
  description: z.string().min(10).optional(),
  price: z.number().min(0).optional(),
  priceCl: z.number().min(0).optional(),
  demoUrl: z.string().url().optional(),
  previewVideo: z.string().optional().or(z.literal("")),
  thumbnail: z.string().optional().or(z.literal("")),
  tags: z.array(z.string()).optional(),
  metaTitle: z.string().max(255).optional().or(z.literal("")),
  metaDescription: z.string().optional().or(z.literal("")),
  reskinPrice: z.number().min(0).optional(),
  publishPrice: z.number().min(0).optional(),
  storeOptimizationPrice: z.number().min(0).optional(),
});

export async function PUT(req: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  try {
    const session = await getCurrentUser();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    const { id } = await params;
    const product = await db.product.findUnique({ where: { id } });
    
    if (!product || product.userId !== session.sub) {
      return NextResponse.json({ error: "Not found or unauthorized" }, { status: 404 });
    }

    const body = await req.json();
    const parsed = updateProductSchema.safeParse(body);
    if (!parsed.success) {
      return NextResponse.json({ error: "Validation failed", details: parsed.error.flatten().fieldErrors }, { status: 400 });
    }

    const dataToUpdate: any = { ...parsed.data };
    if (parsed.data.tags) {
      dataToUpdate.tags = JSON.stringify(parsed.data.tags);
    }

    const updated = await db.product.update({
      where: { id },
      data: dataToUpdate,
    });

    return NextResponse.json({ success: true, product: updated });
  } catch (error) {
    console.error("Update product error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}

export async function PATCH(req: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  try {
    const session = await getCurrentUser();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    const { id } = await params;
    const product = await db.product.findUnique({ where: { id } });
    
    if (!product || product.userId !== session.sub) {
      return NextResponse.json({ error: "Not found or unauthorized" }, { status: 404 });
    }

    const body = await req.json();
    
    // Only allow setting status to 0 (pending)
    if (body.status === 0 && product.status === 2) {
      const updated = await db.product.update({
        where: { id },
        data: { status: 0 },
      });
      return NextResponse.json({ success: true, product: updated });
    }

    return NextResponse.json({ error: "Invalid status update" }, { status: 400 });
  } catch (error) {
    console.error("Patch product error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}

export async function DELETE(req: NextRequest, { params }: { params: Promise<{ id: string }> }) {
  try {
    const session = await getCurrentUser();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    const { id } = await params;
    const product = await db.product.findUnique({ where: { id } });
    
    if (!product || product.userId !== session.sub) {
      return NextResponse.json({ error: "Not found or unauthorized" }, { status: 404 });
    }

    try {
      await db.product.delete({ where: { id } });
    } catch (dbError: any) {
      if (dbError.code === "P2003") {
        return NextResponse.json({ error: "Cannot delete product. It has existing orders or related records. Please take it down instead." }, { status: 400 });
      }
      throw dbError;
    }

    return NextResponse.json({ success: true });
  } catch (error) {
    console.error("Delete product error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
