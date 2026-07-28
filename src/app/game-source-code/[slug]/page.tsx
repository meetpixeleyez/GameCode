import { notFound } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import { db } from "@/lib/db";
import { ProductCard } from "@/components/product/product-card";
import { ProductPurchaseSidebar } from "@/components/product/product-purchase-sidebar";
import { ImageGallery } from "@/components/product/image-gallery";
import { ReviewsSection } from "@/components/review/reviews-section";
import { CommentsSection } from "@/components/review/comments-section";
import { FavoriteButton } from "@/components/FavoriteButton";
import { getCurrentUser } from "@/lib/auth";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Star,
  Eye,
  Download,
  PlayCircle,
  Users,
  Tag,
  Mail,
} from "lucide-react";

import { JsonLd } from "@/components/seo/json-ld";

export const dynamic = "force-dynamic";

interface ProductPageProps {
  params: Promise<{ slug: string }>;
}

export async function generateMetadata({ params }: ProductPageProps) {
  const { slug } = await params;
  const product = await db.product.findUnique({
    where: { slug },
    include: { user: true, category: true, subCategory: true },
  });

  if (!product) {
    return { title: "Product Not Found" };
  }

  const title = product.metaTitle || product.title;
  const description = product.metaDescription || product.description?.replace(/<[^>]+>/g, "").slice(0, 155);

  return {
    title,
    description,
    alternates: {
      canonical: `/game-source-code/${slug}`,
    },
    openGraph: {
      title,
      description,
      images: product.previewImage || product.thumbnail ? [{ url: product.previewImage || product.thumbnail }] : [],
      type: "website",
    },
    twitter: {
      card: "summary_large_image",
      title,
      description,
    },
  };
}

