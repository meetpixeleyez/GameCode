"use client";

import Link from "next/link";
import { Search, ShoppingCart, Menu, X, User } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useState } from "react";
import Image from "next/image";
import { usePathname } from "next/navigation";
import { JwtPayload } from "@/lib/auth";
import { SearchBar } from "@/components/SearchBar";
import { CategoryMegaMenu } from "./category-mega-menu";

type SubCategory = { id: string; name: string };
type Category = { id: string; name: string; subCategories: SubCategory[] };

type NavLink = {
  label: string;
  href: string;
  children?: { label: string; href: string }[];
};

const navLinks: NavLink[] = [
  { label: "All Items", href: "/products" },
  { label: "About", href: "/about" },
  { label: "Resources", href: "/resources" },
  { label: "Privacy Policy", href: "/privacy-policy" },
  { label: "Terms & Conditions", href: "/terms-conditions" },
];

export function Header({ session, cartCount = 0, categories = [] }: { session: JwtPayload | null; cartCount?: number; categories?: Category[] }) {
  const [mobileOpen, setMobileOpen] = useState(false);
  const pathname = usePathname();

  const isActive = (href: string) => {
    if (!pathname) return false;
    const baseHref = href.split("?")[0];
    if (baseHref === "/") return pathname === "/";
    return pathname.startsWith(baseHref);
  };

  return (
    <header className="sticky top-0 z-50 w-full transition-all duration-300">
      {/* Top marquee bar */}
      <div className="w-full bg-[#111] text-white">
        <div className="container mx-auto flex justify-between items-center py-1.5 px-4">
          {/* Left side */}
          <div className="hidden md:flex items-center gap-4 text-sm font-medium">
            <Link href="/contact" className="hover:text-primary transition-colors">Contact</Link>
            <span className="text-muted-foreground">|</span>
            <Link href="/blog" className="hover:text-primary transition-colors">Blog</Link>
          </div>

          {/* Center scroll */}
          <div className="relative overflow-hidden flex-1 mx-4 text-center whitespace-nowrap hidden sm:block">
            <div className="inline-block whitespace-nowrap animate-marquee">
              <span className="inline-block pr-16 text-sm">
                Latest News Here | Updates Coming Soon | Welcome to Our Website | Don’t Miss Out!
              </span>
              <span className="inline-block pr-16 text-sm">
                Latest News Here | Updates Coming Soon | Welcome to Our Website | Don’t Miss Out!
              </span>
            </div>
          </div>

          {/* Right side hire button */}
          <Link
            href="https://api.whatsapp.com/send?phone=919408212310&text=%F0%9F%91%8B%20Hey%20Ready%20Game%20Code,%20can%20you%20help%20me%20with"
            target="_blank"
            className="px-4 py-1 rounded-full border-2 border-white/20 bg-transparent text-white font-bold relative overflow-hidden transition-colors hover:border-white/40"
          >
            <span className="bg-gradient-to-r from-primary via-[#ffcc70] to-white bg-[length:200%_auto] text-transparent bg-clip-text animate-shine inline-block">
              Hire us
            </span>
          </Link>
        </div>
      </div>

      {/* Main nav */}
      <div className="w-full border-b border-border/50 bg-background/80 backdrop-blur-md shadow-xs">
        <div className="container mx-auto">
        <div className="flex h-16 items-center justify-between gap-4">
          {/* Logo */}
          <Link href="/" className="flex items-center shrink-0">
            <Image 
              src="/logo.png" 
              alt="Ready Game Code" 
              width={180} 
              height={60} 
              className="h-10 w-auto object-contain"
              priority
            />
          </Link>

          {/* Desktop nav */}
          <nav className="hidden lg:flex items-center gap-1">
            <CategoryMegaMenu categories={categories} />
            {navLinks.map((link) => {
              const active = isActive(link.href);
              return link.children ? (
                <DropdownMenu key={link.label}>
                  <DropdownMenuTrigger asChild>
                    <Button 
                      variant="ghost" 
                      size="sm" 
                      className={`text-sm ${active ? "text-primary font-semibold bg-primary/5" : ""}`}
                    >
                      {link.label}
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="start">
                    {link.children.map((child) => (
                      <DropdownMenuItem key={child.label} asChild>
                        <Link href={child.href}>{child.label}</Link>
                      </DropdownMenuItem>
                    ))}
                  </DropdownMenuContent>
                </DropdownMenu>
              ) : (
                <Button 
                  key={link.label} 
                  variant="ghost" 
                  size="sm" 
                  asChild
                  className={active ? "text-primary font-semibold bg-primary/5" : ""}
                >
                  <Link href={link.href} className="text-sm">
                    {link.label}
                  </Link>
                </Button>
              );
            })}
          </nav>

          {/* Search */}
          <div className="hidden md:flex items-center flex-1 max-w-sm justify-end lg:justify-center">
            <SearchBar />
          </div>

          {/* Right actions */}
          <div className="flex items-center gap-2">
            {session?.role !== "admin" && (
              <Button variant="ghost" size="icon" asChild className="relative">
                <Link href="/cart" aria-label="Cart">
                  <ShoppingCart className="h-5 w-5" />
                  <span className="absolute -top-1 -right-1 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
                    {cartCount}
                  </span>
                </Link>
              </Button>
            )}
            <div className="hidden sm:flex items-center gap-2">
              {session ? (
                <>
                  {session.role === "admin" && (
                    <Button variant="outline" size="sm" asChild className="mr-2">
                      <Link href="/admin">Admin Panel</Link>
                    </Button>
                  )}
                  <Button variant="ghost" size="icon" asChild>
                    <Link href={session.role === "admin" ? "/admin" : "/dashboard/profile"} aria-label="Profile">
                      <User className="h-5 w-5" />
                    </Link>
                  </Button>
                </>
              ) : (
                <>
                  <Button variant="ghost" size="sm" asChild>
                    <Link href="/login">Login</Link>
                  </Button>
                  <Button size="sm" asChild>
                    <Link href="/register">Register</Link>
                  </Button>
                </>
              )}
            </div>
            {/* Mobile menu toggle */}
            <Button
              variant="ghost"
              size="icon"
              className="lg:hidden"
              onClick={() => setMobileOpen(!mobileOpen)}
              aria-label="Toggle menu"
            >
              {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </Button>
          </div>
        </div>

        {/* Mobile nav */}
        {mobileOpen && (
          <div className="absolute top-16 left-0 w-full bg-background border-b border-border shadow-lg py-4 px-4 flex flex-col gap-4 z-40 lg:hidden">
            <nav className="flex flex-col gap-2">
              <div className="font-semibold text-sm px-2">
                <CategoryMegaMenu categories={categories} />
              </div>
              {navLinks.map((link) => {
                const active = isActive(link.href);
                return link.children ? (
                  <div key={link.label} className="flex flex-col gap-2">
                    <div className={`font-semibold text-sm px-2 ${active ? "text-primary" : ""}`}>
                      {link.label}
                    </div>
                    <div className="flex flex-col pl-4 border-l-2 border-border ml-2 gap-2">
                      {link.children.map((child) => (
                        <Link
                          key={child.label}
                          href={child.href}
                          className="text-sm text-muted-foreground hover:text-foreground"
                          onClick={() => setMobileOpen(false)}
                        >
                          {child.label}
                        </Link>
                      ))}
                    </div>
                  </div>
                ) : (
                  <Link
                    key={link.label}
                    href={link.href}
                    className={`font-semibold text-sm px-2 py-1 rounded-md ${
                      active ? "text-primary bg-primary/5" : "hover:text-primary"
                    }`}
                    onClick={() => setMobileOpen(false)}
                  >
                    {link.label}
                  </Link>
                );
              })}
            </nav>
          </div>
        )}
        </div>
      </div>
    </header>
  );
}
