"use client";

import { useState, useEffect } from "react";
import { useParams, useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { useToast } from "@/hooks/use-toast";
import { Loader2, ArrowLeft, CheckCircle, XCircle } from "lucide-react";
import { Textarea } from "@/components/ui/textarea";
import { Label } from "@/components/ui/label";
import Link from "next/link";
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert";

export default function AdminKYCDetailPage() {
  const params = useParams();
  const userId = params.userId as string;
  const router = useRouter();
  const { toast } = useToast();
  
  const [user, setUser] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isProcessing, setIsProcessing] = useState(false);
  const [rejectionReason, setRejectionReason] = useState("");

  useEffect(() => {
    fetchUser();
  }, [userId]);

  async function fetchUser() {
    try {
      const res = await fetch(`/api/admin/kyc/${userId}`);
      if (!res.ok) {
        if (res.status === 404) router.push("/admin/kyc");
        throw new Error("Failed to load user");
      }
      const data = await res.json();
      // Parse JSON kycData if string
      if (data.kycData && typeof data.kycData === "string") {
        try {
          data.kycData = JSON.parse(data.kycData);
        } catch(e) {}
      }
      setUser(data);
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  async function handleAction(action: "approve" | "reject") {
    if (action === "reject" && !rejectionReason.trim()) {
      toast({ title: "Error", description: "Rejection reason is required", variant: "destructive" });
      return;
    }
    
    setIsProcessing(true);
    try {
      const res = await fetch(`/api/admin/kyc/${userId}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ action, reason: action === "reject" ? rejectionReason : null }),
      });

      if (!res.ok) {
        const d = await res.json();
        throw new Error(d.error || "Failed to process KYC");
      }
      
      toast({ title: action === "approve" ? "KYC Approved" : "KYC Rejected" });
      fetchUser();
      setRejectionReason("");
    } catch (error: any) {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    } finally {
      setIsProcessing(false);
    }
  }

  if (loading) {
    return <div className="flex justify-center p-12"><Loader2 className="h-8 w-8 animate-spin text-primary" /></div>;
  }

  if (!user) return null;

  return (
    <div className="max-w-4xl space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/admin/kyc">
          <Button variant="outline" size="icon">
            <ArrowLeft className="h-4 w-4" />
          </Button>
        </Link>
        <div>
          <h1 className="text-2xl font-bold flex items-center gap-3">
            KYC Application: {user.username || user.firstname}
            {user.kv === 1 && <Badge className="bg-emerald-500">Verified</Badge>}
            {user.kv === 2 && <Badge className="bg-blue-500">Pending</Badge>}
            {user.kv === 0 && <Badge variant="secondary">Unverified/Rejected</Badge>}
          </h1>
          <p className="text-muted-foreground">{user.email}</p>
        </div>
      </div>

      {user.kv === 0 && user.kycRejectionReason && (
        <Alert variant="destructive">
          <AlertTitle>Previous Rejection Reason</AlertTitle>
          <AlertDescription>{user.kycRejectionReason}</AlertDescription>
        </Alert>
      )}

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="bg-card border rounded-lg p-6 space-y-4">
          <h3 className="font-semibold text-lg border-b pb-2">Submitted Details</h3>
          {!user.kycData ? (
            <p className="text-muted-foreground">No KYC data submitted.</p>
          ) : (
            <dl className="space-y-4">
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Full Name</dt>
                <dd className="font-medium mt-1">{user.kycData.fullName || "N/A"}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Document Type</dt>
                <dd className="font-medium mt-1 capitalize">{(user.kycData.documentType || "N/A").replace("_", " ")}</dd>
              </div>
              <div>
                <dt className="text-sm font-medium text-muted-foreground">Residential Address</dt>
                <dd className="font-medium mt-1 whitespace-pre-wrap">{user.kycData.address || "N/A"}</dd>
              </div>
            </dl>
          )}
        </div>

        <div className="bg-card border rounded-lg p-6 space-y-4">
           <h3 className="font-semibold text-lg border-b pb-2">Document Image</h3>
           {user.kycData?.documentUrl ? (
             <div className="space-y-2">
               <a href={user.kycData.documentUrl} target="_blank" rel="noopener noreferrer" className="text-blue-500 hover:underline text-sm break-all">
                 {user.kycData.documentUrl}
               </a>
               <div className="aspect-video bg-muted rounded-md flex items-center justify-center overflow-hidden border">
                 {/* In a real app we might use next/image, but external random URLs require config or img tag */}
                 <img src={user.kycData.documentUrl} alt="Document" className="w-full h-full object-cover" />
               </div>
             </div>
           ) : (
             <p className="text-muted-foreground">No document image provided.</p>
           )}
        </div>
      </div>

      {user.kv === 2 && (
        <div className="bg-card border rounded-lg p-6 space-y-6">
          <h3 className="font-semibold text-lg border-b pb-2">Admin Decision</h3>
          
          <div className="space-y-4">
            <div className="space-y-2">
              <Label>Rejection Reason (only required if rejecting)</Label>
              <Textarea 
                placeholder="e.g. Image is blurry, name does not match..."
                value={rejectionReason}
                onChange={(e) => setRejectionReason(e.target.value)}
              />
            </div>

            <div className="flex gap-4">
              <Button 
                onClick={() => handleAction("approve")} 
                disabled={isProcessing}
                className="bg-emerald-600 hover:bg-emerald-700"
              >
                <CheckCircle className="mr-2 h-4 w-4" /> Approve User
              </Button>
              <Button 
                onClick={() => handleAction("reject")}
                disabled={isProcessing}
                variant="destructive"
              >
                <XCircle className="mr-2 h-4 w-4" /> Reject User
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
