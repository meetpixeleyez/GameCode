import { NextRequest, NextResponse } from "next/server";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";

export async function POST(req: NextRequest) {
  try {
    const session = await getCurrentUser();
    if (!session || !session.sub) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    const body = await req.json();
    
    // In a real system, you'd validate the structure of body.
    // We'll store the whole body as JSON in kycData.

    const user = await db.user.findUnique({ where: { id: session.sub } });
    
    if (!user) {
      return NextResponse.json({ error: "User not found" }, { status: 404 });
    }

    if (user.kv === 1 || user.kv === 2) {
      return NextResponse.json({ error: "KYC already verified or pending" }, { status: 400 });
    }

    const updatedUser = await db.user.update({
      where: { id: session.sub },
      data: {
        kycData: JSON.stringify(body),
        kv: 2, // 2 = Pending
      },
    });

    return NextResponse.json({ success: true, kv: updatedUser.kv }, { status: 200 });
  } catch (error) {
    console.error("KYC submission error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
