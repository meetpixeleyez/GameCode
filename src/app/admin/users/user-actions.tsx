"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { useToast } from "@/hooks/use-toast";
import { Loader2, Ban, CheckCircle } from "lucide-react";

export function UserActions({ userId, currentStatus }: { userId: string; currentStatus: number }) {
  const router = useRouter();
  const { toast } = useToast();
  const [loading, setLoading] = useState(false);

  // status 1 = Active, status 0 = Banned
  async function updateStatus(status: number, actionName: string) {
    setLoading(true);
    try {
      const res = await fetch(`/api/admin/users/${userId}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status }),
      });

      if (!res.ok) {
        throw new Error("Failed to update");
      }

      toast({
        title: "Success",
        description: `User has been ${actionName.toLowerCase()}.`,
      });
      router.refresh();
    } catch (error) {
      toast({
        title: "Error",
        description: `Could not ${actionName.toLowerCase()} user.`,
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="flex gap-2">
      {currentStatus === 1 ? (
        <Button 
          size="sm" 
          variant="destructive" 
          onClick={() => updateStatus(0, "Banned")}
          disabled={loading}
        >
          {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Ban className="h-4 w-4 mr-1" />}
          Ban User
        </Button>
      ) : (
        <Button 
          size="sm" 
          variant="outline" 
          onClick={() => updateStatus(1, "Unbanned")}
          disabled={loading}
        >
          {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : <CheckCircle className="h-4 w-4 mr-1 text-green-500" />}
          Unban User
        </Button>
      )}
    </div>
  );
}
