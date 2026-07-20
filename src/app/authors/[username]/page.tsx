import { notFound } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import { db } from "@/lib/db";
import { ProductCard } from "@/components/product/product-card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  Star,
  Users,
  Package,
  TrendingUp,
  CheckCircle2,
  ShoppingCart,
  Mail,
} from "lucide-react";

export const dynamic = "force-dynamic";

interface AuthorPageProps {
  params: Promise<{ username: string }>;
}

export async function generateMetadata({ params }: AuthorPageProps) {
  const { username } = await params;
  const author = await db.user.findUnique({
    where: { username },
    select: { username: true, firstname: true, lastname: true, isAuthor: true },
  });

  if (!author || author.isAuthor !== 1) {
    return { title: "Author Not Found" };
  }

  const name = `${author.firstname || ""} ${author.lastname || ""}`.trim() || author.username;
  return {
    title: `${name} — Author Profile`,
    description: `Browse game source codes by ${name} on Ready Game Code.`,
  };
}

export default async function AuthorProfilePage({ params }: AuthorPageProps) {
  const { username } = await params;

  const author = await db.user.findUnique({
    where: { username },
    select: {
      id: true,
      firstname: true,
      lastname: true,
      username: true,
      email: true,
      isAuthor: true,
      isAuthorFeatured: true,
      totalSold: true,
      totalSoldAmount: true,
      totalReview: true,
      avgRating: true,
      totalFollower: true,
      totalFollowing: true,
      countryName: true,
      city: true,
      createdAt: true,
    },
  });

  if (!author || author.isAuthor !== 1) {
    notFound();
  }

  // Get author's products
  const [products, collections] = await Promise.all([
    db.product.findMany({
      where: { userId: author.id, status: 1 },
      include: { user: true },
      orderBy: { totalSold: "desc" },
    }),
    db.productCollection.findMany({
      where: { userId: author.id },
      take: 10,
    }),
  ]);

  const displayName =
    `${author.firstname || ""} ${author.lastname || ""}`.trim() || author.username;
  const initials = displayName.charAt(0).toUpperCase();

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Breadcrumb */}
      <nav className="flex items-center gap-2 text-sm text-muted-foreground mb-6">
        <Link href="/" className="hover:text-primary">Home</Link>
        <span>/</span>
        <span>Authors</span>
        <span>/</span>
        <span className="text-foreground">{author.username}</span>
      </nav>

      {/* Profile header */}
      <div className="rounded-lg border border-border bg-card p-6 mb-8">
        <div className="flex flex-col md:flex-row items-start md:items-center gap-6">
          {/* Avatar */}
          <div className="w-24 h-24 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
            <span className="font-bold text-primary text-3xl">{initials}</span>
          </div>

          {/* Info */}
          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-2 flex-wrap">
              <h1 className="text-2xl font-bold">{displayName}</h1>
              {author.isAuthorFeatured === 1 && (
                <Badge className="text-xs">
                  <CheckCircle2 className="h-3 w-3 mr-1" />
                  Featured Author
                </Badge>
              )}
            </div>
            <p className="text-sm text-muted-foreground mt-1">@{author.username}</p>

            {(author.city || author.countryName) && (
              <p className="text-sm text-muted-foreground mt-2">
                {author.city}
                {author.city && author.countryName ? ", " : ""}
                {author.countryName}
              </p>
            )}

            <p className="text-xs text-muted-foreground mt-2">
              Member since{" "}
              {new Date(author.createdAt).toLocaleDateString(undefined, {
                month: "long",
                year: "numeric",
              })}
            </p>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 w-full md:w-auto">
            <Stat icon={Package} value={products.length} label="Products" />
            <Stat icon={TrendingUp} value={author.totalSold} label="Sales" />
            <Stat
              icon={Star}
              value={author.avgRating.toFixed(1)}
              label={`(${author.totalReview})`}
            />
            <Stat icon={Users} value={author.totalFollower} label="Followers" />
          </div>
        </div>

        <Separator className="my-4" />

        <div className="flex flex-wrap gap-2">
          <Button size="sm" variant="outline">
            <Users className="mr-2 h-4 w-4" />
            Follow
          </Button>
          <Button size="sm" variant="outline" asChild>
            <Link href="/contact">
              <Mail className="mr-2 h-4 w-4" />
              Contact
            </Link>
          </Button>
          {products.length > 0 && (
            <Button size="sm" asChild>
              <Link href="/products">
                <ShoppingCart className="mr-2 h-4 w-4" />
                Browse Products
              </Link>
            </Button>
          )}
        </div>
      </div>

      {/* Tabs: Products / Collections */}
      <Tabs defaultValue="products" className="w-full">
        <TabsList>
          <TabsTrigger value="products">
            Products ({products.length})
          </TabsTrigger>
          <TabsTrigger value="collections">
            Collections ({collections.length})
          </TabsTrigger>
        </TabsList>

        <TabsContent value="products" className="mt-6">
          {products.length === 0 ? (
            <div className="text-center py-16 rounded-lg border border-dashed border-border">
              <Package className="h-12 w-12 mx-auto text-muted-foreground/50 mb-4" />
              <h3 className="font-semibold">No products yet</h3>
              <p className="text-sm text-muted-foreground mt-1">
                This author hasn&apos;t published any products.
              </p>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              {products.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          )}
        </TabsContent>

        <TabsContent value="collections" className="mt-6">
          {collections.length === 0 ? (
            <div className="text-center py-16 rounded-lg border border-dashed border-border">
              <Package className="h-12 w-12 mx-auto text-muted-foreground/50 mb-4" />
              <h3 className="font-semibold">No collections</h3>
              <p className="text-sm text-muted-foreground mt-1">
                This author hasn&apos;t created any product collections.
              </p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {collections.map((col) => (
                <div
                  key={col.id}
                  className="rounded-lg border border-border bg-card p-4 hover:shadow-md transition-shadow"
                >
                  <h3 className="font-semibold text-sm">{col.name}</h3>
                  {col.description && (
                    <p className="text-xs text-muted-foreground mt-1 line-clamp-2">
                      {col.description}
                    </p>
                  )}
                </div>
              ))}
            </div>
          )}
        </TabsContent>
      </Tabs>
    </div>
  );
}

function Stat({
  icon: Icon,
  value,
  label,
}: {
  icon: React.ComponentType<{ className?: string }>;
  value: string | number;
  label: string;
}) {
  return (
    <div className="text-center">
      <Icon className="h-5 w-5 text-primary mx-auto mb-1" />
      <div className="text-lg font-bold">{value}</div>
      <div className="text-xs text-muted-foreground">{label}</div>
    </div>
  );
}
