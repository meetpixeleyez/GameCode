import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import {
  Code2,
  Shield,
  Zap,
  Package,
  Users,
  Globe,
  ArrowRight,
  CheckCircle2,
} from "lucide-react";

export const metadata = {
  title: "About Us",
  description:
    "Ready Game Code is a marketplace for premium Unity game source codes. Built by developers, for developers.",
};

export default function AboutPage() {
  return (
    <div>
      {/* Hero */}
      <section className="bg-gradient-to-br from-primary/5 via-background to-accent/10 border-b border-border">
        <div className="container mx-auto px-4 py-16 md:py-24">
          <div className="max-w-3xl mx-auto text-center space-y-6">
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">
              Engineering the Future of{" "}
              <span className="text-primary">Game Development</span>
            </h1>
            <p className="text-lg text-muted-foreground">
              Ready Game Code is a marketplace for premium Unity game source codes,
              built by developers, for developers. We believe high-quality game
              templates should be accessible, affordable, and ready to publish.
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
              <Button size="lg" asChild>
                <Link href="/products">
                  Explore Products
                  <ArrowRight className="ml-2 h-4 w-4" />
                </Link>
              </Button>
              <Button size="lg" variant="outline" asChild>
                <Link href="/contact">Contact Us</Link>
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Stats */}
      <section className="container mx-auto px-4 py-16">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
          <StatCard icon={Package} value="50+" label="Game Codes" />
          <StatCard icon={Users} value="1+" label="Active Authors" />
          <StatCard icon={Shield} value="100%" label="Verified Code" />
          <StatCard icon={Globe} value="50+" label="Total Sales" />
        </div>
      </section>

      {/* Values */}
      <section className="container mx-auto px-4 py-12">
        <div className="text-center mb-12">
          <h2 className="text-2xl md:text-3xl font-bold">Our Values</h2>
          <p className="text-muted-foreground mt-2">
            What drives us to build the best game code marketplace
          </p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
          <ValueCard
            icon={Code2}
            title="Code Quality First"
            description="Every source code is reviewed by our team before publishing. We ensure clean, readable, and well-documented code that's easy to customize."
          />
          <ValueCard
            icon={Shield}
            title="Verified Licenses"
            description="Personal and Commercial licenses with clear terms. No hidden fees, no surprises. What you see is what you get."
          />
          <ValueCard
            icon={Zap}
            title="Ready to Publish"
            description="Our templates come with AdMob integration, easy reskin options, and full documentation. Launch your game in days, not months."
          />
        </div>
      </section>

      {/* Features */}
      <section className="bg-secondary/30 border-y border-border">
        <div className="container mx-auto px-4 py-16">
          <div className="text-center mb-12">
            <h2 className="text-2xl md:text-3xl font-bold">Why Choose Us?</h2>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto">
            {[
              "Highly Optimized game performance",
              "Modular Setup for easy customization",
              "Verified Licenses with full support",
              "LTS Compatibility across Unity versions",
              "Future Updates included with every purchase",
              "3 Months Support from authors",
              "AdMob Integration ready to monetize",
              "Full Documentation and setup guides",
            ].map((feature) => (
              <div key={feature} className="flex items-center gap-3">
                <CheckCircle2 className="h-5 w-5 text-primary shrink-0" />
                <span className="text-sm">{feature}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="container mx-auto px-4 py-16">
        <Card className="max-w-2xl mx-auto text-center bg-gradient-to-br from-primary/5 to-accent/10">
          <CardContent className="p-8">
            <h2 className="text-2xl font-bold mb-3">Ready to Get Started?</h2>
            <p className="text-muted-foreground mb-6">
              Browse our collection of premium game source codes or become a seller
              to start earning from your game development skills.
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
              <Button asChild>
                <Link href="/products">Browse Products</Link>
              </Button>
              <Button variant="outline" asChild>
                <Link href="/register">Become a Seller</Link>
              </Button>
            </div>
          </CardContent>
        </Card>
      </section>
    </div>
  );
}

function StatCard({
  icon: Icon,
  value,
  label,
}: {
  icon: React.ComponentType<{ className?: string }>;
  value: string;
  label: string;
}) {
  return (
    <div className="text-center">
      <Icon className="h-8 w-8 text-primary mx-auto mb-2" />
      <div className="text-2xl font-bold">{value}</div>
      <div className="text-xs text-muted-foreground mt-1">{label}</div>
    </div>
  );
}

function ValueCard({
  icon: Icon,
  title,
  description,
}: {
  icon: React.ComponentType<{ className?: string }>;
  title: string;
  description: string;
}) {
  return (
    <Card>
      <CardContent className="p-6">
        <div className="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
          <Icon className="h-6 w-6 text-primary" />
        </div>
        <h3 className="font-semibold text-lg mb-2">{title}</h3>
        <p className="text-sm text-muted-foreground">{description}</p>
      </CardContent>
    </Card>
  );
}
