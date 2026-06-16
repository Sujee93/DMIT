import type { Metadata } from "next";
import { Plus_Jakarta_Sans, Inter } from "next/font/google";
import "./globals.css";
import Header from "@/components/Header";
import Footer from "@/components/Footer";

const jakarta = Plus_Jakarta_Sans({
  variable: "--font-jakarta",
  subsets: ["latin"],
  weight: ["500", "600", "700", "800"],
  display: "swap",
});

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
  display: "swap",
});

export const metadata: Metadata = {
  metadataBase: new URL("https://healthyhomespestcontrol.com.au"),
  title: {
    default: "Healthy Homes | Carpet Cleaning & Pest Control in Sydney",
    template: "%s | Healthy Homes",
  },
  description:
    "Licensed, family-owned carpet cleaning and pest control across Sydney. Safe, effective treatments for termites, cockroaches, ants, spiders, rodents and more. Free quotes.",
  keywords: [
    "pest control Sydney",
    "termite treatment",
    "carpet cleaning",
    "cockroach control",
    "rodent control",
  ],
  openGraph: {
    title: "Healthy Homes | Carpet Cleaning & Pest Control",
    description:
      "Safe, effective, family-owned pest control and carpet cleaning across Sydney.",
    type: "website",
  },
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html
      lang="en-AU"
      className={`${jakarta.variable} ${inter.variable} h-full antialiased`}
    >
      <body className="flex min-h-full flex-col bg-white">
        <Header />
        <main className="flex-1">{children}</main>
        <Footer />
      </body>
    </html>
  );
}
