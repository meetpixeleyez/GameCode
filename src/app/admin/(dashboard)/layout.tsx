import { redirect } from "next/navigation";
import Link from "next/link";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { LogoutButton } from "@/components/auth/logout-button";
import { MobileNavSelect } from "./mobile-nav";
import {
  LayoutDashboard,
  Users,
  Package,
  Download,
  Settings,
  ShieldCheck,
  FolderTree,
  FileText,
  LifeBuoy,
  ShieldAlert,
} from "lucide-react";

export const dynamic = "force-dynamic";

const navItems = [
  { href: "/admin", label: "Dashboard", icon: LayoutDashboard },
  { href: "/admin/users", label: "Users", icon: Users },
  { href: "/admin/categories", label: "Categories", icon: FolderTree },
  { href: "/admin/products", label: "Products", icon: Package },
  { href: "/admin/withdrawals", label: "Withdrawals", icon: Download },

  { href: "/admin/blog", label: "Blog", icon: FileText },
  { href: "/admin/support", label: "Helpdesk", icon: LifeBuoy },
];

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getCurrentUser();
  if (!session || (session.role !== "admin" && session.role !== "ADMIN")) {
    redirect("/admin/login");
  }

  const admin = await db.user.findUnique({
    where: { id: session.sub },
    select: {
      id: true,
      firstname: true,
      lastname: true,
      username: true,
      email: true,
      role: true,
    },
  });

  if (!admin || admin.role !== "ADMIN") {
    redirect("/admin/login");
  }

  const displayName = admin.firstname || admin.username || "Admin";
  const initials = displayName.charAt(0).toUpperCase();

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {/* Sidebar */}
        <aside className="lg:col-span-1 border-r pr-4 border-border hidden lg:block">
          <div className="sticky top-24 space-y-4">
            {/* Admin card */}
            <div className="rounded-lg border border-border bg-card p-4 text-center">
              <div className="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                <span className="font-bold text-primary text-xl">{initials}</span>
              </div>
              <div className="font-medium truncate">{displayName}</div>
              <div className="text-xs text-muted-foreground flex items-center justify-center gap-1 mt-1">
                <ShieldCheck className="h-3 w-3 text-primary" />
                Administrator
              </div>
            </div>

            {/* Navigation */}
            <nav className="rounded-lg border border-border bg-card overflow-hidden flex flex-col">
              {navItems.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className="flex items-center gap-3 px-4 py-3 text-sm font-medium hover:bg-muted transition-colors border-b border-border last:border-0"
                >
                  <item.icon className="h-4 w-4 text-muted-foreground" />
                  {item.label}
                </Link>
              ))}
              <div className="p-2 border-t border-border">
                <LogoutButton />
              </div>
            </nav>
          </div>
        </aside>

        {/* Mobile Navigation Dropdown (simplified) */}
        <div className="lg:hidden mb-6 flex justify-between items-center bg-card border rounded-lg p-3">
           <div className="flex items-center gap-2">
             <ShieldCheck className="h-5 w-5 text-primary" />
             <span className="font-medium">Admin Panel</span>
           </div>
           <MobileNavSelect navItems={navItems.map(item => ({ href: item.href, label: item.label }))} />
        </div>

        {/* Main content */}
        <main className="lg:col-span-4">
          {children}
        </main>
      </div>
    </div>
  );
}
