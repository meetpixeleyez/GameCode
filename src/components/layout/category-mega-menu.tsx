"use client";

import { useState, useMemo } from "react";
import Link from "next/link";
import { Search, ChevronDown } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";

type SubCategory = {
  id: string;
  name: string;
};

type Category = {
  id: string;
  name: string;
  subCategories: SubCategory[];
};

export function CategoryMegaMenu({ categories }: { categories: Category[] }) {
  const [open, setOpen] = useState(false);
  const [search, setSearch] = useState("");

  const filteredCategories = useMemo(() => {
    if (!search.trim()) return categories;

    const query = search.toLowerCase();
    
    return categories.map(category => {
      // If category name matches, return it with all its subcategories
      if (category.name.toLowerCase().includes(query)) {
        return category;
      }
      
      // Otherwise, filter its subcategories
      const filteredSubs = category.subCategories.filter(sub => 
        sub.name.toLowerCase().includes(query)
      );

      return {
        ...category,
        subCategories: filteredSubs
      };
    }).filter(category => category.subCategories.length > 0 || category.name.toLowerCase().includes(query));
  }, [categories, search]);

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button variant="ghost" size="sm" className="text-sm font-medium flex items-center gap-1">
          Games
          <ChevronDown className="h-4 w-4" />
        </Button>
      </DialogTrigger>
      <DialogContent className="max-w-4xl max-h-[85vh] overflow-hidden flex flex-col p-0">
        <DialogHeader className="px-6 py-4 border-b border-border/50">
          <DialogTitle className="text-xl font-bold">Browse Games & Categories</DialogTitle>
          <div className="relative mt-4">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input 
              placeholder="Search categories or sub-categories..."
              className="pl-9 h-11 bg-muted/50 border-border/50"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
        </DialogHeader>
        
        <div className="flex-1 overflow-y-auto p-6 bg-background">
          {filteredCategories.length > 0 ? (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {filteredCategories.map(category => (
                <div key={category.id} className="space-y-3">
                  <Link 
                    href={`/products?category=${category.id}`}
                    onClick={() => setOpen(false)}
                    className="block"
                  >
                    <h3 className="font-bold text-lg hover:text-primary transition-colors border-b border-border/50 pb-2">
                      {category.name}
                    </h3>
                  </Link>
                  {category.subCategories.length > 0 ? (
                    <ul className="space-y-1">
                      {category.subCategories.map(sub => (
                        <li key={sub.id}>
                          <Link 
                            href={`/products?category=${category.id}&sub_category=${sub.id}`}
                            onClick={() => setOpen(false)}
                            className="text-sm text-muted-foreground hover:text-primary transition-colors block py-1"
                          >
                            {sub.name}
                          </Link>
                        </li>
                      ))}
                    </ul>
                  ) : (
                    <p className="text-sm text-muted-foreground/50 italic">No sub-categories</p>
                  )}
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-12">
              <p className="text-muted-foreground">No categories found matching &quot;{search}&quot;</p>
            </div>
          )}
        </div>
      </DialogContent>
    </Dialog>
  );
}
