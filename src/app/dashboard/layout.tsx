import { redirect } from "next/navigation";
import Link from "next/link";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { LogoutButton } from "@/components/auth/logout-button";
import { LayoutDashboard, Package, Download, User, Store, Heart, Folder, LifeBuoy, ShieldAlert, Wallet } from "lucide-react";

export const dynamic = "force-dynamic";

const navItems = [
  { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
  { href: "/dashboard/purchases", label: "Purchases", icon: Package },
  { href: "/dashboard/downloads", label: "Downloads", icon: Download },
  { href: "/dashboard/favorites", label: "Favorites", icon: Heart },
  { href: "/dashboard/collections", label: "Collections", icon: Folder },
  { href: "/dashboard/deposit", label: "Add Funds", icon: Wallet },
  { href: "/dashboard/kyc", label: "Verification", icon: ShieldAlert },
  { href: "/dashboard/support", label: "Support", icon: LifeBuoy },
  { href: "/dashboard/profile", label: "Profile", icon: User },
];

export default async function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getCurrentUser();
  if (!session) {
    redirect("/login?redirect=/dashboard");
  }

  const user = await db.user.findUnique({
    where: { id: session.sub },
    select: {
      id: true,
      firstname: true,
      lastname: true,
      username: true,
      email: true,
      isAuthor: true,
      balance: true,
    },
  });

  if (!user) {
    redirect("/login");
  }

  const displayName = user.firstname || user.username || "User";
  const initials = displayName.charAt(0).toUpperCase();

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* Sidebar */}
        <aside className="lg:col-span-1">
          <div className="sticky top-24 space-y-4">
            {/* User card */}
            <div className="rounded-lg border border-border bg-card p-4">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                  <span className="font-bold text-primary text-lg">{initials}</span>
                </div>
                <div className="min-w-0">
                  <div className="font-medium text-sm truncate">
                    {displayName}
                  </div>
                  <div className="text-xs text-muted-foreground truncate">
                    {user.email}
                  </div>
                </div>
              </div>
              <div className="mt-3 pt-3 border-t border-border">
                <div className="text-xs text-muted-foreground">Balance</div>
                <div className="font-bold text-primary">${user.balance.toFixed(2)}</div>
              </div>
            </div>

            {/* Nav */}
            <nav className="rounded-lg border border-border bg-card p-2">
              {navItems.map((item) => {
                const Icon = item.icon;
                return (
                  <Link
                    key={item.href}
                    href={item.href}
                    className="flex items-center gap-3 px-3 py-2 rounded-md text-sm hover:bg-accent transition-colors"
                  >
                    <Icon className="h-4 w-4 text-muted-foreground" />
                    {item.label}
                  </Link>
                );
              })}
              {user.isAuthor === 1 && (
                <Link
                  href="/seller"
                  className="flex items-center gap-3 px-3 py-2 rounded-md text-sm hover:bg-accent transition-colors"
                >
                  <Store className="h-4 w-4 text-muted-foreground" />
                  Seller Dashboard
                </Link>
              )}
              <div className="pt-2 mt-2 border-t border-border">
                <LogoutButton
                  variant="ghost"
                  className="w-full justify-start text-destructive hover:text-destructive"
                />
              </div>
            </nav>
          </div>
        </aside>

        {/* Main content */}
        <div className="lg:col-span-3">{children}</div>
      </div>
    </div>
  );
}
