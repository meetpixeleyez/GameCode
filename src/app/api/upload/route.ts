import { NextRequest, NextResponse } from "next/server";
import { getCurrentUser } from "@/lib/auth";
import { writeFile, mkdir } from "fs/promises";
import { join } from "path";
import { existsSync } from "fs";

export const dynamic = "force-dynamic";
export const maxDuration = 300;

export async function POST(req: NextRequest) {
  try {
    const session = await getCurrentUser();
    if (!session) {
      return NextResponse.json({ error: "Session expired or unauthorized. Please log in again." }, { status: 401 });
    }

    const formData = await req.formData();
    
    // We can handle multiple files dynamically by iterating over all entries
    const uploadedFiles: Record<string, string | string[]> = {};
    
    const uploadDir = join(process.cwd(), "public", "uploads");
    if (!existsSync(uploadDir)) {
      await mkdir(uploadDir, { recursive: true });
    }

    // Convert formData entries to array to handle async loops cleanly
    const entries = Array.from(formData.entries());
    
    for (const [fieldName, formDataEntry] of entries) {
      if (formDataEntry instanceof File) {
        const file = formDataEntry as File;
        if (!file.name || file.size === 0) continue;

        const bytes = await file.arrayBuffer();
        const buffer = Buffer.from(bytes);

        // Generate unique filename
        const ext = file.name.split(".").pop();
        const uniqueSuffix = `${Date.now()}-${Math.round(Math.random() * 1e9)}`;
        const filename = `${fieldName}-${uniqueSuffix}.${ext}`;
        const path = join(uploadDir, filename);

        await writeFile(path, buffer);
        const fileUrl = `/uploads/${filename}`;

        // If field already exists (e.g. multiple screenshots), make it an array
        if (uploadedFiles[fieldName]) {
          if (Array.isArray(uploadedFiles[fieldName])) {
            (uploadedFiles[fieldName] as string[]).push(fileUrl);
          } else {
            uploadedFiles[fieldName] = [uploadedFiles[fieldName] as string, fileUrl];
          }
        } else {
          uploadedFiles[fieldName] = fileUrl;
        }
      }
    }

    if (Object.keys(uploadedFiles).length === 0) {
      return NextResponse.json({ error: "No files provided" }, { status: 400 });
    }

    return NextResponse.json({ success: true, files: uploadedFiles });
  } catch (error: any) {
    console.error("Upload error:", error);
    return NextResponse.json(
      { error: error?.message || "File upload failed on server" },
      { status: 500 }
    );
  }
}
