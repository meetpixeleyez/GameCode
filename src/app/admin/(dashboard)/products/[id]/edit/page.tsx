import { db } from "@/lib/db";
import { getCurrentUser } from "@/lib/auth";
import { notFound, redirect } from "next/navigation";
import ProductEditForm from "@/components/product-edit-form";

export const dynamic = "force-dynamic";

export default async function AdminProductEditPage({ params }: { params: Promise<{ id: string }> }) {
  const session = await getCurrentUser();
  if (!session || (session.role !== "admin" && session.role !== "ADMIN")) redirect("/admin/login");

  const { id } = await params;

  const product = await db.product.findUnique({
    where: { id },
  });

  if (!product) {
    notFound();
  }

  // Parse tags if possible
  let tags = "";
  if (product.tags) {
    try {
      const parsed = JSON.parse(product.tags);
      if (Array.isArray(parsed)) tags = parsed.join(", ");
    } catch {
      tags = product.tags;
    }
  }

  const initialData = {
    id: product.id,
    categoryId: product.categoryId || "",
    subCategoryId: product.subCategoryId || "",
    title: product.title,
    description: product.description || "",
    price: product.price.toString(),
    priceCl: product.priceCl.toString(),
    demoUrl: product.demoUrl || "",
    previewVideo: product.previewVideo || "",
    thumbnail: product.thumbnail || "",
    file: product.file || "",
    inlinePreviewImage: product.inlinePreviewImage || "[]",
    tags,
    metaTitle: product.metaTitle || "",
    metaDescription: product.metaDescription || "",
    reskinPrice: product.reskinPrice.toString(),
    publishPrice: product.publishPrice.toString(),
    storeOptimizationPrice: product.storeOptimizationPrice.toString(),
  };

  return <ProductEditForm initialData={initialData} isAdmin={true} />;
}
