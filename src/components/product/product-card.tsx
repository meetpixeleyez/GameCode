import Link from "next/link";
import Image from "next/image";
import { ShoppingCart, Star, Eye } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

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
  };
}

export function ProductCard({ product }: ProductCardProps) {
  const authorName = product.user?.username || "Ready Game Code";
  const imageSrc = product.inlinePreviewImage || product.thumbnail || "/products/placeholder.svg";

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
          <div className="font-bold text-lg text-primary">
            ${product.price.toFixed(2)}
          </div>
          <Button size="sm" variant="outline" className="h-8 w-8 p-0">
            <ShoppingCart className="h-4 w-4" />
            <span className="sr-only">Add to cart</span>
          </Button>
        </div>
      </div>
    </div>
  );
}
