"use client";

import Link from "next/link";
import { Search, ShoppingCart, Menu, X } from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useState } from "react";

const navLinks = [
  { label: "All Items", href: "/#products" },
  { label: "About", href: "/about" },
  { label: "Resources", href: "/blog" },
  {
    label: "Game",
    href: "/#products",
    children: [
      { label: "Puzzle", href: "/#products" },
      { label: "Casual", href: "/#products" },
      { label: "Platformer", href: "/#products" },
      { label: "Action", href: "/#products" },
      { label: "Racing", href: "/#products" },
      { label: "Multiplayer", href: "/#products" },
    ],
  },
  {
    label: "Services",
    href: "/#products",
    children: [
      { label: "Technical Support", href: "/#products" },
    ],
  },
];

export function Header() {
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 w-full border-b border-border/50 glass transition-all duration-300">
      {/* Top contact bar */}
      <div className="hidden md:block bg-primary text-primary-foreground">
        <div className="container mx-auto flex items-center justify-between h-9 text-xs">
          <div className="flex items-center gap-4">
            <Link href="/contact" className="hover:underline">Contact</Link>
            <Link href="/blog" className="hover:underline">Blog</Link>
          </div>
          <div className="flex items-center gap-4">
            <Link href="/contact" className="hover:underline">Hire us</Link>
          </div>
        </div>
      </div>

      {/* Main nav */}
      <div className="container mx-auto">
        <div className="flex h-16 items-center justify-between gap-4">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2 shrink-0">
            <div className="flex items-center gap-2">
              <div className="w-8 h-8 rounded-lg bg-primary flex items-center justify-center">
                <span className="text-primary-foreground font-bold text-sm">R</span>
              </div>
              <span className="font-bold text-lg tracking-tight">
                Ready Game Code
              </span>
            </div>
          </Link>

          {/* Desktop nav */}
          <nav className="hidden lg:flex items-center gap-1">
            {navLinks.map((link) =>
              link.children ? (
                <DropdownMenu key={link.label}>
                  <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="sm" className="text-sm">
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
                <Button key={link.label} variant="ghost" size="sm" asChild>
                  <Link href={link.href} className="text-sm">
                    {link.label}
                  </Link>
                </Button>
              )
            )}
          </nav>

          {/* Search */}
          <div className="hidden md:flex items-center flex-1 max-w-sm">
            <div className="relative w-full">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
              <input
                type="search"
                placeholder="Search..."
                className="w-full pl-9 pr-3 py-2 text-sm rounded-md border border-input bg-background focus:outline-none focus:ring-2 focus:ring-ring"
              />
            </div>
          </div>

          {/* Right actions */}
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon" asChild className="relative">
              <Link href="/cart" aria-label="Cart">
                <ShoppingCart className="h-5 w-5" />
                <span className="absolute -top-1 -right-1 bg-primary text-primary-foreground text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold">
                  0
                </span>
              </Link>
            </Button>
            <div className="hidden sm:flex items-center gap-2">
              <Button variant="ghost" size="sm" asChild>
                <Link href="/login">Login</Link>
              </Button>
              <Button size="sm" asChild>
                <Link href="/register">Register</Link>
              </Button>
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
          <div className="lg:hidden border-t border-border py-4">
            <nav className="flex flex-col gap-1">
              {navLinks.map((link) => (
                <Button
                  key={link.label}
                  variant="ghost"
                  size="sm"
                  asChild
                  className="justify-start"
                  onClick={() => setMobileOpen(false)}
                >
                  <Link href={link.href}>{link.label}</Link>
                </Button>
              ))}
              <div className="flex flex-col gap-2 mt-2 pt-2 border-t border-border">
                <Button variant="outline" size="sm" asChild>
                  <Link href="/login">Login</Link>
                </Button>
                <Button size="sm" asChild>
                  <Link href="/register">Register</Link>
                </Button>
              </div>
            </nav>
          </div>
        )}
      </div>
    </header>
  );
}
