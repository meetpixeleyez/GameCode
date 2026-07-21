import { notFound } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import { db } from "@/lib/db";
import { ProductCard } from "@/components/product/product-card";
import { ProductPurchaseSidebar } from "@/components/product/product-purchase-sidebar";
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
    openGraph: {
      title,
      description,
      images: product.previewImage ? [{ url: product.previewImage }] : [],
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
    },
  });

  if (!product || product.status === 5 || product.status === 3) {
    notFound();
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
  const session = await getCurrentUser();
  if (session && session.sub) {
    const fav = await db.productUser.findFirst({
      where: { userId: session.sub, productId: product.id }
    });
    if (fav) isFavorited = true;
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

  return (
    <div className="container mx-auto px-4 py-8">
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
          {/* Preview image */}
          <div className="relative aspect-video bg-muted rounded-lg overflow-hidden border border-border">
            <Image
              src={imageSrc}
              alt={product.title}
              fill
              sizes="(max-width: 1024px) 100vw, 66vw"
              className="object-cover"
              priority
            />
            {youtubeEmbedUrl && (
              <a
                href={product.previewVideo || "#"}
                target="_blank"
                rel="noopener noreferrer"
                className="absolute inset-0 flex items-center justify-center bg-black/0 hover:bg-black/30 transition-colors group"
              >
                <PlayCircle className="h-16 w-16 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
              </a>
            )}
          </div>

          {/* Title + actions row */}
          <div>
            <div className="flex items-start justify-between gap-4 flex-wrap">
              <div className="flex items-center gap-3 flex-1">
                <h1 className="text-2xl md:text-3xl font-bold">{product.title}</h1>
                <FavoriteButton productId={product.id} initialIsFavorited={isFavorited} size={24} />
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
              <TabsTrigger value="comments">Comments ({product.totalReview})</TabsTrigger>
              <TabsTrigger value="changelog">Changelog</TabsTrigger>
            </TabsList>

            <TabsContent value="description" className="mt-4">
              <div
                className="prose prose-sm max-w-none dark:prose-invert"
                dangerouslySetInnerHTML={{
                  __html: product.description || "<p>No description available.</p>",
                }}
              />

              {/* Demo + APK */}
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
                <Button asChild variant="outline">
                  <Link href="#">
                    <Download className="mr-2 h-4 w-4" />
                    Download APK
                  </Link>
                </Button>
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
                  />
                </div>

                <Separator />

                {/* Comments subsection */}
                <div>
                  <h3 className="font-semibold text-lg mb-4">Comments</h3>
                  <CommentsSection productId={product.id} />
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
        <div className="space-y-6">
          {/* Purchase card — interactive client component */}
          <ProductPurchaseSidebar
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
            <Button variant="ghost" size="sm" asChild>
              <Link href={`/authors/${authorName}`}>View Portfolio</Link>
            </Button>
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
