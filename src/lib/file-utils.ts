import fs from "fs";
import path from "path";

export async function deleteLocalFiles(fileUrls: string[]) {
  const publicDir = path.join(process.cwd(), "public");

  for (const fileUrl of fileUrls) {
    if (!fileUrl) continue;
    
    // We only want to delete files stored locally, i.e., those that start with /uploads
    if (fileUrl.startsWith("/uploads")) {
      try {
        // Construct physical path: e.g., C:\...\public\uploads\file.zip
        const filePath = path.join(publicDir, fileUrl);
        
        if (fs.existsSync(filePath)) {
          fs.unlinkSync(filePath);
          console.log(`Successfully deleted file: ${filePath}`);
        }
      } catch (error) {
        console.error(`Failed to delete file at ${fileUrl}:`, error);
      }
    }
  }
}
