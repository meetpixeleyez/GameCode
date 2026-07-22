"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Loader2, Eye, Search, Filter, ShieldCheck } from "lucide-react";

export default function AdminKYCPage() {
  const [users, setUsers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    fetchUsers();
  }, []);

  async function fetchUsers() {
    try {
      const res = await fetch("/api/admin/kyc");
      const data = await res.json();
      if (res.ok) {
        setUsers(data);
      }
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  const getStatusBadge = (kv: number) => {
    switch (kv) {
      case 0: return <Badge variant="secondary" className="bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">Unverified</Badge>;
      case 1: return <Badge variant="secondary" className="bg-emerald-500/15 text-emerald-600 hover:bg-emerald-500/25">Verified</Badge>;
      case 2: return <Badge variant="secondary" className="bg-amber-500/15 text-amber-600 hover:bg-amber-500/25">Pending</Badge>;
      default: return <Badge variant="outline">Unknown</Badge>;
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">KYC Requests</h1>
          <p className="text-muted-foreground mt-2">Review and verify author verification requests.</p>
        </div>
      </div>

      <Card>
        <CardHeader className="pb-4">
          <div className="flex flex-col sm:flex-row justify-between gap-4">
            <div>
              <CardTitle>Verification Queue</CardTitle>
              <CardDescription>Manage incoming KYC documents and approvals.</CardDescription>
            </div>
            <div className="flex items-center gap-2">
              <div className="relative w-full sm:w-64">
                <Search className="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                  type="search"
                  placeholder="Search authors..."
                  className="pl-8 bg-background"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                />
              </div>
              <Button variant="outline" size="icon">
                <Filter className="h-4 w-4" />
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow className="bg-muted/50">
                  <TableHead className="w-[120px]">User ID</TableHead>
                  <TableHead>User</TableHead>
                  <TableHead>Email</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead>Joined</TableHead>
                  <TableHead className="text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {loading ? (
                  <TableRow>
                    <TableCell colSpan={6} className="h-32 text-center">
                      <Loader2 className="h-6 w-6 animate-spin mx-auto text-muted-foreground" />
                    </TableCell>
                  </TableRow>
                ) : users.length === 0 ? (
                  <TableRow>
                    <TableCell colSpan={6} className="h-48 text-center">
                      <div className="flex flex-col items-center justify-center text-muted-foreground">
                        <ShieldCheck className="h-12 w-12 mb-4 text-muted-foreground/30" />
                        <h3 className="text-lg font-medium text-foreground">No KYC requests</h3>
                        <p className="text-sm mt-1">There are no pending author verifications at the moment.</p>
                      </div>
                    </TableCell>
                  </TableRow>
                ) : (
                  users.filter(user => {
                    const searchLower = searchQuery.toLowerCase();
                    const name = (user.username || user.firstname || "").toLowerCase();
                    const email = (user.email || "").toLowerCase();
                    const id = (user.id || "").toLowerCase();
                    return name.includes(searchLower) || email.includes(searchLower) || id.includes(searchLower);
                  }).map((user) => (
                    <TableRow key={user.id} className="group">
                      <TableCell className="font-mono text-xs text-muted-foreground truncate max-w-[120px]">{user.id}</TableCell>
                      <TableCell className="font-medium">{user.username || user.firstname}</TableCell>
                      <TableCell className="text-muted-foreground text-sm">{user.email}</TableCell>
                      <TableCell>{getStatusBadge(user.kv)}</TableCell>
                      <TableCell className="text-sm text-muted-foreground">{new Date(user.createdAt).toLocaleDateString()}</TableCell>
                      <TableCell className="text-right">
                        <Link href={`/admin/kyc/${user.id}`}>
                          <Button variant="ghost" size="sm" className="opacity-0 group-hover:opacity-100 transition-opacity">
                            <Eye className="h-4 w-4 mr-2" /> Review
                          </Button>
                        </Link>
                      </TableCell>
                    </TableRow>
                  ))
                )}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>
    </div>
  );
}
