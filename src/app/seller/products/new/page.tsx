"use client";

import { useState, FormEvent } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { useToast } from "@/hooks/use-toast";
import { ArrowLeft, Loader2, Save, AlertCircle } from "lucide-react";

export default function NewProductPage() {
  const router = useRouter();
  const { toast } = useToast();
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({
    title: "",
    description: "",
    price: "",
    priceCl: "",
    demoUrl: "",
    previewVideo: "",
    tags: "",
    metaTitle: "",
    metaDescription: "",
    reskinPrice: "120",
    publishPrice: "25",
    storeOptimizationPrice: "50",
  });

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setSaving(true);

    try {
      const res = await fetch("/api/products", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          ...form,
          price: parseFloat(form.price) || 0,
          priceCl: parseFloat(form.priceCl) || 0,
          reskinPrice: parseFloat(form.reskinPrice) || 0,
          publishPrice: parseFloat(form.publishPrice) || 0,
          storeOptimizationPrice: parseFloat(form.storeOptimizationPrice) || 0,
          tags: form.tags
            .split(",")
            .map((t) => t.trim())
            .filter(Boolean),
        }),
      });

      const data = await res.json();

      if (!res.ok) {
        toast({
          title: "Upload failed",
          description: data.error || "Validation failed",
          variant: "destructive",
        });
        return;
      }

      toast({
        title: "Product uploaded!",
        description: "Your product is now pending review. You'll be notified once approved.",
      });

      router.push("/seller/products");
      router.refresh();
    } catch {
      toast({
        title: "Network error",
        description: "Could not reach the server.",
        variant: "destructive",
      });
    } finally {
      setSaving(false);
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <Button variant="ghost" size="sm" asChild className="mb-2">
          <Link href="/seller/products">
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to Products
          </Link>
        </Button>
        <h1 className="text-2xl font-bold">Upload New Product</h1>
        <p className="text-sm text-muted-foreground mt-1">
          Fill in the details below. Your product will be reviewed before going live.
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Basic info */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Basic Information</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="title">Product Title *</Label>
              <Input
                id="title"
                required
                value={form.title}
                onChange={(e) => setForm({ ...form, title: e.target.value })}
                placeholder="e.g., Brain Teaser Screw Puzzle – Unity Game Source Code"
              />
              <p className="text-xs text-muted-foreground">
                Descriptive title with key keywords for SEO.
              </p>
            </div>

            <div className="space-y-2">
              <Label htmlFor="description">Description *</Label>
              <Textarea
                id="description"
                required
                rows={8}
                value={form.description}
                onChange={(e) => setForm({ ...form, description: e.target.value })}
                placeholder="<p>Describe your game, features, gameplay mechanics, and what makes it special...</p>"
              />
              <p className="text-xs text-muted-foreground">
                HTML allowed. Describe features, gameplay, and what buyers get.
              </p>
            </div>

            <div className="space-y-2">
              <Label htmlFor="tags">Tags (comma-separated)</Label>
              <Input
                id="tags"
                value={form.tags}
                onChange={(e) => setForm({ ...form, tags: e.target.value })}
                placeholder="unity puzzle game, casual game, hyper casual, admob"
              />
              <p className="text-xs text-muted-foreground">
                Helps buyers find your product via search.
              </p>
            </div>
          </CardContent>
        </Card>

        {/* Pricing */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Pricing</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="price">Personal License Price ($) *</Label>
                <Input
                  id="price"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  value={form.price}
                  onChange={(e) => setForm({ ...form, price: e.target.value })}
                  placeholder="15.00"
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="priceCl">Commercial License Price ($) *</Label>
                <Input
                  id="priceCl"
                  type="number"
                  step="0.01"
                  min="0"
                  required
                  value={form.priceCl}
                  onChange={(e) => setForm({ ...form, priceCl: e.target.value })}
                  placeholder="150.00"
                />
              </div>
            </div>

            <Separator />

            <div>
              <Label className="text-sm font-medium">Additional Service Prices</Label>
              <p className="text-xs text-muted-foreground mb-3">
                Optional services buyers can add to their purchase.
              </p>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="reskinPrice" className="text-xs">Reskin ($)</Label>
                  <Input
                    id="reskinPrice"
                    type="number"
                    step="0.01"
                    min="0"
                    value={form.reskinPrice}
                    onChange={(e) => setForm({ ...form, reskinPrice: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="publishPrice" className="text-xs">Publish ($)</Label>
                  <Input
                    id="publishPrice"
                    type="number"
                    step="0.01"
                    min="0"
                    value={form.publishPrice}
                    onChange={(e) => setForm({ ...form, publishPrice: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="storeOptimizationPrice" className="text-xs">Store Opt ($)</Label>
                  <Input
                    id="storeOptimizationPrice"
                    type="number"
                    step="0.01"
                    min="0"
                    value={form.storeOptimizationPrice}
                    onChange={(e) => setForm({ ...form, storeOptimizationPrice: e.target.value })}
                  />
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Demo + Media */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Demo & Media</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="demoUrl">Demo URL *</Label>
              <Input
                id="demoUrl"
                type="url"
                required
                value={form.demoUrl}
                onChange={(e) => setForm({ ...form, demoUrl: e.target.value })}
                placeholder="https://your-demo-url.com"
              />
              <p className="text-xs text-muted-foreground">
                Link to a playable demo or APK download.
              </p>
            </div>

            <div className="space-y-2">
              <Label htmlFor="previewVideo">Preview Video URL (YouTube)</Label>
              <Input
                id="previewVideo"
                type="url"
                value={form.previewVideo}
                onChange={(e) => setForm({ ...form, previewVideo: e.target.value })}
                placeholder="https://www.youtube.com/watch?v=..."
              />
              <p className="text-xs text-muted-foreground">
                Gameplay trailer or demo video on YouTube.
              </p>
            </div>

            <div className="p-3 rounded-md bg-accent/50 text-xs text-muted-foreground flex items-start gap-2">
              <AlertCircle className="h-4 w-4 text-primary shrink-0 mt-0.5" />
              <div>
                <strong className="text-foreground">Note:</strong> Thumbnail, preview
                image, screenshots, and main file upload will be available after the
                initial product creation. The reviewer will request these during the
                review process.
              </div>
            </div>
          </CardContent>
        </Card>

        {/* SEO */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">SEO (Optional)</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="metaTitle">Meta Title</Label>
              <Input
                id="metaTitle"
                value={form.metaTitle}
                onChange={(e) => setForm({ ...form, metaTitle: e.target.value })}
                placeholder="Custom title for search engines (defaults to product title)"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="metaDescription">Meta Description</Label>
              <Textarea
                id="metaDescription"
                rows={3}
                value={form.metaDescription}
                onChange={(e) => setForm({ ...form, metaDescription: e.target.value })}
                placeholder="Short description for search engines (max 155 chars recommended)"
              />
            </div>
          </CardContent>
        </Card>

        {/* Submit */}
        <div className="flex justify-end gap-3">
          <Button variant="outline" type="button" asChild>
            <Link href="/seller/products">Cancel</Link>
          </Button>
          <Button type="submit" disabled={saving}>
            {saving ? (
              <>
                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                Uploading...
              </>
            ) : (
              <>
                <Save className="mr-2 h-4 w-4" />
                Submit for Review
              </>
            )}
          </Button>
        </div>
      </form>
    </div>
  );
}
