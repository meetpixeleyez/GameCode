import type { Metadata } from "next";
import { Inter, Outfit } from "next/font/google";
import "./globals.css";
import { Toaster } from "@/components/ui/toaster";
import { Header } from "@/components/layout/header";
import { Footer } from "@/components/layout/footer";
import { getCurrentUser } from "@/lib/auth";
import { getCartContext } from "@/lib/cart-session";
import { db } from "@/lib/db";

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
});

const outfit = Outfit({
  variable: "--font-outfit",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  title: {
    default: "Ready Game Code — Buy Unity Source Codes & Game Templates",
    template: "%s | Ready Game Code",
  },
  description:
    "Turn Your Game Dreams into Reality with Premium Source Codes! Buy high-quality Unity, Android, and iOS game source codes at affordable prices. Ready-to-publish game templates with AdMob integration, easy reskin options, and full documentation.",
  keywords: [
    "digital product",
    "buy unity game source code",
    "buy unity source code",
    "unity 3d template",
    "android game code",
    "car racing game source code",
    "ready to publish unity game",
    "unity game code",
    "android game source code",
    "IOS source code",
    "buy action game source code",
    "buy casual game source code",
  ],
  authors: [{ name: "Ready Game Code" }],
  icons: {
    icon: "/favicon.ico",
  },
  openGraph: {
    title: "Buy Unity Source Code | Ready Game Code",
    description:
      "Buy high-quality Unity, Android, and iOS game source codes at affordable prices. Ready-to-publish game templates with AdMob integration, easy reskin options, and full documentation.",
    url: "https://readygamecode.com",
    siteName: "Ready Game Code",
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: "Ready Game Code — Unity Source Codes Marketplace",
    description:
      "Buy high-quality Unity, Android, and iOS game source codes at affordable prices.",
  },
  robots: {
    index: true,
    follow: true,
  },
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const session = await getCurrentUser();
  const cartCtx = await getCartContext();
  const cartCount = await db.cart.count({
    where: cartCtx.userId ? { userId: cartCtx.userId } : { sessionId: cartCtx.sessionId },
  });

  const categories = await db.category.findMany({
    where: { status: 1 },
    include: { subCategories: { where: { status: 1 }, orderBy: { name: 'asc' } } },
    orderBy: { name: 'asc' },
  });

  return (
    <html lang="en" suppressHydrationWarning>
      <body
        className={`${inter.variable} ${outfit.variable} antialiased min-h-screen flex flex-col font-sans`}
      >
        <Header session={session} cartCount={cartCount} categories={categories} />
        <main className="flex-1">{children}</main>
        <Footer />
        <Toaster />
      </body>
    </html>
  );
}
