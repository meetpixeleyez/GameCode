"use client";

import { useState, FormEvent } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent } from "@/components/ui/card";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";
import { useToast } from "@/hooks/use-toast";
import { Mail, Phone, MessageCircle, Loader2, Send } from "lucide-react";

const faqs = [
  {
    question: "Are the digital products on your marketplace safe and virus-free?",
    answer:
      "Yes, absolutely. Every source code is reviewed by our team before publishing. We scan all files for malware and verify code quality. Additionally, all files are served from secure servers with SSL encryption.",
  },
  {
    question: "Can I get a refund for a digital product?",
    answer:
      "Due to the nature of digital products, we offer refunds only in specific cases: if the product doesn't work as described, has critical bugs that can't be fixed, or if you haven't downloaded the source code yet. Contact us within 7 days of purchase with your order details.",
  },
  {
    question: "How do I access and download my purchased digital products?",
    answer:
      "After completing your purchase, you'll be redirected to a thank-you page with download links. You can also access all your purchases anytime from your Dashboard → Downloads section. Each purchase comes with a unique purchase code for license verification.",
  },
  {
    question: "What payment methods are accepted?",
    answer:
      "We accept Razorpay (UPI, cards, net banking — for India), PayPal (credit/debit cards — international), and manual Google Pay / UPI transfers. All payments are processed securely through SSL-encrypted gateways.",
  },
  {
    question: "How do I purchase a digital product on your marketplace?",
    answer:
      "Simply browse our products, click 'Add to Cart' on any item, choose your license type (Personal or Commercial) and any additional services (Reskin, Publish, Store Optimization), then proceed to checkout. Complete payment and get instant access to your downloads.",
  },
];

export default function ContactPage() {
  const { toast } = useToast();
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    name: "",
    email: "",
    subject: "",
    message: "",
  });

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });
      const data = await res.json();
      if (!res.ok) {
        toast({
          title: "Failed to send",
          description: data.error,
          variant: "destructive",
        });
        return;
      }
      toast({
        title: "Message sent!",
        description: "We'll get back to you within 24 hours.",
      });
      setForm({ name: "", email: "", subject: "", message: "" });
    } catch {
      toast({ title: "Network error", variant: "destructive" });
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="container mx-auto px-4 py-12">
      <div className="max-w-5xl mx-auto">
        {/* Header */}
        <div className="text-center mb-12">
          <h1 className="text-3xl md:text-4xl font-bold">Contact Us</h1>
          <p className="text-muted-foreground mt-2 max-w-2xl mx-auto">
            Have a question? Want to hire us for custom game development? Fill out
            the form below and we&apos;ll get back to you within 24 hours.
          </p>
        </div>

        {/* Contact methods */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
          <ContactMethod
            icon={Mail}
            title="Email"
            value="info@readygamecode.com"
            href="mailto:info@readygamecode.com"
          />
          <ContactMethod
            icon={Phone}
            title="Phone"
            value="+91 9408212310"
            href="tel:+919408212310"
          />
          <ContactMethod
            icon={MessageCircle}
            title="WhatsApp"
            value="Chat with us"
            href="https://wa.me/919408212310"
          />
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Form */}
          <Card>
            <CardContent className="p-6">
              <h2 className="font-semibold text-lg mb-4">Get in Touch</h2>
              <form onSubmit={handleSubmit} className="space-y-4">
                <div className="space-y-2">
                  <Label htmlFor="name">Name *</Label>
                  <Input
                    id="name"
                    required
                    value={form.name}
                    onChange={(e) => setForm({ ...form, name: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="email">Email *</Label>
                  <Input
                    id="email"
                    type="email"
                    required
                    value={form.email}
                    onChange={(e) => setForm({ ...form, email: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="subject">Subject *</Label>
                  <Input
                    id="subject"
                    required
                    value={form.subject}
                    onChange={(e) => setForm({ ...form, subject: e.target.value })}
                  />
                </div>
                <div className="space-y-2">
                  <Label htmlFor="message">Message *</Label>
                  <Textarea
                    id="message"
                    required
                    rows={5}
                    value={form.message}
                    onChange={(e) => setForm({ ...form, message: e.target.value })}
                  />
                </div>
                <Button type="submit" className="w-full" disabled={loading}>
                  {loading ? (
                    <>
                      <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                      Sending...
                    </>
                  ) : (
                    <>
                      <Send className="mr-2 h-4 w-4" />
                      Send Message
                    </>
                  )}
                </Button>
              </form>
            </CardContent>
          </Card>

          {/* FAQ */}
          <div>
            <h2 className="font-semibold text-lg mb-4">Commonly Asked Questions</h2>
            <Accordion type="single" collapsible className="space-y-3">
              {faqs.map((faq, i) => (
                <AccordionItem
                  key={i}
                  value={`item-${i}`}
                  className="border border-border/80 rounded-xl px-4 bg-card shadow-2xs transition-all duration-200"
                >
                  <AccordionTrigger className="text-sm font-semibold text-left py-3.5 hover:no-underline hover:text-primary">
                    {faq.question}
                  </AccordionTrigger>
                  <AccordionContent className="text-sm text-muted-foreground pb-4 leading-relaxed">
                    {faq.answer}
                  </AccordionContent>
                </AccordionItem>
              ))}
            </Accordion>
          </div>
        </div>
      </div>
    </div>
  );
}

function ContactMethod({
  icon: Icon,
  title,
  value,
  href,
}: {
  icon: React.ComponentType<{ className?: string }>;
  title: string;
  value: string;
  href: string;
}) {
  return (
    <a
      href={href}
      target={href.startsWith("http") ? "_blank" : undefined}
      rel={href.startsWith("http") ? "noopener noreferrer" : undefined}
      className="flex items-center gap-3 p-4 rounded-lg border border-border bg-card hover:border-primary/50 transition-colors"
    >
      <div className="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
        <Icon className="h-5 w-5 text-primary" />
      </div>
      <div>
        <div className="text-xs text-muted-foreground">{title}</div>
        <div className="font-medium text-sm">{value}</div>
      </div>
    </a>
  );
}
