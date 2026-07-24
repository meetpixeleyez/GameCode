"use client";

import { useEffect } from "react";
import Link from "next/link";

export function ChatWidgets() {
  useEffect(() => {
    // Tawk.to integration script
    // TODO: Replace 'YOUR_PROPERTY_ID' with the actual ID from Tawk.to dashboard
    const propertyId = "YOUR_PROPERTY_ID"; // Change this!
    const widgetId = "default"; // Change this if you have a specific widget ID

    // If propertyId is YOUR_PROPERTY_ID, Tawk.to won't load properly until you change it.
    if (propertyId === "YOUR_PROPERTY_ID") {
      console.warn("Tawk.to Property ID is not set. Please update it in src/components/chat-widgets.tsx");
    }

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
      // Optional: Cleanup if the component unmounts (rare for global layout)
      if (script.parentNode) {
        script.parentNode.removeChild(script);
      }
    };
  }, []);

  return (
    <>
      {/* WhatsApp Floating Button */}
      <div className="fixed bottom-6 left-6 z-50 flex flex-col items-center group">
        <Link
          href="https://api.whatsapp.com/send?phone=919408212310"
          target="_blank"
          rel="noopener noreferrer"
          className="relative flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition-transform hover:scale-110 hover:shadow-xl"
          aria-label="Chat with us on WhatsApp"
        >
          {/* SVG WhatsApp Logo */}
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="h-8 w-8">
            <path d="M12.031 0C5.388 0 0 5.388 0 12.032c0 2.115.55 4.184 1.59 6l-1.63 5.968 6.103-1.603A12.015 12.015 0 0012.031 24c6.643 0 12.031-5.388 12.031-12.032C24.062 5.388 18.674 0 12.031 0zm.012 20.012c-1.803 0-3.568-.484-5.116-1.402l-.367-.217-3.8.997 1.015-3.704-.238-.378a9.98 9.98 0 01-1.528-5.326c0-5.505 4.48-9.986 9.985-9.986 2.66 0 5.163 1.037 7.043 2.918 1.88 1.881 2.916 4.384 2.916 7.045 0 5.505-4.48 9.986-9.985 9.986zm5.495-7.513c-.302-.151-1.782-.879-2.057-.98-.276-.1-.477-.151-.678.151-.201.302-.779.98-1.01 1.182-.195.17-.417.195-.718.045-2.05-.985-3.328-2.185-4.444-4.106-.2-.345.203-.321.79-1.493.101-.202.05-.378-.025-.529-.075-.152-.678-1.633-.928-2.235-.245-.59-.495-.51-.678-.52-.176-.01-.378-.01-.578-.01-.2 0-.527.075-.803.377-.276.301-1.054 1.03-1.054 2.511 0 1.482 1.08 2.914 1.23 3.115.15.201 2.124 3.245 5.143 4.545 2.155.932 2.766 1.006 3.266.953.56-.06 1.782-.728 2.032-1.432.251-.703.251-1.306.176-1.432-.075-.126-.276-.201-.577-.352z"/>
          </svg>
          
          <span className="absolute -top-1 -right-1 flex h-4 w-4">
            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span className="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white"></span>
          </span>
        </Link>
        <div className="absolute -bottom-8 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap bg-background border shadow-sm px-2 py-1 rounded-md text-xs font-medium">
          Talk to us?
        </div>
      </div>
    </>
  );
}
