"use client";

import { useState, FormEvent } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/hooks/use-toast";
import { ArrowLeft, Loader2, Save } from "lucide-react";

interface ProductEditFormProps {
  initialData: {
    id: string;
    title: string;
    description: string;
    price: string;
    priceCl: string;
    demoUrl: string;
    previewVideo: string;
    thumbnail: string;
    tags: string;
    metaTitle: string;
    metaDescription: string;
    reskinPrice: string;
    publishPrice: string;
    storeOptimizationPrice: string;
  };
  isAdmin: boolean;
}

export default function ProductEditForm({ initialData, isAdmin }: ProductEditFormProps) {
  const router = useRouter();
  const { toast } = useToast();
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState(initialData);

  const backLink = isAdmin ? "/admin/products" : "/seller/products";
  const apiEndpoint = isAdmin ? `/api/admin/products/${initialData.id}` : `/api/products/${initialData.id}`;

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setSaving(true);

    try {
      const res = await fetch(apiEndpoint, {
        method: "PUT",
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
        let errorDesc = data.error || "Validation failed";
        if (data.details) {
          errorDesc = Object.entries(data.details)
            .map(([field, msgs]) => `${field}: ${(msgs as string[]).join(", ")}`)
            .join("\n");
        }

        toast({
          title: "Update failed",
          description: errorDesc,
          variant: "destructive",
        });
        return;
      }

      toast({
        title: "Product updated!",
        description: "Your product changes have been saved successfully.",
      });

      router.push(backLink);
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
          <Link href={backLink}>
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to Products
          </Link>
        </Button>
        <h1 className="text-2xl font-bold">Edit Product</h1>
        <p className="text-sm text-muted-foreground mt-1">
          Update the details below.
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
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
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="description">Description *</Label>
              <Textarea
                id="description"
                required
                rows={8}
                value={form.description}
                onChange={(e) => setForm({ ...form, description: e.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tags">Tags (comma-separated)</Label>
              <Input
                id="tags"
                value={form.tags}
                onChange={(e) => setForm({ ...form, tags: e.target.value })}
              />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Pricing ($)</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="price">Regular License Price *</Label>
                <Input
                  id="price"
                  type="number"
                  step="0.01"
                  required
                  value={form.price}
                  onChange={(e) => setForm({ ...form, price: e.target.value })}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="priceCl">Extended License Price *</Label>
                <Input
                  id="priceCl"
                  type="number"
                  step="0.01"
                  required
                  value={form.priceCl}
                  onChange={(e) => setForm({ ...form, priceCl: e.target.value })}
                />
              </div>
            </div>
            
            <div className="pt-4 space-y-4 border-t mt-4">
              <h4 className="font-medium">Services & Add-ons</h4>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="space-y-2">
                  <Label htmlFor="reskinPrice">Reskin Price</Label>
                  <Input
                    id="reskinPrice"
                    type="number"
                    step="0.01"
                    value={form.reskinPrice}
                    onChange={(e) => setForm({ ...form, reskinPrice: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="publishPrice">Publish Price</Label>
                  <Input
                    id="publishPrice"
                    type="number"
                    step="0.01"
                    value={form.publishPrice}
                    onChange={(e) => setForm({ ...form, publishPrice: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="storeOptimizationPrice">ASO Price</Label>
                  <Input
                    id="storeOptimizationPrice"
                    type="number"
                    step="0.01"
                    value={form.storeOptimizationPrice}
                    onChange={(e) => setForm({ ...form, storeOptimizationPrice: e.target.value })}
                  />
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Media & URLs</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="demoUrl">Demo URL * (APK/Play Store)</Label>
              <Input
                id="demoUrl"
                type="url"
                required
                value={form.demoUrl}
                onChange={(e) => setForm({ ...form, demoUrl: e.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="thumbnail">Thumbnail Image URL</Label>
              <Input
                id="thumbnail"
                type="url"
                value={form.thumbnail}
                onChange={(e) => setForm({ ...form, thumbnail: e.target.value })}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="previewVideo">Preview Video URL</Label>
              <Input
                id="previewVideo"
                type="url"
                value={form.previewVideo}
                onChange={(e) => setForm({ ...form, previewVideo: e.target.value })}
              />
            </div>
          </CardContent>
        </Card>

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
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="metaDescription">Meta Description</Label>
              <Textarea
                id="metaDescription"
                rows={3}
                value={form.metaDescription}
                onChange={(e) => setForm({ ...form, metaDescription: e.target.value })}
              />
            </div>
          </CardContent>
        </Card>

        <div className="flex justify-end gap-4">
          <Button type="button" variant="outline" asChild>
            <Link href={backLink}>Cancel</Link>
          </Button>
          <Button type="submit" disabled={saving}>
            {saving ? <Loader2 className="h-4 w-4 mr-2 animate-spin" /> : <Save className="h-4 w-4 mr-2" />}
            Save Changes
          </Button>
        </div>
      </form>
    </div>
  );
}
