import { notFound } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { ProductCard } from "@/components/product/product-card";
import { AuthorHeader } from "@/components/author/author-header";
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

  const user = await getCurrentUser();
  let initialIsFollowing = false;

  if (user) {
    const existingFollow = await db.follow.findFirst({
      where: {
        followerId: user.sub,
        followingId: author.id,
      },
    });
    initialIsFollowing = !!existingFollow;
  }

  const displayName =
    `${author.firstname || ""} ${author.lastname || ""}`.trim() || author.username || "Author";
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
      <AuthorHeader 
        author={author} 
        productsCount={products.length} 
        initialIsFollowing={initialIsFollowing} 
        currentUserId={user?.sub}
      />

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
