"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import Logo from "./Logo";
import { nav, site } from "@/lib/site";
import { PhoneIcon, MenuIcon, CloseIcon, ClockIcon, PinIcon } from "./icons";

export default function Header() {
  const pathname = usePathname();
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 12);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <header className="sticky top-0 z-50">
      {/* Top utility bar */}
      <div className="hidden bg-forest-950 text-white/80 lg:block">
        <div className="container-px flex h-10 items-center justify-between text-xs">
          <div className="flex items-center gap-6">
            <span className="inline-flex items-center gap-1.5">
              <PinIcon className="h-4 w-4 text-leaf-400" /> Servicing {site.area}
            </span>
            <span className="inline-flex items-center gap-1.5">
              <ClockIcon className="h-4 w-4 text-leaf-400" /> {site.hours}
            </span>
          </div>
          <div className="flex items-center gap-6">
            <span className="text-leaf-400">Licensed & fully insured</span>
            <a href={site.phoneHref} className="inline-flex items-center gap-1.5 font-semibold text-white hover:text-leaf-400">
              <PhoneIcon className="h-4 w-4" /> {site.phoneDisplay}
            </a>
          </div>
        </div>
      </div>

      {/* Main bar */}
      <div
        className={`border-b transition-all duration-300 ${
          scrolled
            ? "border-sand bg-white/90 shadow-sm backdrop-blur-md"
            : "border-transparent bg-white"
        }`}
      >
        <div className="container-px flex h-18 items-center justify-between py-3">
          <Logo />

          <nav className="hidden items-center gap-1 lg:flex">
            {nav.map((item) => {
              const active =
                item.href === "/" ? pathname === "/" : pathname.startsWith(item.href);
              return (
                <Link
                  key={item.href}
                  href={item.href}
                  className={`relative rounded-full px-4 py-2 text-sm font-semibold transition-colors ${
                    active ? "text-brand-700" : "text-forest-900/70 hover:text-forest-900"
                  }`}
                >
                  {item.label}
                  {active && (
                    <span className="absolute inset-x-4 -bottom-0.5 h-0.5 rounded-full bg-brand-600" />
                  )}
                </Link>
              );
            })}
          </nav>

          <div className="flex items-center gap-3">
            <Link href="/contact" className="btn-primary hidden sm:inline-flex">
              Get a free quote
            </Link>
            <button
              type="button"
              onClick={() => setOpen((v) => !v)}
              aria-label="Toggle menu"
              aria-expanded={open}
              className="grid h-11 w-11 place-items-center rounded-xl border border-sand text-forest-900 lg:hidden"
            >
              {open ? <CloseIcon className="h-6 w-6" /> : <MenuIcon className="h-6 w-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile menu */}
      {open && (
        <div className="border-b border-sand bg-white lg:hidden">
          <nav className="container-px flex flex-col gap-1 py-4">
            {nav.map((item) => {
              const active =
                item.href === "/" ? pathname === "/" : pathname.startsWith(item.href);
              return (
                <Link
                  key={item.href}
                  href={item.href}
                  onClick={() => setOpen(false)}
                  className={`rounded-xl px-4 py-3 text-base font-semibold ${
                    active ? "bg-cream text-brand-700" : "text-forest-900"
                  }`}
                >
                  {item.label}
                </Link>
              );
            })}
            <div className="mt-3 flex flex-col gap-2">
              <a href={site.phoneHref} className="btn-ghost w-full">
                <PhoneIcon className="h-4 w-4" /> {site.phoneDisplay}
              </a>
              <Link href="/contact" onClick={() => setOpen(false)} className="btn-primary w-full">
                Get a free quote
              </Link>
            </div>
          </nav>
        </div>
      )}
    </header>
  );
}
