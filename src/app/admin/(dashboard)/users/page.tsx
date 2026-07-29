import Link from "next/link";
import { db } from "@/lib/db";
import { Badge } from "@/components/ui/badge";
import { UserActions } from "./user-actions";
import { formatCurrency } from "@/lib/utils";
import { UserSearch } from "./user-search";

export const dynamic = "force-dynamic";

interface AdminUsersPageProps {
  searchParams: Promise<{ filter?: string; q?: string }>;
}

export default async function AdminUsersPage({ searchParams }: AdminUsersPageProps) {
  const { filter, q } = await searchParams;
  
  let whereClause: any = { role: { notIn: ["ADMIN"] } };
  if (filter === "sellers") {
    whereClause.isAuthor = 1;
  } else if (filter === "buyers") {
    whereClause.isAuthor = 0;
  }

  if (q) {
    whereClause.OR = [
      { firstname: { contains: q, mode: "insensitive" } },
      { lastname: { contains: q, mode: "insensitive" } },
      { email: { contains: q, mode: "insensitive" } },
      { username: { contains: q, mode: "insensitive" } },
    ];
  }

  const users = await db.user.findMany({
    where: whereClause,
    orderBy: { createdAt: "desc" },
    include: {
      _count: {
        select: { products: true, orders: true }
      }
    }
  });

  const baseCountWhere = { role: { notIn: ["ADMIN"] as any } };
  const totalUsers = await db.user.count({ where: baseCountWhere });
  const totalSellers = await db.user.count({ where: { ...baseCountWhere, isAuthor: 1 } });
  const totalBuyers = totalUsers - totalSellers;

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold tracking-tight">Users Management</h1>
          <p className="text-muted-foreground mt-1">
            Review and manage buyers and sellers.
          </p>
        </div>
        <UserSearch />
      </div>

      <div className="flex gap-2 overflow-x-auto pb-2">
        <Link
          href="/admin/users"
          className={`px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors ${
            !filter
              ? "bg-primary text-primary-foreground"
              : "bg-muted text-muted-foreground hover:bg-muted/80"
          }`}
        >
          All Users ({totalUsers})
        </Link>
        <Link
          href="/admin/users?filter=buyers"
          className={`px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors ${
            filter === "buyers"
              ? "bg-primary text-primary-foreground"
              : "bg-muted text-muted-foreground hover:bg-muted/80"
          }`}
        >
          Buyers ({totalBuyers})
        </Link>
        <Link
          href="/admin/users?filter=sellers"
          className={`px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors ${
            filter === "sellers"
              ? "bg-primary text-primary-foreground"
              : "bg-muted text-muted-foreground hover:bg-muted/80"
          }`}
        >
          Sellers ({totalSellers})
        </Link>
      </div>

      <div className="border border-border rounded-xl bg-card overflow-hidden shadow-xs">
        {users.length === 0 ? (
          <div className="p-12 text-center text-muted-foreground">
            No users found.
          </div>
        ) : (
          <div className="overflow-x-auto overflow-y-auto max-h-[500px] min-h-[460px] relative">
            <table className="w-full text-sm text-left border-collapse">
              <thead className="text-xs text-muted-foreground bg-muted/95 backdrop-blur-xs uppercase border-b border-border sticky top-0 z-10 shadow-2xs">
                <tr>
                  <th className="px-6 py-4 font-medium">User</th>
                  <th className="px-6 py-4 font-medium">Role</th>
                  <th className="px-6 py-4 font-medium">Balance</th>
                  <th className="px-6 py-4 font-medium">Stats</th>
                  <th className="px-6 py-4 font-medium">Status</th>
                  <th className="px-6 py-4 font-medium">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                {users.map((user) => (
                  <tr key={user.id} className="hover:bg-muted/30 transition-colors">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                          <span className="font-bold text-primary">
                            {(user.firstname || user.username || "U").charAt(0).toUpperCase()}
                          </span>
                        </div>
                        <div className="min-w-0">
                          <div className="font-medium truncate">
                            {user.firstname} {user.lastname}
                          </div>
                          <div className="text-xs text-muted-foreground mt-0.5">
                            {user.email} &middot; {user.username}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {user.isAuthor === 1 ? (
                        <Badge variant="outline" className="bg-blue-500/10 text-blue-500 border-blue-500/20">Seller</Badge>
                      ) : (
                        <Badge variant="outline" className="bg-slate-500/10 text-slate-500 border-slate-500/20">Buyer</Badge>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {formatCurrency(user.balance || 0)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-xs text-muted-foreground">
                      <div>Orders: {user._count.orders}</div>
                      {user.isAuthor === 1 && <div>Products: {user._count.products}</div>}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {user.status === 1 ? (
                        <Badge variant="default" className="bg-green-500 hover:bg-green-600 text-white">Active</Badge>
                      ) : (
                        <Badge variant="destructive">Banned</Badge>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <UserActions userId={user.id} currentStatus={user.status} />
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
