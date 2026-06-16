import Link from "next/link";
import Logo from "./Logo";
import { nav, pests, site } from "@/lib/site";
import { PhoneIcon, MailIcon, PinIcon, ClockIcon, LeafIcon } from "./icons";

export default function Footer() {
  return (
    <footer className="mt-auto bg-forest-950 text-white/70">
      {/* CTA strip */}
      <div className="container-px">
        <div className="relative -translate-y-1/2 overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-forest-800 px-7 py-10 shadow-2xl shadow-forest-950/40 sm:px-12 sm:py-12">
          <LeafIcon className="pointer-events-none absolute -right-6 -top-6 h-44 w-44 text-white/10" />
          <div className="relative flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
              <h2 className="text-2xl font-extrabold text-white sm:text-3xl">
                Ready for a pest-free home?
              </h2>
              <p className="mt-2 max-w-md text-white/80">
                Book a free, no-obligation inspection and get a same-day quote
                from our licensed local technicians.
              </p>
            </div>
            <div className="flex flex-col gap-3 sm:flex-row">
              <Link href="/contact" className="btn-leaf">
                Get a free quote
              </Link>
              <a href={site.phoneHref} className="btn-outline">
                <PhoneIcon className="h-4 w-4" /> {site.phoneDisplay}
              </a>
            </div>
          </div>
        </div>
      </div>

      {/* Main footer */}
      <div className="container-px -mt-8 pb-10">
        <div className="grid gap-10 border-t border-white/10 pt-12 md:grid-cols-2 lg:grid-cols-12">
          <div className="lg:col-span-4">
            <Logo variant="light" />
            <p className="mt-5 max-w-xs text-sm leading-relaxed">
              Family-owned carpet cleaning and pest control, keeping {site.area}{" "}
              homes and businesses healthy, safe and pest-free.
            </p>
            <p className="mt-5 text-xs text-white/45">{site.abn}</p>
          </div>

          <div className="lg:col-span-2">
            <h3 className="text-sm font-bold uppercase tracking-wider text-white">
              Explore
            </h3>
            <ul className="mt-4 space-y-3 text-sm">
              {nav.map((item) => (
                <li key={item.href}>
                  <Link href={item.href} className="hover:text-leaf-400">
                    {item.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div className="lg:col-span-3">
            <h3 className="text-sm font-bold uppercase tracking-wider text-white">
              Services
            </h3>
            <ul className="mt-4 grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
              {pests.map((p) => (
                <li key={p.slug}>
                  <Link href="/services" className="hover:text-leaf-400">
                    {p.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div className="lg:col-span-3">
            <h3 className="text-sm font-bold uppercase tracking-wider text-white">
              Get in touch
            </h3>
            <ul className="mt-4 space-y-4 text-sm">
              <li>
                <a href={site.phoneHref} className="flex items-start gap-3 hover:text-leaf-400">
                  <PhoneIcon className="mt-0.5 h-5 w-5 text-leaf-400" />
                  {site.phoneDisplay}
                </a>
              </li>
              <li>
                <a href={`mailto:${site.email}`} className="flex items-start gap-3 break-all hover:text-leaf-400">
                  <MailIcon className="mt-0.5 h-5 w-5 text-leaf-400" />
                  {site.email}
                </a>
              </li>
              <li className="flex items-start gap-3">
                <PinIcon className="mt-0.5 h-5 w-5 text-leaf-400" />
                Servicing {site.area}
              </li>
              <li className="flex items-start gap-3">
                <ClockIcon className="mt-0.5 h-5 w-5 text-leaf-400" />
                {site.hours}
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-12 flex flex-col items-center justify-between gap-3 border-t border-white/10 pt-6 text-xs text-white/45 sm:flex-row">
          <p>© {new Date().getFullYear()} {site.name} {site.tagline}. All rights reserved.</p>
          <p className="flex items-center gap-4">
            <Link href="#" className="hover:text-white/80">Privacy</Link>
            <Link href="#" className="hover:text-white/80">Terms</Link>
          </p>
        </div>
      </div>
    </footer>
  );
}
