import { redirect } from "next/navigation";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { SidebarNav } from "./sidebar-nav";

export const dynamic = "force-dynamic";

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
            <SidebarNav isAuthor={user.isAuthor === 1} />
          </div>
        </aside>

        {/* Main content */}
        <div className="lg:col-span-3">{children}</div>
      </div>
    </div>
  );
}
