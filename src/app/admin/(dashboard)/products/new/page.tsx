"use client";

import { useState, useEffect, FormEvent } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { TagInput } from "@/components/ui/tag-input";
import { RichTextEditor } from "@/components/ui/rich-text-editor";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";
import { SearchableSelect } from "@/components/ui/searchable-select";
import { useToast } from "@/hooks/use-toast";
import { ArrowLeft, Loader2, Save, Upload, X } from "lucide-react";

export default function AdminNewProductPage() {
  const router = useRouter();
  const { toast } = useToast();
  const [saving, setSaving] = useState(false);
  const [categories, setCategories] = useState<any[]>([]);
  const [form, setForm] = useState({
    title: "",
    categoryId: "",
    subCategoryId: "",
    description: "",
    price: "",
    priceCl: "",
    demoUrl: "",
    previewVideo: "",
    tags: [] as string[],
    metaTitle: "",
    metaDescription: "",
    reskinPrice: "120",
    publishPrice: "25",
    storeOptimizationPrice: "50",
  });
  
  // File states
  const [thumbnailFile, setThumbnailFile] = useState<File | null>(null);
  const [mainFile, setMainFile] = useState<File | null>(null);
  const [screenshotsFiles, setScreenshotsFiles] = useState<File[]>([]);
  
  // Track uploaded URLs to prevent duplicate uploads on validation failure
  const [uploadedUrls, setUploadedUrls] = useState<any>({
    thumbnail: "",
    file: "",
    inlinePreviewImage: [] as string[]
  });

  useEffect(() => {
    fetch("/api/categories")
      .then((res) => res.json())
      .then((data) => {
        if (data.success) setCategories(data.categories);
      })
      .catch((err) => console.error("Failed to load categories", err));
  }, []);

  const activeCategory = categories.find((c) => c.id === form.categoryId);
  const subCategories = activeCategory?.subCategories || [];
  const activeSubCategory = subCategories.find((sc: any) => sc.id === form.subCategoryId);

  async function uploadFiles() {
    const formData = new FormData();
    if (thumbnailFile) formData.append("thumbnail", thumbnailFile);
    if (mainFile) formData.append("file", mainFile); // Admins upload directly to active file
    
    if (screenshotsFiles.length > 0) {
      for (let i = 0; i < screenshotsFiles.length; i++) {
        formData.append("inlinePreviewImage", screenshotsFiles[i]);
      }
    }

    if (Array.from(formData.keys()).length === 0) return {};

    const res = await fetch("/api/upload", {
      method: "POST",
      body: formData,
    });
    
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      throw new Error(data.error || `File upload failed (${res.status})`);
    }
    
    return data.files;
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    if (saving) return;
    if (!form.categoryId || !form.subCategoryId) {
      toast({ title: "Error", description: "Please select a Category and Subcategory.", variant: "destructive" });
      return;
    }
    if ((!thumbnailFile && !uploadedUrls.thumbnail) || (!mainFile && !uploadedUrls.file)) {
      toast({ title: "Error", description: "Thumbnail and Main File are required.", variant: "destructive" });
      return;
    }

    setSaving(true);
    try {
      let finalUrls = { ...uploadedUrls };
      
      if (thumbnailFile || mainFile || screenshotsFiles.length > 0) {
        toast({ title: "Uploading files...", description: "Please wait while we upload your files." });
        const newUploadedFiles = await uploadFiles();
        
        if (newUploadedFiles.thumbnail) finalUrls.thumbnail = newUploadedFiles.thumbnail;
        if (newUploadedFiles.file) finalUrls.file = newUploadedFiles.file;
        if (newUploadedFiles.inlinePreviewImage) {
           const newScreenshots = Array.isArray(newUploadedFiles.inlinePreviewImage) 
              ? newUploadedFiles.inlinePreviewImage 
              : [newUploadedFiles.inlinePreviewImage];
           finalUrls.inlinePreviewImage = [...finalUrls.inlinePreviewImage, ...newScreenshots];
        }
        
        setUploadedUrls(finalUrls);
        setThumbnailFile(null);
        setMainFile(null);
        setScreenshotsFiles([]);
      }

      const res = await fetch("/api/admin/products", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          ...form,
          price: parseFloat(form.price) || 0,
          priceCl: parseFloat(form.priceCl) || 0,
          reskinPrice: parseFloat(form.reskinPrice) || 0,
          publishPrice: parseFloat(form.publishPrice) || 0,
          storeOptimizationPrice: parseFloat(form.storeOptimizationPrice) || 0,
          tags: form.tags,
          thumbnail: finalUrls.thumbnail,
          file: finalUrls.file,
          inlinePreviewImage: JSON.stringify(finalUrls.inlinePreviewImage),
        }),
      });

      const data = await res.json();
      if (!res.ok) {
        let errorDesc = data.error || "Validation failed";
        if (data.details) {
          errorDesc = Object.entries(data.details).map(([field, msgs]) => `${field}: ${(msgs as string[]).join(", ")}`).join("\n");
        }
        throw new Error(errorDesc);
      }

      toast({
        title: "Product published!",
        description: "Your product is now live on the marketplace.",
      });

      router.push("/admin/products");
      router.refresh();
    } catch (err: any) {
      toast({
        title: "Upload failed",
        description: err.message || "Could not reach the server.",
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
          <Link href="/admin/products">
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to Products
          </Link>
        </Button>
        <h1 className="text-2xl font-bold">Publish New Product</h1>
        <p className="text-sm text-muted-foreground mt-1">
          As an Admin, your product will bypass the review queue and go live immediately.
        </p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        {/* Category */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Select Category</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Label htmlFor="category">Category *</Label>
                <SearchableSelect
                  id="category"
                  options={categories.map((c) => ({ value: c.id, label: c.name }))}
                  value={form.categoryId}
                  onValueChange={(val) => setForm({ ...form, categoryId: val, subCategoryId: "" })}
                  placeholder="Select Category"
                  searchPlaceholder="Search category..."
                  emptyText="No categories found."
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="subCategory">Subcategory *</Label>
                <SearchableSelect
                  id="subCategory"
                  options={subCategories.map((sc: any) => ({ value: sc.id, label: sc.name }))}
                  value={form.subCategoryId}
                  onValueChange={(val) => setForm({ ...form, subCategoryId: val })}
                  disabled={!form.categoryId || subCategories.length === 0}
                  placeholder={!form.categoryId ? "Select Category first" : subCategories.length === 0 ? "No Subcategories" : "Select Subcategory"}
                  searchPlaceholder="Search subcategory..."
                  emptyText="No subcategories found."
                />
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Basic info */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Basic Information</CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="title">Product Title *</Label>
              <Input id="title" required value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="description">Description *</Label>
              <RichTextEditor 
                value={form.description} 
                onChange={(val) => setForm({ ...form, description: val })}
                productTitle={form.title}
                categoryName={activeCategory?.name}
                subcategoryName={activeSubCategory?.name}
                tags={form.tags}
              />
            </div>
            <div className="space-y-2">
              <Label>Tags</Label>
              <TagInput value={form.tags} onChange={(val) => setForm({ ...form, tags: val })} />
            </div>
          </CardContent>
        </Card>

        {/* Files */}
        <Card>
          <CardHeader>
            <CardTitle className="text-lg">Files</CardTitle>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="space-y-3">
              <Label>Thumbnail Image *</Label>
              <div className="flex flex-col gap-3">
                <div className="flex items-center gap-4">
                  <Label htmlFor="thumbnail" className="flex items-center justify-center px-4 py-2 bg-secondary text-secondary-foreground hover:bg-secondary/80 cursor-pointer rounded-md border text-sm font-medium transition-colors">
                    <Upload className="w-4 h-4 mr-2" />
                    Choose File
                  </Label>
                  <Input id="thumbnail" type="file" accept="image/png, image/jpeg, image/jpg" className="hidden" required={!thumbnailFile && !uploadedUrls.thumbnail} onChange={(e) => {
                    if (e.target.files && e.target.files[0]) setThumbnailFile(e.target.files[0]);
                  }} />
                  {!thumbnailFile && !uploadedUrls.thumbnail && <span className="text-sm text-muted-foreground">No file chosen</span>}
                </div>
                {thumbnailFile ? (
                  <div className="relative w-24 h-24 border rounded-md overflow-hidden group">
                    <img src={URL.createObjectURL(thumbnailFile)} alt="Thumbnail" className="w-full h-full object-cover" />
                    <button type="button" onClick={() => setThumbnailFile(null)} className="absolute top-1 right-1 bg-black/50 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <X className="w-3 h-3" />
                    </button>
                  </div>
                ) : uploadedUrls.thumbnail ? (
                  <div className="relative w-24 h-24 border rounded-md overflow-hidden">
                    <img src={uploadedUrls.thumbnail} alt="Uploaded Thumbnail" className="w-full h-full object-cover" />
                  </div>
                ) : null}
              </div>
              <p className="text-xs text-muted-foreground">Supported Files: .png, .jpg, .jpeg. Image size must be 80x80 px</p>
            </div>
            <div className="space-y-3">
              <Label>Main File (ZIP) *</Label>
              <div className="flex flex-col gap-3">
                <div className="flex items-center gap-4">
                  <Label htmlFor="mainFile" className="flex items-center justify-center px-4 py-2 bg-secondary text-secondary-foreground hover:bg-secondary/80 cursor-pointer rounded-md border text-sm font-medium transition-colors">
                    <Upload className="w-4 h-4 mr-2" />
                    Choose File
                  </Label>
                  <Input id="mainFile" type="file" accept=".zip,.rar,.7z" className="hidden" required={!mainFile && !uploadedUrls.file} onChange={(e) => {
                    if (e.target.files && e.target.files[0]) setMainFile(e.target.files[0]);
                  }} />
                  {!mainFile && !uploadedUrls.file && <span className="text-sm text-muted-foreground">No file chosen</span>}
                </div>
                {mainFile ? (
                  <div className="flex items-center justify-between p-3 border rounded-md max-w-sm">
                    <span className="text-sm truncate mr-4">{mainFile.name}</span>
                    <button type="button" onClick={() => setMainFile(null)} className="text-muted-foreground hover:text-destructive">
                      <X className="w-4 h-4" />
                    </button>
                  </div>
                ) : uploadedUrls.file ? (
                  <div className="flex items-center justify-between p-3 border rounded-md max-w-sm">
                    <span className="text-sm truncate mr-4 text-blue-600 font-medium">
                      {uploadedUrls.file.split('/').pop()}
                    </span>
                  </div>
                ) : null}
              </div>
              <p className="text-xs text-muted-foreground">ZIP all the files for buyers.</p>
            </div>
            <div className="space-y-3">
              <Label>Screenshots</Label>
              <div className="flex flex-col gap-3">
                <div className="flex items-center gap-4">
                  <Label htmlFor="screenshots" className="flex items-center justify-center px-4 py-2 bg-secondary text-secondary-foreground hover:bg-secondary/80 cursor-pointer rounded-md border text-sm font-medium transition-colors">
                    <Upload className="w-4 h-4 mr-2" />
                    Choose Files
                  </Label>
                  <Input id="screenshots" type="file" accept="image/png, image/jpeg, image/jpg" multiple className="hidden" onChange={(e) => {
                    if (e.target.files) {
                      setScreenshotsFiles(prev => [...prev, ...Array.from(e.target.files!)]);
                    }
                  }} />
                  <span className="text-sm text-muted-foreground">
                    {screenshotsFiles.length > 0 ? `${screenshotsFiles.length} file(s) selected` : "No file chosen"}
                  </span>
                </div>
                {screenshotsFiles.length > 0 && (
                  <div className="flex flex-wrap gap-3">
                    {screenshotsFiles.map((file, idx) => (
                      <div key={idx} className="relative w-24 h-24 border rounded-md overflow-hidden group">
                        <img src={URL.createObjectURL(file)} alt="Screenshot" className="w-full h-full object-cover" />
                        <button type="button" onClick={() => setScreenshotsFiles(prev => prev.filter((_, i) => i !== idx))} className="absolute top-1 right-1 bg-black/50 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                          <X className="w-3 h-3" />
                        </button>
                      </div>
                    ))}
                  </div>
                )}
              </div>
              <p className="text-xs text-muted-foreground">Upload multiple screenshot images.</p>
            </div>
            <div className="space-y-2">
              <Label htmlFor="previewVideo">Preview Video (YouTube URL)</Label>
              <Input id="previewVideo" type="url" value={form.previewVideo} onChange={(e) => setForm({ ...form, previewVideo: e.target.value })} />
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
                <Input id="price" type="number" step="0.01" required value={form.price} onChange={(e) => setForm({ ...form, price: e.target.value })} />
              </div>
              <div className="space-y-2">
                <Label htmlFor="priceCl">Commercial License Price ($) *</Label>
                <Input id="priceCl" type="number" step="0.01" required value={form.priceCl} onChange={(e) => setForm({ ...form, priceCl: e.target.value })} />
              </div>
            </div>
            <Separator />
            <div>
              <Label className="text-sm font-medium">Additional Service Prices</Label>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                <div className="space-y-2"><Label>Reskin ($)</Label><Input type="number" step="0.01" value={form.reskinPrice} onChange={(e) => setForm({ ...form, reskinPrice: e.target.value })} /></div>
                <div className="space-y-2"><Label>Publish ($)</Label><Input type="number" step="0.01" value={form.publishPrice} onChange={(e) => setForm({ ...form, publishPrice: e.target.value })} /></div>
                <div className="space-y-2"><Label>Store Opt ($)</Label><Input type="number" step="0.01" value={form.storeOptimizationPrice} onChange={(e) => setForm({ ...form, storeOptimizationPrice: e.target.value })} /></div>
              </div>
            </div>
          </CardContent>
        </Card>

        {/* Submit */}
        <div className="flex justify-end gap-3">
          <Button variant="outline" type="button" asChild><Link href="/admin/products">Cancel</Link></Button>
          <Button type="submit" disabled={saving}>
            {saving ? <><Loader2 className="mr-2 h-4 w-4 animate-spin" /> Publishing...</> : <><Save className="mr-2 h-4 w-4" /> Publish Product</>}
          </Button>
        </div>
      </form>
    </div>
  );
}
