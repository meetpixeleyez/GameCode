import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { Toaster } from "@/components/ui/toaster";
import { Header } from "@/components/layout/header";
import { Footer } from "@/components/layout/footer";

const inter = Inter({
  variable: "--font-geist-sans",
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

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body
        className={`${inter.variable} antialiased bg-background text-foreground min-h-screen flex flex-col`}
      >
        <Header />
        <main className="flex-1">{children}</main>
        <Footer />
        <Toaster />
      </body>
    </html>
  );
}