export default async function ProductDetailPage({ params }: ProductPageProps) {
  const { slug } = await params;

  const product = await db.product.findUnique({
    where: { slug },
    include: {
      user: true,
      category: true,
      subCategory: true,
      changelogs: {
        orderBy: { createdAt: "desc" },
      },
      _count: {
        select: { comments: true },
      },
    },
  });

  const session = await getCurrentUser();
  const isAdmin = session?.role === "admin" || session?.role === "ADMIN";
  const isAuthor = session?.sub === product?.userId;

  if (!product) {
    notFound();
  }

  // If product is not approved (status 1), only author and admin can view it
  if (product.status !== 1 && !isAdmin && !isAuthor) {
    return (
      <div className="container mx-auto px-4 py-32 text-center flex flex-col items-center justify-center min-h-[60vh]">
        <div className="bg-muted/50 p-6 rounded-full mb-6">
          <Eye className="h-10 w-10 text-muted-foreground opacity-50" />
        </div>
        <h1 className="text-2xl font-bold mb-3">Product Unavailable</h1>
        <p className="text-muted-foreground max-w-md mx-auto mb-8 text-sm">
          This product is currently under review, has been taken down, or is otherwise not available for public viewing at this time.
        </p>
        <Button asChild>
          <Link href="/products">
            Browse Other Products
          </Link>
        </Button>
      </div>
    );
  }

  // Increment view count (1 row per product per day)
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const existingView = await db.productView.findFirst({
    where: {
      productId: product.id,
      viewsDate: today,
    },
  });
  if (existingView) {
    await db.productView.update({
      where: { id: existingView.id },
      data: { views: { increment: 1 } },
    });
  } else {
    await db.productView.create({
      data: {
        productId: product.id,
        views: 1,
        viewsDate: today,
      },
    });
  }

  // Check if user has favorited
  let isFavorited = false;
  if (session && session.sub && !isAdmin) {
    const fav = await db.productUser.findFirst({
      where: { userId: session.sub, productId: product.id }
    });
    if (fav) isFavorited = true;
  }

  // Check if user has purchased the item or if it's free
  let hasPurchased = isAdmin || isAuthor || product.isFree === 1;
  if (!hasPurchased && session?.sub) {
    const purchaseCount = await db.orderItem.count({
      where: {
        productId: product.id,
        userId: session.sub,
        order: { paymentStatus: 1 },
      },
    });
    if (purchaseCount > 0) {
      hasPurchased = true;
    }
  }

  // Get more items by the same author
  const moreByAuthor = await db.product.findMany({
    where: {
      userId: product.userId,
      status: 1,
      id: { not: product.id },
    },
    include: { user: true },
    take: 8,
    orderBy: { totalSold: "desc" },
  });

  const authorName = product.user?.username || "Ready Game Code";
  const imageSrc = product.inlinePreviewImage || product.thumbnail || "/products/placeholder.svg";
  const tags: string[] = product.tags ? JSON.parse(product.tags) : [];

  // Convert YouTube URL to embed URL
  const youtubeEmbedUrl = product.previewVideo
    ? product.previewVideo.replace("watch?v=", "embed/").replace("youtu.be/", "youtube.com/embed/")
    : null;

  // Compile images for gallery
  let screenshots: string[] = [];
  try {
    if (product.inlinePreviewImage) {
      screenshots = JSON.parse(product.inlinePreviewImage);
      if (!Array.isArray(screenshots)) screenshots = [screenshots];
    }
  } catch(e) {}

  if (screenshots.length === 0 && product.thumbnail) {
    screenshots = [product.thumbnail];
  } else if (product.thumbnail && !screenshots.includes(product.thumbnail)) {
    screenshots = [product.thumbnail, ...screenshots];
  }

  const baseUrl = process.env.NEXT_PUBLIC_APP_URL || "https://readygamecode.com";

  const productSchema = {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": product.title,
    "description": product.metaDescription || product.description?.replace(/<[^>]+>/g, "").slice(0, 200),
    "url": `${baseUrl}/game-source-code/${product.slug}`,
    "image": product.thumbnail || product.previewImage || `${baseUrl}/logo.png`,
    "applicationCategory": "GameApplication",
    "operatingSystem": "Unity, Android, iOS, Windows",
    "offers": {
      "@type": "Offer",
      "price": product.price.toFixed(2),
      "priceCurrency": "USD",
      "availability": "https://schema.org/InStock",
      "url": `${baseUrl}/game-source-code/${product.slug}`,
      "seller": {
        "@type": "Person",
        "name": authorName,
      },
    },
    "aggregateRating": product.totalReview > 0 ? {
      "@type": "AggregateRating",
      "ratingValue": product.avgRating.toFixed(1),
      "reviewCount": product.totalReview,
      "bestRating": "5",
      "worstRating": "1",
    } : undefined,
  };

  const breadcrumbSchema = {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": baseUrl,
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Products",
        "item": `${baseUrl}/products`,
      },
      {
        "@type": "ListItem",
        "position": 3,
        "name": product.category?.name || "Game Code",
        "item": `${baseUrl}/products?category=${encodeURIComponent(product.category?.name || "")}`,
      },
      {
        "@type": "ListItem",
        "position": 4,
        "name": product.title,
        "item": `${baseUrl}/game-source-code/${product.slug}`,
      },
    ],
  };

  return (
    <div className="container mx-auto px-4 py-8">
      <JsonLd data={[productSchema, breadcrumbSchema]} />
      {/* Breadcrumb */}
      <nav className="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <Link href="/" className="hover:text-primary">Home</Link>
        <span>/</span>
        <Link href="/products" className="hover:text-primary">Products</Link>
        <span>/</span>
        <Link
          href={`/products?category=${product.categoryId}`}
          className="hover:text-primary"
        >
          {product.category?.name}
        </Link>
        <span>/</span>
        <span className="text-foreground truncate max-w-xs">{product.title}</span>
      </nav>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Main content - 2/3 width */}
        <div className="lg:col-span-2 space-y-6">
          {/* Interactive Image Gallery */}
          <ImageGallery 
            images={screenshots}
            youtubeEmbedUrl={youtubeEmbedUrl}
            productTitle={product.title}
          />

          {/* Title + actions row */}
          <div>
            <div className="flex items-start justify-between gap-4 flex-wrap">
              <div className="flex items-center gap-3 flex-1">
                <h1 className="text-2xl md:text-3xl font-bold">{product.title}</h1>
                {!isAdmin && (
                  <FavoriteButton productId={product.id} initialIsFavorited={isFavorited} size={24} />
                )}
              </div>
              <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <div className="flex items-center gap-1">
                  <Star className="h-4 w-4 fill-primary text-primary" />
                  <span className="font-medium text-foreground">
                    {product.avgRating.toFixed(1)}
                  </span>
                  <span>({product.totalReview} reviews)</span>
                </div>
                <span>·</span>
                <span>{product.totalSold} sales</span>
              </div>
            </div>

            {/* Author */}
            <div className="flex items-center gap-2 mt-3 text-sm">
              <span className="text-muted-foreground">by</span>
              <Link
                href={`/authors/${authorName}`}
                className="font-medium text-primary hover:underline"
              >
                {authorName}
              </Link>
              {product.user?.isAuthor === 1 && (
                <Badge variant="secondary" className="text-xs">Author</Badge>
              )}
            </div>
          </div>

          {/* Tabs */}
          <Tabs defaultValue="description" className="w-full">
            <TabsList className="grid w-full grid-cols-3">
              <TabsTrigger value="description">Description</TabsTrigger>
              <TabsTrigger value="comments">Comments ({product._count.comments})</TabsTrigger>
              <TabsTrigger value="changelog">Changelog</TabsTrigger>
            </TabsList>

            <TabsContent value="description" className="mt-4">
              <div
                className="prose prose-sm max-w-none dark:prose-invert"
                dangerouslySetInnerHTML={{
                  __html: product.description || "<p>No description available.</p>",
                }}
              />

              {/* Demo */}
              <div className="flex flex-wrap gap-3 mt-6">
                {product.demoUrl && (
                  <Button asChild variant="outline">
                    <a
                      href={product.demoUrl}
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      <Eye className="mr-2 h-4 w-4" />
                      Live Preview
                    </a>
                  </Button>
                )}
              </div>

              {/* Video preview */}
              {youtubeEmbedUrl && (
                <div className="mt-6">
                  <h3 className="font-semibold text-lg mb-3 flex items-center gap-2">
                    <PlayCircle className="h-5 w-5 text-primary" />
                    Watch Gameplay Video
                  </h3>
                  <div className="relative aspect-video bg-black rounded-lg overflow-hidden">
                    <iframe
                      src={youtubeEmbedUrl}
                      title={`${product.title} gameplay`}
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowFullScreen
                      className="absolute inset-0 w-full h-full"
                    />
                  </div>
                </div>
              )}
            </TabsContent>

            <TabsContent value="comments" className="mt-4">
              <div className="space-y-8">
                {/* Reviews subsection */}
                <div>
                  <h3 className="font-semibold text-lg mb-4">Reviews</h3>
                  <ReviewsSection
                    productId={product.id}
                    initialAvgRating={product.avgRating}
                    initialTotalReview={product.totalReview}
                    userId={session?.sub}
                  />
                </div>

                <Separator />

                {/* Comments subsection */}
                <div>
                  <h3 className="font-semibold text-lg mb-4">Comments</h3>
                  <CommentsSection productId={product.id} userId={session?.sub} />
                </div>
              </div>
            </TabsContent>

            <TabsContent value="changelog" className="mt-4">
              {product.changelogs.length > 0 ? (
                <div className="space-y-4">
                  {product.changelogs.map((log) => (
                    <div
                      key={log.id}
                      className="rounded-lg border border-border p-4 bg-card"
                    >
                      <h4 className="font-semibold text-sm">{log.heading}</h4>
                      <p className="text-sm text-muted-foreground mt-1">
                        {log.description}
                      </p>
                      <p className="text-xs text-muted-foreground mt-2">
                        {new Date(log.createdAt).toLocaleDateString()}
                      </p>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-sm text-muted-foreground text-center py-6">
                  No changelogs yet.
                </p>
              )}
            </TabsContent>
          </Tabs>
        </div>

        {/* Sidebar - 1/3 width */}
        <div className="space-y-6 lg:sticky lg:top-24 lg:self-start">
          {/* Purchase card — interactive client component */}
          <ProductPurchaseSidebar
            isAdmin={session?.role === "admin"}
            product={{
              id: product.id,
              title: product.title,
              slug: product.slug,
              price: product.price,
              priceCl: product.priceCl,
              reskinPrice: product.reskinPrice,
              publishPrice: product.publishPrice,
              storeOptimizationPrice: product.storeOptimizationPrice,
              demoUrl: product.demoUrl,
              category: product.category ? {
                personalBuyerFee: product.category.personalBuyerFee,
                commercialBuyerFee: product.category.commercialBuyerFee,
                twelveMonthExtendedFee: product.category.twelveMonthExtendedFee,
              } : null,
              hasPurchased,
              fileUrl: product.file,
            }}
          />

          {/* Author card */}
          <div className="rounded-lg border border-border bg-card p-6">
            <h4 className="font-semibold text-sm mb-3">Author</h4>
            <Link
              href={`/authors/${authorName}`}
              className="flex items-center gap-3 hover:bg-accent/50 -mx-2 px-2 py-2 rounded-md transition-colors"
            >
              <div className="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                <span className="font-bold text-primary">
                  {authorName.charAt(0).toUpperCase()}
                </span>
              </div>
              <div>
                <div className="font-medium text-sm">{authorName}</div>
                <div className="text-xs text-muted-foreground">
                  {product.user?.totalSold || 0} sales · {product.user?.totalReview || 0} reviews
                </div>
              </div>
            </Link>
            <Button variant="outline" size="sm" className="w-full mt-3" asChild>
              <Link href={`/authors/${authorName}`}>
                View Portfolio
              </Link>
            </Button>
          </div>

          {/* Tags */}
          {tags.length > 0 && (
            <div className="rounded-lg border border-border bg-card p-6">
              <h4 className="font-semibold text-sm mb-3 flex items-center gap-2">
                <Tag className="h-4 w-4" />
                Tags
              </h4>
              <div className="flex flex-wrap gap-2">
                {tags.map((tag) => (
                  <Link
                    key={tag}
                    href={`/products?search=${encodeURIComponent(tag)}`}
                    className="text-xs px-2 py-1 rounded-md bg-accent hover:bg-accent/70 transition-colors"
                  >
                    {tag}
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Support contact */}
          <div className="rounded-lg border border-border bg-card p-6">
            <h4 className="font-semibold text-sm mb-3">Need Help?</h4>
            <p className="text-xs text-muted-foreground mb-3">
              Have questions about this source code? Contact the author or our support team.
            </p>
            <Button variant="outline" size="sm" className="w-full" asChild>
              <Link href="/contact">
                <Mail className="mr-2 h-4 w-4" />
                Contact Support
              </Link>
            </Button>
          </div>
        </div>
      </div>

      {/* More items by author */}
      {moreByAuthor.length > 0 && (
        <section className="mt-16">
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-xl font-bold">More items by {authorName}</h2>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            {moreByAuthor.slice(0, 4).map((p) => (
              <ProductCard key={p.id} product={p} />
            ))}
          </div>
        </section>
      )}
    </div>
  );
}
