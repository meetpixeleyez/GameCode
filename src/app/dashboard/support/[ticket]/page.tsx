"use client";

import { useState, useEffect } from "react";
import { useParams, useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { useToast } from "@/hooks/use-toast";
import { Loader2, ArrowLeft, Send } from "lucide-react";
import Link from "next/link";

export default function TicketDetailPage() {
  const params = useParams();
  const ticketId = params.ticket as string;
  const router = useRouter();
  const { toast } = useToast();
  
  const [ticket, setTicket] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [replyMessage, setReplyMessage] = useState("");
  const [isReplying, setIsReplying] = useState(false);

  useEffect(() => {
    fetchTicket();
  }, [ticketId]);

  async function fetchTicket() {
    try {
      const res = await fetch(`/api/support/${ticketId}`);
      if (!res.ok) {
        if (res.status === 404) router.push("/dashboard/support");
        throw new Error("Failed to load ticket");
      }
      const data = await res.json();
      setTicket(data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  async function handleReply(e: React.FormEvent) {
    e.preventDefault();
    if (!replyMessage.trim()) return;

    setIsReplying(true);
    try {
      const res = await fetch(`/api/support/${ticketId}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: replyMessage }),
      });

      if (!res.ok) throw new Error("Failed to send reply");
      
      toast({ title: "Reply sent!" });
      setReplyMessage("");
      fetchTicket();
    } catch (error: any) {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    } finally {
      setIsReplying(false);
    }
  }

  if (loading) {
    return <div className="flex justify-center p-12"><Loader2 className="h-8 w-8 animate-spin text-primary" /></div>;
  }

  if (!ticket) return null;

  const isClosed = ticket.status === 3;

  return (
    <div className="max-w-3xl space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/dashboard/support">
          <Button variant="outline" size="icon">
            <ArrowLeft className="h-4 w-4" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold flex items-center gap-3">
            Ticket #{ticket.ticket}
            {isClosed ? <Badge variant="secondary">Closed</Badge> : <Badge className="bg-blue-500">Open</Badge>}
          </h1>
          <p className="text-muted-foreground">{ticket.subject}</p>
        </div>
      </div>

      <div className="space-y-4">
        {ticket.messages.map((msg: any, idx: number) => {
          // Simplistic logic: first message is from user. Assuming further ones alternate or based on admin flag.
          // Since we don't have an `isAdmin` flag on SupportMessage in the schema, we'll assume alternating or we can't tell easily without an authorId.
          // Wait, SupportMessage doesn't have an author! The schema says: supportTicketId, message, createdAt.
          // So we'll just show them in a list. Usually user creates it, then admin replies.
          // We will render it as a simple timeline for now.
          return (
            <div key={msg.id} className="p-4 border rounded-lg bg-card">
              <div className="text-xs text-muted-foreground mb-2">
                {new Date(msg.createdAt).toLocaleString()}
              </div>
              <div className="whitespace-pre-wrap">{msg.message}</div>
            </div>
          );
        })}
      </div>

      {!isClosed && (
        <form onSubmit={handleReply} className="pt-6 border-t mt-6">
          <h3 className="font-semibold mb-2">Send a Reply</h3>
          <Textarea 
            value={replyMessage}
            onChange={(e) => setReplyMessage(e.target.value)}
            placeholder="Type your reply here..."
            className="mb-4 min-h-[100px]"
            required
          />
          <Button type="submit" disabled={isReplying}>
            {isReplying ? <Loader2 className="h-4 w-4 animate-spin mr-2" /> : <Send className="h-4 w-4 mr-2" />}
            Send Reply
          </Button>
        </form>
      )}
    </div>
  );
}
