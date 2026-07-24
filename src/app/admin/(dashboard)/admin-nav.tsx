"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import { LogoutButton } from "@/components/auth/logout-button";
import {
  LayoutDashboard,
  Users,
  Package,
  Download,
  FolderTree,
  FileText,
  LifeBuoy,
  Undo2,
} from "lucide-react";

export const adminNavItems = [
  { href: "/admin", label: "Dashboard", icon: LayoutDashboard },
  { href: "/admin/users", label: "Users", icon: Users },
  { href: "/admin/categories", label: "Categories", icon: FolderTree },
  { href: "/admin/products", label: "Products", icon: Package },
  { href: "/admin/withdrawals", label: "Withdrawals", icon: Download },
  { href: "/admin/refunds", label: "Refunds", icon: Undo2 },
  { href: "/admin/blog", label: "Blog", icon: FileText },
  { href: "/admin/support", label: "Helpdesk", icon: LifeBuoy },
];

export function AdminNav() {
  const pathname = usePathname();
  
  return (
    <nav className="rounded-lg border border-border bg-card overflow-hidden flex flex-col">
      {adminNavItems.map((item) => {
        const isActive = item.href === "/admin" 
          ? pathname === "/admin" 
          : pathname.startsWith(item.href);
          
        return (
          <Link
            key={item.href}
            href={item.href}
            className={cn(
              "flex items-center gap-3 px-4 py-3 text-sm font-medium transition-colors border-b border-border last:border-0",
              isActive 
                ? "bg-primary text-primary-foreground" 
                : "hover:bg-muted text-muted-foreground"
            )}
          >
            <item.icon className={cn("h-4 w-4", isActive ? "text-primary-foreground" : "text-muted-foreground")} />
            {item.label}
          </Link>
        );
      })}
      <div className="p-2 border-t border-border">
        <LogoutButton />
      </div>
    </nav>
  );
}
