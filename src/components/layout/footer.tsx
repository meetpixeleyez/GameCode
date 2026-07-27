import Link from "next/link";
import Image from "next/image";
import { Mail, Phone, MessageCircle, Facebook, Twitter, Linkedin, Instagram, Youtube } from "lucide-react";

const quickLinks = [
  { label: "Home", href: "/" },
  { label: "About Us", href: "/about" },
  { label: "Resources", href: "/blog" },
  { label: "Game", href: "/#products" },
  { label: "Services", href: "/#products" },
  { label: "Register", href: "/register" },
  { label: "Contact", href: "/contact" },
];

const policyLinks = [
  { label: "Privacy Policy", href: "/privacy-policy" },
  { label: "Terms of Service", href: "/terms-conditions" },
  { label: "Refund Policy", href: "/refund-policy" },
];

const socialLinks = [
  { icon: Facebook, href: "#", label: "Facebook" },
  { icon: Twitter, href: "#", label: "Twitter" },
  { icon: Linkedin, href: "#", label: "LinkedIn" },
  { icon: Instagram, href: "#", label: "Instagram" },
  { icon: Youtube, href: "#", label: "YouTube" },
];

export function Footer() {
  return (
    <footer className="mt-auto bg-black text-white border-t border-white/10">
      <div className="container mx-auto py-12 px-4">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Brand */}
          <div className="space-y-4">
            <Link href="/" className="flex items-center shrink-0 mb-4">
              <Image 
                src="/logo_dark.png" 
                alt="Ready Game Code" 
                width={200} 
                height={66} 
                className="h-12 w-auto object-contain"
                priority
              />
            </Link>
            <p className="text-sm text-white/60">
              Premium Unity game source codes marketplace. Buy, sell, and download
              ready-to-publish game templates.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="font-semibold text-sm mb-4 text-white">Quick Link</h4>
            <ul className="space-y-2">
              {quickLinks.map((link) => (
                <li key={link.label}>
                  <Link
                    href={link.href}
                    className="text-sm text-white/60 hover:text-primary transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Policy Page */}
          <div>
            <h4 className="font-semibold text-sm mb-4 text-white">Policy Page</h4>
            <ul className="space-y-2">
              {policyLinks.map((link) => (
                <li key={link.label}>
                  <Link
                    href={link.href}
                    className="text-sm text-white/60 hover:text-primary transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="font-semibold text-sm mb-4 text-white">Get In Touch</h4>
            <ul className="space-y-4">
              <li>
                <a 
                  href="mailto:info@readygamecode.com" 
                  className="flex items-center gap-2 text-sm text-white/60 hover:text-primary transition-colors"
                >
                  <Mail className="h-4 w-4" />
                  info@readygamecode.com
                </a>
              </li>
              <li>
                <a 
                  href="tel:+919408212310" 
                  className="flex items-center gap-2 text-sm text-white/60 hover:text-primary transition-colors"
                >
                  <Phone className="h-4 w-4" />
                  +91 9408212310
                </a>
              </li>
              <li>
                <a 
                  href="https://api.whatsapp.com/send?phone=919408212310&text=%F0%9F%91%8B%20Hey%20Ready%20Game%20Code,%20can%20you%20help%20me%20with" 
                  className="flex items-center gap-2 text-sm text-white/60 hover:text-primary transition-colors"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <MessageCircle className="h-4 w-4" />
                  Chat On WhatsApp
                </a>
              </li>
            </ul>
            <div className="flex items-center gap-3 mt-4">
              {socialLinks.map((social) => {
                const Icon = social.icon;
                return (
                  <a
                    key={social.label}
                    href={social.href}
                    aria-label={social.label}
                    className="text-white/60 hover:text-primary transition-colors"
                  >
                    <Icon className="h-5 w-5" />
                  </a>
                );
              })}
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="mt-8 pt-8 border-t border-white/10 text-center text-sm text-white/60">
          © {new Date().getFullYear()} Ready Game Code. All rights reserved.
        </div>
      </div>
    </footer>
  );
}
