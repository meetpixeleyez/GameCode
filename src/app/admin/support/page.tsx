"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Loader2, MessageSquare } from "lucide-react";

export default function AdminSupportPage() {
  const [tickets, setTickets] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchTickets();
  }, []);

  async function fetchTickets() {
    try {
      const res = await fetch("/api/admin/support");
      const data = await res.json();
      if (res.ok) {
        setTickets(data);
      }
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  const getStatusBadge = (status: number) => {
    switch (status) {
      case 0: return <Badge variant="default" className="bg-blue-500">Open</Badge>;
      case 1: return <Badge variant="secondary" className="bg-emerald-500 text-white">Answered</Badge>;
      case 2: return <Badge variant="outline" className="text-orange-500 border-orange-500">Customer Reply</Badge>;
      case 3: return <Badge variant="secondary" className="bg-gray-500 text-white">Closed</Badge>;
      default: return <Badge>Unknown</Badge>;
    }
  };

  const getPriorityBadge = (priority: number) => {
    switch (priority) {
      case 1: return <Badge variant="outline" className="text-emerald-500 border-emerald-500">Low</Badge>;
      case 2: return <Badge variant="outline" className="text-blue-500 border-blue-500">Medium</Badge>;
      case 3: return <Badge variant="outline" className="text-red-500 border-red-500">High</Badge>;
      default: return null;
    }
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Helpdesk</h1>
        <p className="text-muted-foreground mt-2">Manage customer support tickets.</p>
      </div>

      <div className="border rounded-md bg-card">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Ticket ID</TableHead>
              <TableHead>User / Email</TableHead>
              <TableHead>Subject</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Priority</TableHead>
              <TableHead>Last Updated</TableHead>
              <TableHead className="text-right">Action</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {loading ? (
              <TableRow>
                <TableCell colSpan={7} className="h-24 text-center">
                  <Loader2 className="h-6 w-6 animate-spin mx-auto text-muted-foreground" />
                </TableCell>
              </TableRow>
            ) : tickets.length === 0 ? (
              <TableRow>
                <TableCell colSpan={7} className="h-24 text-center text-muted-foreground">
                  No support tickets found.
                </TableCell>
              </TableRow>
            ) : (
              tickets.map((ticket) => (
                <TableRow key={ticket.id}>
                  <TableCell className="font-mono text-xs">#{ticket.ticket}</TableCell>
                  <TableCell>
                    <div className="font-medium">{ticket.name}</div>
                    <div className="text-xs text-muted-foreground">{ticket.email}</div>
                  </TableCell>
                  <TableCell className="font-medium max-w-[200px] truncate">{ticket.subject}</TableCell>
                  <TableCell>{getStatusBadge(ticket.status)}</TableCell>
                  <TableCell>{getPriorityBadge(ticket.priority)}</TableCell>
                  <TableCell>{new Date(ticket.updatedAt).toLocaleDateString()}</TableCell>
                  <TableCell className="text-right">
                    <Link href={`/admin/support/${ticket.id}`}>
                      <Button variant="ghost" size="sm">
                        <MessageSquare className="h-4 w-4 mr-2" /> View
                      </Button>
                    </Link>
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
