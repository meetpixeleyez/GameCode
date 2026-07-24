import Link from "next/link";
import Image from "next/image";
import { Star, Eye } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { AddToCartButton } from "./add-to-cart";
import { FavoriteButton } from "@/components/FavoriteButton";
import { getCurrentUser } from "@/lib/auth";

interface ProductCardProps {
  product: {
    id: string;
    title: string;
    slug: string;
    price: number;
    thumbnail?: string | null;
    inlinePreviewImage?: string | null;
    avgRating: number;
    totalReview: number;
    totalSold: number;
    user?: {
      username?: string | null;
      firstname?: string | null;
      lastname?: string | null;
    };
    campaignProducts?: {
      discountPercentage: number;
      campaign: { status: number; startDate: Date; endDate: Date };
    }[];
  };
  initialIsFavorited?: boolean;
}

export async function ProductCard({ product, initialIsFavorited = false }: ProductCardProps) {
  const session = await getCurrentUser();
  const isAdmin = session?.role === "admin";
  const authorName = product.user?.username || "Ready Game Code";
  const imageSrc = product.thumbnail || "/products/placeholder.svg";

  // Check for active campaign discount
  let activeDiscount = 0;
  if (product.campaignProducts) {
    const now = new Date();
    const activeCampaign = product.campaignProducts.find(
      cp => cp.campaign.status === 1 && new Date(cp.campaign.startDate) <= now && new Date(cp.campaign.endDate) >= now
    );
    if (activeCampaign) {
      activeDiscount = activeCampaign.discountPercentage;
    }
  }

  const discountedPrice = activeDiscount > 0 
    ? product.price - (product.price * activeDiscount) / 100 
    : product.price;

  return (
    <div className="group rounded-lg border border-border bg-card overflow-hidden hover:shadow-md transition-shadow">
      {/* Image */}
      <div className="relative aspect-[4/3] bg-muted overflow-hidden">
        <Image
          src={imageSrc}
          alt={product.title}
          fill
          sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
          className="object-cover group-hover:scale-105 transition-transform duration-300"
        />
        {/* Live Preview overlay */}
        <Link
          href={`/game-source-code/${product.slug}`}
          className="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100"
        >
          <span className="bg-background/90 text-foreground px-4 py-2 rounded-md text-sm font-medium flex items-center gap-2">
            <Eye className="h-4 w-4" />
            Live Preview
          </span>
        </Link>

        {/* Favorite Button Overlay */}
        {!isAdmin && (
          <div className="absolute top-2 right-2 z-10 bg-background/80 rounded-full backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <FavoriteButton productId={product.id} initialIsFavorited={initialIsFavorited} size={16} />
          </div>
        )}

        {/* Sale Badge */}
        {activeDiscount > 0 && (
          <div className="absolute top-2 left-2 z-10">
            <Badge className="bg-destructive hover:bg-destructive text-white border-none shadow-sm">
              SALE {activeDiscount}% OFF
            </Badge>
          </div>
        )}
      </div>

      {/* Body */}
      <div className="p-4 space-y-3">
        <Link
          href={`/game-source-code/${product.slug}`}
          className="font-medium text-sm line-clamp-2 hover:text-primary transition-colors min-h-[2.5rem] block"
        >
          {product.title}
        </Link>

        <Link
          href={`/authors/${authorName}`}
          className="text-xs text-muted-foreground hover:text-primary transition-colors block"
        >
          by {authorName}
        </Link>

        {/* Rating + Sales */}
        <div className="flex items-center justify-between text-xs text-muted-foreground">
          <div className="flex items-center gap-1">
            <Star className="h-3.5 w-3.5 fill-primary text-primary" />
            <span className="font-medium">{product.avgRating.toFixed(1)}</span>
            <span>({product.totalReview})</span>
          </div>
          <span>{product.totalSold} Sales</span>
        </div>

        {/* Price + Cart */}
        <div className="flex items-center justify-between pt-2 border-t border-border">
          <div className="flex flex-col">
            {activeDiscount > 0 && (
              <span className="text-xs text-muted-foreground line-through">
                ${product.price.toFixed(2)}
              </span>
            )}
            <span className="font-bold text-lg text-primary">
              ${discountedPrice.toFixed(2)}
            </span>
          </div>
          {!isAdmin && (
            <AddToCartButton
              productId={product.id}
              variant="outline"
              size="sm"
              label="Add"
            />
          )}
        </div>
      </div>
    </div>
  );
}
