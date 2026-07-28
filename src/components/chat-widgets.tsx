"use client";

import { useEffect } from "react";
import Link from "next/link";
import Image from "next/image";

export function ChatWidgets() {
  useEffect(() => {
    // Initialize Tawk.to window variables
    (window as any).Tawk_API = (window as any).Tawk_API || {};
    (window as any).Tawk_LoadStart = new Date();

    const propertyId = "6a66eeb59b421a1d42846db7";
    const widgetId = "1juh18u83";

    const script = document.createElement("script");
    script.async = true;
    script.src = `https://embed.tawk.to/${propertyId}/${widgetId}`;
    script.charset = "UTF-8";
    script.setAttribute("crossorigin", "*");

    const firstScript = document.getElementsByTagName("script")[0];
    if (firstScript && firstScript.parentNode) {
      firstScript.parentNode.insertBefore(script, firstScript);
    } else {
      document.body.appendChild(script);
    }

    return () => {
      if (script.parentNode) {
        script.parentNode.removeChild(script);
      }
    };
  }, []);

  return (
    <>
      {/* WhatsApp Floating Button with Auto Spiral / Sonar Ripple Waves */}
      <div className="fixed bottom-6 left-6 z-50 flex flex-col items-center group">
        <Link
          href="https://api.whatsapp.com/send?phone=9194082123108&text=%F0%9F%91%8B%20Hey%20Ready%20Game%20Code,%20can%20you%20help%20me%20with"
          target="_blank"
          rel="noopener noreferrer"
          className="relative flex flex-col items-center justify-center transition-transform hover:scale-105"
          aria-label="Talk to us on WhatsApp"
        >
          {/* Main WhatsApp Image with Ripple Waves */}
          <div className="relative flex h-20 w-20 items-center justify-center rounded-full">
            {/* Staggered Sonar/Spiral Ripple Wave Rings */}
            <span className="absolute inset-0 rounded-full bg-[#25D366]/50 animate-ping opacity-75" style={{ animationDuration: '2.2s' }} />
            <span className="absolute inset-0 rounded-full bg-[#25D366]/30 animate-ping opacity-50" style={{ animationDuration: '2.2s', animationDelay: '0.7s' }} />

            {/* Custom WhatsApp PNG Image */}
            <Image
              src="/whatsapp.png"
              alt="WhatsApp Chat"
              width={80}
              height={80}
              className="relative z-10 h-20 w-20 drop-shadow-lg rounded-full object-contain"
              unoptimized
            />
          </div>

          {/* Label Underneath */}
          <span className="mt-1 text-[11px] font-semibold text-slate-600 tracking-tight whitespace-nowrap drop-shadow-sm group-hover:text-[#25D366] transition-colors">
            Talk to us?
          </span>
        </Link>
      </div>
    </>
  );
}
