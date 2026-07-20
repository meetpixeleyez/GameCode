import { redirect } from "next/navigation";
import Link from "next/link";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  ShoppingCart,
  Download,
  Wallet,
  Package,
  TrendingUp,
  ArrowRight,
  Settings,
} from "lucide-react";

export const dynamic = "force-dynamic";

export default async function DashboardPage() {
  const session = await getCurrentUser();
  if (!session) {
    redirect("/login?redirect=/dashboard");
  }

  const user = await db.user.findUnique({
    where: { id: session.userId },
    include: {
      orders: {
        include: { orderItems: { include: { product: true } } },
        orderBy: { createdAt: "desc" },
        take: 5,
      },
      cartItems: true,
    },
  });

  if (!user) {
    redirect("/login");
  }

  const totalPurchases = user.orders.reduce(
    (sum, o) => sum + o.orderItems.length,
    0
  );

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="mb-8">
        <h1 className="text-2xl font-bold">
          Welcome back, {user.firstname || user.username}!
        </h1>
        <p className="text-sm text-muted-foreground mt-1">
          Manage your purchases, downloads, and account settings
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Total Purchases
            </CardTitle>
            <ShoppingCart className="h-4 w-4 text-primary" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{totalPurchases}</div>
            <p className="text-xs text-muted-foreground mt-1">
              across {user.orders.length} orders
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Cart Items
            </CardTitle>
            <Package className="h-4 w-4 text-primary" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{user.cartItems.length}</div>
            <Link
              href="/cart"
              className="text-xs text-primary hover:underline mt-1 inline-block"
            >
              View cart →
            </Link>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Wallet Balance
            </CardTitle>
            <Wallet className="h-4 w-4 text-primary" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">${user.balance.toFixed(2)}</div>
            <p className="text-xs text-muted-foreground mt-1">
              {user.isAuthor === 1 ? "Seller earnings" : "Available"}
            </p>
          </CardContent>
        </Card>

        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Account Status
            </CardTitle>
            <TrendingUp className="h-4 w-4 text-primary" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">
              <Badge variant={user.status === 1 ? "default" : "destructive"}>
                {user.status === 1 ? "Active" : "Banned"}
              </Badge>
            </div>
            <p className="text-xs text-muted-foreground mt-1">
              {user.isAuthor === 1 ? "Author account" : "Buyer account"}
            </p>
          </CardContent>
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2">
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Recent Orders</CardTitle>
            </CardHeader>
            <CardContent>
              {user.orders.length === 0 ? (
                <div className="text-center py-8">
                  <p className="text-sm text-muted-foreground mb-4">
                    No purchases yet
                  </p>
                  <Button asChild>
                    <Link href="/products">
                      Browse Products
                      <ArrowRight className="ml-2 h-4 w-4" />
                    </Link>
                  </Button>
                </div>
              ) : (
                <div className="space-y-3">
                  {user.orders.map((order) => (
                    <div
                      key={order.id}
                      className="flex items-center justify-between border border-border rounded-md p-3"
                    >
                      <div>
                        <div className="font-medium text-sm">Order #{order.trx?.slice(-8)}</div>
                        <div className="text-xs text-muted-foreground">
                          {new Date(order.createdAt).toLocaleDateString()} •{" "}
                          {order.orderItems.length} item(s)
                        </div>
                      </div>
                      <div className="flex items-center gap-3">
                        <div className="font-bold text-primary">
                          ${order.amount.toFixed(2)}
                        </div>
                        <Badge variant={order.paymentStatus === 1 ? "default" : "destructive"}>
                          {order.paymentStatus === 1 ? "Paid" : "Pending"}
                        </Badge>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        <div className="space-y-3">
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Quick Actions</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <Button variant="outline" className="w-full justify-start" asChild>
                <Link href="/dashboard/downloads">
                  <Download className="mr-2 h-4 w-4" />
                  My Downloads
                </Link>
              </Button>
              <Button variant="outline" className="w-full justify-start" asChild>
                <Link href="/dashboard/profile">
                  <Settings className="mr-2 h-4 w-4" />
                  Profile Settings
                </Link>
              </Button>
              {user.isAuthor === 1 && (
                <Button variant="outline" className="w-full justify-start" asChild>
                  <Link href="/seller">
                    <Package className="mr-2 h-4 w-4" />
                    Seller Dashboard
                  </Link>
                </Button>
              )}
              <form action="/api/auth/logout" method="POST">
                <Button type="submit" variant="ghost" className="w-full text-destructive">
                  Sign Out
                </Button>
              </form>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
