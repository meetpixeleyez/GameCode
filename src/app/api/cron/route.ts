import { NextResponse } from "next/server";
import { db } from "@/lib/db";

// Ensure this route can't be cached and always runs dynamically
export const dynamic = 'force-dynamic';

export async function GET(request: Request) {
  // Simple basic security: Require a secret key via header or query to execute cron manually,
  // or allow if hit from localhost if testing.
  const authHeader = request.headers.get('authorization');
  if (authHeader !== `Bearer ${process.env.CRON_SECRET || 'secret'}`) {
    return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  }

  try {
    const now = new Date();

    // Find campaigns that are active (1) but their endDate has passed
    const expiredCampaigns = await db.campaign.findMany({
      where: {
        status: 1,
        endDate: { lt: now }
      },
      include: {
        campaignProducts: true
      }
    });

    if (expiredCampaigns.length === 0) {
      return NextResponse.json({ success: true, message: "No campaigns to expire" });
    }

    let expiredCount = 0;
    let productsExpiredCount = 0;

    await db.$transaction(async (tx) => {
      for (const campaign of expiredCampaigns) {
        // Update campaign status to 2 (expired)
        await tx.campaign.update({
          where: { id: campaign.id },
          data: { status: 2 }
        });
        expiredCount++;

        // Update all related campaign products to 3 (expired)
        const updated = await tx.campaignProduct.updateMany({
          where: { campaignId: campaign.id },
          data: { status: 3 }
        });
        productsExpiredCount += updated.count;
      }
    });

    return NextResponse.json({ 
      success: true, 
      message: `Expired ${expiredCount} campaigns and ${productsExpiredCount} products.` 
    });
  } catch (error: any) {
    console.error("Cron Job Error:", error);
    return NextResponse.json({ error: "Internal server error" }, { status: 500 });
  }
}
