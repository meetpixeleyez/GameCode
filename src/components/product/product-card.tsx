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
  priority?: boolean;
}

export async function ProductCard({ product, initialIsFavorited = false, priority = false }: ProductCardProps) {
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
    <div className="group rounded-lg border border-border/80 bg-card overflow-hidden card-hover">
      {/* Image */}
      <div className="relative aspect-[4/3] bg-muted overflow-hidden">
        <Image
          src={imageSrc}
          alt={product.title}
          fill
          sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, (max-width: 1280px) 33vw, 25vw"
          className="object-cover group-hover:scale-105 transition-transform duration-300"
          priority={priority}
        />
        {/* Live Preview overlay */}
        <Link
          href={`/game-source-code/${product.slug}`}
          className="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100"
        >
          <span className="bg-background/90 text-foreground px-3 py-1.5 rounded-md text-xs font-medium flex items-center gap-1.5 shadow-md">
            <Eye className="h-3.5 w-3.5" />
            Live Preview
          </span>
        </Link>

        {/* Favorite Button Overlay */}
        {!isAdmin && (
          <div className="absolute top-2 right-2 z-10 bg-background/80 rounded-full backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <FavoriteButton productId={product.id} initialIsFavorited={initialIsFavorited} size={15} />
          </div>
        )}

        {/* Sale Badge */}
        {activeDiscount > 0 && (
          <div className="absolute top-2 left-2 z-10">
            <Badge className="bg-gradient-to-r from-red-500 to-orange-500 text-white border-none shadow-xs text-[10px] px-2 py-0.5 font-semibold">
              SALE {activeDiscount}% OFF
            </Badge>
          </div>
        )}
      </div>

      {/* Body */}
      <div className="p-3 space-y-2">
        <Link
          href={`/game-source-code/${product.slug}`}
          className="font-medium text-xs sm:text-sm line-clamp-2 hover:text-primary transition-colors min-h-[2.25rem] block leading-tight"
        >
          {product.title}
        </Link>

        <Link
          href={`/authors/${authorName}`}
          className="text-[11px] text-muted-foreground hover:text-primary transition-colors block truncate"
        >
          by {authorName}
        </Link>

        {/* Rating + Sales */}
        <div className="flex items-center justify-between text-[11px] text-muted-foreground">
          <div className="flex items-center gap-1">
            <Star className="h-3 w-3 fill-primary text-primary" />
            <span className="font-medium text-foreground">{product.avgRating.toFixed(1)}</span>
            <span>({product.totalReview})</span>
          </div>
          <span>{product.totalSold} Sales</span>
        </div>

        {/* Price + Cart */}
        <div className="flex items-center justify-between pt-2 border-t border-border">
          <div className="flex flex-col">
            {activeDiscount > 0 && (
              <span className="text-[11px] text-muted-foreground line-through">
                ${product.price.toFixed(2)}
              </span>
            )}
            <span className="font-bold text-base text-primary">
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
