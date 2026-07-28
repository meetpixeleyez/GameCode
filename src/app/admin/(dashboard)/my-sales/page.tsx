import Link from "next/link";
import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { redirect } from "next/navigation";
import { Badge } from "@/components/ui/badge";
import { formatCurrency } from "@/lib/utils";
import {
  ShoppingBag,
  DollarSign,
  TrendingUp,
  CreditCard,
  ArrowUpRight,
  ArrowDownRight,
  User,
  Calendar,
  Package,
} from "lucide-react";

export const dynamic = "force-dynamic";

export default async function AdminMySalesPage() {
  const session = await getCurrentUser();
  if (!session || (session.role !== "admin" && session.role !== "ADMIN")) {
    redirect("/admin/login");
  }

  const userId = session.sub;

  const [user, orderItems, transactions, myProductsCount] = await Promise.all([
    db.user.findUnique({
      where: { id: userId },
      select: { balance: true, totalSold: true, totalSoldAmount: true },
    }),
    db.orderItem.findMany({
      where: { product: { userId } },
      include: {
        product: { select: { id: true, title: true, slug: true } },
        user: { select: { id: true, username: true, email: true, firstname: true, lastname: true } },
      },
      orderBy: { createdAt: "desc" },
      take: 50,
    }),
    db.transaction.findMany({
      where: { userId },
      orderBy: { createdAt: "desc" },
      take: 50,
    }),
    db.product.count({ where: { userId } }),
  ]);

  const activeOrderItems = orderItems.filter((item) => item.isRefunded === 0);
  const totalSalesCount = user?.totalSold !== undefined ? user.totalSold : activeOrderItems.length;
  const totalEarnedAmount = user?.totalSoldAmount !== undefined
    ? user.totalSoldAmount
    : activeOrderItems.reduce((sum, item) => sum + (item.sellerEarning || item.productPrice), 0);

  const remarkLabels: Record<string, string> = {
    new_sale: "Product Sale",
    seller_fee: "Platform Fee",
    payment: "Payment Received",
    purchase: "Item Purchased",
    balance_add: "Balance Added",
    withdrawal: "Withdrawal",
    refund_debit: "Refund Debit",
    refund_credit: "Refund Credit",
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold tracking-tight">My Sales &amp; Earnings</h1>
        <p className="text-muted-foreground mt-1">
          Unified overview of product sales, buyer orders, and financial transactions.
        </p>
      </div>

      {/* 4-Grid Summary Metrics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="rounded-xl border border-border bg-card p-4 flex items-center gap-4">
          <div className="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
            <DollarSign className="h-5 w-5" />
          </div>
          <div>
            <p className="text-xs text-muted-foreground">Total Sales Revenue</p>
            <p className="text-xl font-bold text-emerald-600 dark:text-emerald-400">
              {formatCurrency(totalEarnedAmount)}
            </p>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 flex items-center gap-4">
          <div className="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500">
            <ShoppingBag className="h-5 w-5" />
          </div>
          <div>
            <p className="text-xs text-muted-foreground">Total Orders Sold</p>
            <p className="text-xl font-bold">{totalSalesCount}</p>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 flex items-center gap-4">
          <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
            <CreditCard className="h-5 w-5" />
          </div>
          <div>
            <p className="text-xs text-muted-foreground">Available Balance</p>
            <p className="text-xl font-bold">{formatCurrency(user?.balance || 0)}</p>
          </div>
        </div>

        <div className="rounded-xl border border-border bg-card p-4 flex items-center gap-4">
          <div className="w-10 h-10 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500">
            <Package className="h-5 w-5" />
          </div>
          <div>
            <p className="text-xs text-muted-foreground">My Uploaded Products</p>
            <p className="text-xl font-bold">{myProductsCount}</p>
          </div>
        </div>
      </div>

      {/* Section 1: Sales Orders History */}
      <div className="border border-border rounded-xl bg-card overflow-hidden">
        <div className="p-4 border-b border-border bg-muted/30 flex items-center justify-between">
          <h2 className="font-semibold text-base flex items-center gap-2">
            <ShoppingBag className="h-4 w-4 text-primary" />
            Recent Sales Orders
          </h2>
          <Badge variant="outline" className="text-xs">
            {orderItems.length} Recent Sales
          </Badge>
        </div>

        {orderItems.length === 0 ? (
          <div className="p-8 text-center text-muted-foreground">
            <ShoppingBag className="h-8 w-8 mx-auto mb-2 text-muted-foreground/50" />
            <p className="font-semibold text-foreground">No sales orders yet</p>
            <p className="text-xs mt-1">When buyers purchase your products, orders will appear here.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="text-xs text-muted-foreground bg-muted/50 uppercase border-b border-border">
                <tr>
                  <th className="px-4 py-3">Purchase Code</th>
                  <th className="px-4 py-3">Product</th>
                  <th className="px-4 py-3">Buyer</th>
                  <th className="px-4 py-3">License</th>
                  <th className="px-4 py-3">Earnings</th>
                  <th className="px-4 py-3 text-right">Date</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                {orderItems.map((item) => {
                  const buyerName =
                    item.user?.username ||
                    `${item.user?.firstname || ""} ${item.user?.lastname || ""}`.trim() ||
                    item.user?.email ||
                    "Buyer";

                  return (
                    <tr key={item.id} className="hover:bg-muted/30 transition-colors">
                      <td className="px-4 py-3 font-mono text-xs text-muted-foreground">
                        {item.purchaseCode || item.id.slice(0, 10)}
                      </td>
                      <td className="px-4 py-3">
                        <Link
                          href={`/game-source-code/${item.product.slug}`}
                          target="_blank"
                          className="font-medium text-foreground hover:text-primary transition-colors line-clamp-1 inline-flex items-center gap-1"
                        >
                          {item.product.title}
                          <ArrowUpRight className="h-3 w-3 shrink-0 text-muted-foreground" />
                        </Link>
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex items-center gap-1.5 text-xs text-muted-foreground">
                          <User className="h-3.5 w-3.5" />
                          <span>{buyerName}</span>
                        </div>
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex items-center gap-1.5">
                          <Badge variant="outline" className="text-xs">
                            {item.license === 2 ? "Commercial" : "Personal"}
                          </Badge>
                          {item.isRefunded === 1 && (
                            <Badge variant="destructive" className="text-[10px] bg-red-500/10 text-red-600 border-red-500/30">
                              Refunded
                            </Badge>
                          )}
                        </div>
                      </td>
                      <td className="px-4 py-3 font-semibold">
                        {item.isRefunded === 1 ? (
                          <div className="flex flex-col">
                            <span className="line-through text-muted-foreground text-xs font-normal">
                              {formatCurrency(item.sellerEarning || item.productPrice)}
                            </span>
                            <span className="text-red-500 text-xs font-medium">
                              $0.00 (Refunded)
                            </span>
                          </div>
                        ) : (
                          <span className="text-emerald-600 dark:text-emerald-400">
                            {formatCurrency(item.sellerEarning || item.productPrice)}
                          </span>
                        )}
                      </td>
                      <td className="px-4 py-3 text-xs text-muted-foreground text-right">
                        {new Date(item.createdAt).toLocaleDateString()}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Section 2: Financial Ledger Transactions */}
      <div className="border border-border rounded-xl bg-card overflow-hidden">
        <div className="p-4 border-b border-border bg-muted/30 flex items-center justify-between">
          <h2 className="font-semibold text-base flex items-center gap-2">
            <CreditCard className="h-4 w-4 text-emerald-500" />
            Financial Transactions Ledger
          </h2>
          <Badge variant="outline" className="text-xs">
            {transactions.length} Ledger Records
          </Badge>
        </div>

        {transactions.length === 0 ? (
          <div className="p-8 text-center text-muted-foreground">
            <CreditCard className="h-8 w-8 mx-auto mb-2 text-muted-foreground/50" />
            <p className="font-semibold text-foreground">No financial ledger entries yet</p>
            <p className="text-xs mt-1">Transaction entries for sale credits &amp; fee debits will appear here.</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="text-xs text-muted-foreground bg-muted/50 uppercase border-b border-border">
                <tr>
                  <th className="px-4 py-3">TRX</th>
                  <th className="px-4 py-3">Type</th>
                  <th className="px-4 py-3">Amount</th>
                  <th className="px-4 py-3">Post Balance</th>
                  <th className="px-4 py-3 text-right">Date</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                {transactions.map((trx) => {
                  const isCredit = trx.trxType === "+";
                  const remarkText = remarkLabels[trx.remark || ""] || trx.remark || "Transaction";

                  return (
                    <tr key={trx.id} className="hover:bg-muted/30 transition-colors">
                      <td className="px-4 py-3 font-mono text-xs text-muted-foreground">
                        {trx.trx || trx.id.slice(0, 10)}
                      </td>
                      <td className="px-4 py-3">
                        <div className="flex items-center gap-2">
                          <span
                            className={`w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold ${
                              isCredit
                                ? "bg-emerald-500/10 text-emerald-600 dark:text-emerald-400"
                                : "bg-rose-500/10 text-rose-600 dark:text-rose-400"
                            }`}
                          >
                            {isCredit ? (
                              <ArrowUpRight className="h-3 w-3" />
                            ) : (
                              <ArrowDownRight className="h-3 w-3" />
                            )}
                          </span>
                          <span className="font-medium text-foreground">{remarkText}</span>
                        </div>
                      </td>
                      <td className="px-4 py-3 font-semibold">
                        <span
                          className={
                            isCredit
                              ? "text-emerald-600 dark:text-emerald-400"
                              : "text-rose-600 dark:text-rose-400"
                          }
                        >
                          {isCredit ? "+" : "-"}
                          {formatCurrency(trx.amount)}
                        </span>
                      </td>
                      <td className="px-4 py-3 font-medium text-muted-foreground">
                        {formatCurrency(trx.postBalance)}
                      </td>
                      <td className="px-4 py-3 text-xs text-muted-foreground text-right">
                        {new Date(trx.createdAt).toLocaleDateString()}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
