"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { useToast } from "@/hooks/use-toast";
import { Loader2, Check, X } from "lucide-react";

export function ProductActions({ productId, currentStatus }: { productId: string; currentStatus: number }) {
  const router = useRouter();
  const { toast } = useToast();
  const [loading, setLoading] = useState(false);

  async function updateStatus(status: number, actionName: string) {
    setLoading(true);
    try {
      const res = await fetch(`/api/admin/products/${productId}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status }),
      });

      if (!res.ok) {
        throw new Error("Failed to update");
      }

      toast({
        title: "Success",
        description: `Product has been ${actionName.toLowerCase()}.`,
      });
      router.refresh();
    } catch (error) {
      toast({
        title: "Error",
        description: `Could not ${actionName.toLowerCase()} product.`,
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="flex gap-2">
      {currentStatus === 0 && (
        <>
          <Button 
            size="sm" 
            variant="default" 
            onClick={() => updateStatus(1, "Approved")}
            disabled={loading}
          >
            {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Check className="h-4 w-4 mr-1" />}
            Approve
          </Button>
          <Button 
            size="sm" 
            variant="destructive" 
            onClick={() => updateStatus(2, "Soft Rejected")}
            disabled={loading}
          >
            {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : <X className="h-4 w-4 mr-1" />}
            Reject
          </Button>
        </>
      )}
      {currentStatus === 1 && (
        <Button 
          size="sm" 
          variant="outline" 
          onClick={() => updateStatus(4, "Taken Down")}
          disabled={loading}
        >
          {loading ? <Loader2 className="h-4 w-4 animate-spin" /> : "Take Down"}
        </Button>
      )}
    </div>
  );
}
