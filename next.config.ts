import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  output: "standalone",
  /* config options here */
  typescript: {
    ignoreBuildErrors: true,
  },
  reactStrictMode: false,
  images: {
    // Allow SVG product placeholder images (production should use real PNG/JPG)
    dangerouslyAllowSVG: true,
    unoptimized: true,
  },
};

export default nextConfig;
