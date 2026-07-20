import { db } from "@/lib/db";

export const dynamic = "force-dynamic";

// Loads a policy page from DB (Frontend table — Laravel-compatible) or returns null
export async function getPolicyPage(slug: string) {
  const frontend = await db.frontend.findFirst({
    where: {
      slug,
      dataKeys: "policy_pages.element",
    },
  });

  if (frontend) {
    try {
      const values = JSON.parse(frontend.dataValues || "{}");
      return {
        title: values.title || slug,
        body: values.body || "",
      };
    } catch {
      // fall through
    }
  }

  const page = await db.page.findUnique({ where: { slug } });
  if (page) {
    try {
      const secs = JSON.parse(page.secs || "{}");
      return {
        title: page.name || slug,
        body: secs.body || "",
      };
    } catch {
      // fall through
    }
  }

  return null;
}

export function PolicyLayout({
  title,
  children,
}: {
  title: string;
  children: React.ReactNode;
}) {
  return (
    <div className="container mx-auto px-4 py-12">
      <div className="max-w-3xl mx-auto">
        <h1 className="text-3xl font-bold mb-2">{title}</h1>
        <p className="text-sm text-muted-foreground mb-8">
          Last updated:{" "}
          {new Date().toLocaleDateString(undefined, {
            year: "numeric",
            month: "long",
            day: "numeric",
          })}
        </p>
        <div className="prose prose-sm max-w-none dark:prose-invert space-y-4">
          {children}
        </div>
      </div>
    </div>
  );
}
