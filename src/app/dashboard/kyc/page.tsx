"use client";

import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Loader2, CheckCircle, Clock, AlertCircle } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

export default function KYCPage() {
  const [status, setStatus] = useState<number | null>(null);
  const [rejectionReason, setRejectionReason] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const { toast } = useToast();

  const [formData, setFormData] = useState({
    fullName: "",
    documentType: "passport",
    documentUrl: "",
  });

  useEffect(() => {
    fetchStatus();
  }, []);

  async function fetchStatus() {
    try {
      const res = await fetch("/api/auth/me"); // Assuming this returns current user details including kv
      const data = await res.json();
      if (res.ok && data.user) {
        setStatus(data.user.kv);
        setRejectionReason(data.user.kycRejectionReason);
      }
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setIsSubmitting(true);
    try {
      const res = await fetch("/api/kyc", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (res.ok) {
        toast({ title: "KYC submitted successfully!" });
        setStatus(2); // Pending
      } else {
        throw new Error(data.error || "Failed to submit");
      }
    } catch (error: any) {
      toast({ title: "Error", description: error.message, variant: "destructive" });
    } finally {
      setIsSubmitting(false);
    }
  }

  if (loading) {
    return <div className="flex justify-center p-12"><Loader2 className="h-8 w-8 animate-spin text-primary" /></div>;
  }

  return (
    <div className="max-w-2xl space-y-6">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Identity Verification (KYC)</h1>
        <p className="text-muted-foreground mt-2">
          Verify your identity to unlock seller features and withdrawals.
        </p>
      </div>

      {status === 1 && (
        <Alert className="bg-emerald-50 border-emerald-200">
          <CheckCircle className="h-5 w-5 text-emerald-600" />
          <AlertTitle className="text-emerald-800">Verified</AlertTitle>
          <AlertDescription className="text-emerald-700">
            Your identity has been successfully verified. You now have full access to all seller features!
          </AlertDescription>
        </Alert>
      )}

      {status === 2 && (
        <Alert className="bg-blue-50 border-blue-200">
          <Clock className="h-5 w-5 text-blue-600" />
          <AlertTitle className="text-blue-800">Pending Review</AlertTitle>
          <AlertDescription className="text-blue-700">
            Your KYC application is currently being reviewed by our team. Please check back later.
          </AlertDescription>
        </Alert>
      )}

      {status === 0 && (
        <div className="space-y-6">
          {rejectionReason && (
            <Alert variant="destructive">
              <AlertCircle className="h-5 w-5" />
              <AlertTitle>Application Rejected</AlertTitle>
              <AlertDescription>
                <strong>Reason:</strong> {rejectionReason}
                <br />
                Please correct the issues and resubmit your application below.
              </AlertDescription>
            </Alert>
          )}

          <form onSubmit={handleSubmit} className="space-y-6 bg-card p-6 border rounded-lg">
            <div className="space-y-2">
              <Label>Full Legal Name</Label>
              <Input 
                value={formData.fullName} 
                onChange={(e) => setFormData({ ...formData, fullName: e.target.value })} 
                required 
                placeholder="John Doe"
              />
            </div>

            <div className="space-y-2">
              <Label>Document Type</Label>
              <select 
                value={formData.documentType}
                onChange={(e) => setFormData({ ...formData, documentType: e.target.value })}
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <option value="passport">Passport</option>
                <option value="national_id">National ID Card</option>
                <option value="driver_license">Driver's License</option>
              </select>
            </div>

            <div className="space-y-2">
              <Label>Document Image URL</Label>
              <Input 
                value={formData.documentUrl} 
                onChange={(e) => setFormData({ ...formData, documentUrl: e.target.value })} 
                required 
                placeholder="https://example.com/my-id.jpg"
              />
              <p className="text-xs text-muted-foreground">Please provide a direct link to a clear image of your document.</p>
            </div>

            <Button type="submit" className="w-full" disabled={isSubmitting}>
              {isSubmitting ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : null}
              Submit KYC Application
            </Button>
          </form>
        </div>
      )}
    </div>
  );
}
