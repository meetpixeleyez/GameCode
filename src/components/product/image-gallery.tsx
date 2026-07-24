"use client";

import { useState } from "react";
import Image from "next/image";
import { PlayCircle } from "lucide-react";
import { cn } from "@/lib/utils";

interface ImageGalleryProps {
  images: string[];
  youtubeEmbedUrl?: string | null;
  productTitle: string;
}

export function ImageGallery({ images, youtubeEmbedUrl, productTitle }: ImageGalleryProps) {
  type MediaItem = { type: "image"; url: string; thumbnail: string };
  const mediaList: MediaItem[] = [];

  images.forEach(img => {
    if (img) {
      mediaList.push({
        type: "image",
        url: img,
        thumbnail: img,
      });
    }
  });

  const [activeIndex, setActiveIndex] = useState(0);
  
  if (mediaList.length === 0) {
    return (
      <div className="relative aspect-video bg-muted rounded-lg overflow-hidden border border-border">
        <Image src="/products/placeholder.svg" alt={productTitle} fill className="object-cover" />
      </div>
    );
  }

  const activeMedia = mediaList[activeIndex];

  return (
    <div className="space-y-4">
      <div className="relative aspect-video bg-muted rounded-lg overflow-hidden border border-border shadow-sm">
        <Image
          src={activeMedia.url}
          alt={`${productTitle} - Image ${activeIndex + 1}`}
          fill
          sizes="(max-width: 1024px) 100vw, 66vw"
          className="object-contain bg-black/5"
          priority
        />
      </div>

      {/* Thumbnails Row */}
      {mediaList.length > 1 && (
        <div className="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-muted-foreground/30">
          {mediaList.map((media, idx) => (
            <button
              key={idx}
              type="button"
              onClick={() => setActiveIndex(idx)}
              className={cn(
                "relative flex-shrink-0 w-24 h-16 md:w-32 md:h-20 rounded-md overflow-hidden border-2 transition-all",
                activeIndex === idx ? "border-primary ring-2 ring-primary/20" : "border-transparent opacity-70 hover:opacity-100"
              )}
            >
              <Image
                src={media.thumbnail}
                alt={`Thumbnail ${idx + 1}`}
                fill
                className="object-cover"
                sizes="(max-width: 768px) 100px, 150px"
                priority={idx < 4}
              />
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
