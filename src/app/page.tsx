import Link from "next/link";
import { pests, site } from "@/lib/site";
import PestIcon from "@/components/PestIcon";
import SmartImage from "@/components/SmartImage";
import TrustBadges from "@/components/TrustBadges";
import Process from "@/components/Process";
import Testimonials from "@/components/Testimonials";
import FAQ from "@/components/FAQ";
import {
  ArrowRight,
  PhoneIcon,
  CheckIcon,
  ShieldIcon,
  StarIcon,
  LeafIcon,
} from "@/components/icons";

const IMG = {
  hero: "https://images.unsplash.com/photo-1560185007-cde436f6a4d0?auto=format&fit=crop&w=1200&q=80",
  family:
    "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1000&q=80",
};

export default function HomePage() {
  return (
    <>
      {/* ───────────────────────── Hero ───────────────────────── */}
      <section className="relative overflow-hidden bg-forest-950 text-white">
        <div className="absolute inset-0">
          <SmartImage src={IMG.hero} alt="" className="h-full w-full opacity-25" />
          <div className="absolute inset-0 bg-gradient-to-br from-forest-950 via-forest-950/95 to-forest-900/80" />
        </div>
        <LeafIcon className="pointer-events-none absolute -left-10 top-10 h-64 w-64 text-white/[0.04]" />

        <div className="container-px relative grid items-center gap-12 py-20 lg:grid-cols-12 lg:py-28">
          <div className="lg:col-span-7">
            <span className="inline-flex items-center gap-2 rounded-full border border-leaf-400/30 bg-leaf-400/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-leaf-400">
              <LeafIcon className="h-4 w-4" /> Safe · Local · Family-owned
            </span>

            <h1 className="mt-6 text-balance text-5xl font-extrabold leading-[1.02] sm:text-6xl lg:text-7xl">
              A healthier,
              <br />
              <span className="text-leaf-400">pest-free</span> home.
            </h1>

            <p className="mt-6 max-w-xl text-lg text-white/75">
              Licensed carpet cleaning and pest control across {site.area}. We
              get rid of termites, cockroaches, rodents and more — with safe,
              effective treatments your family and pets can trust.
            </p>

            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
              <Link href="/contact" className="btn-leaf text-base">
                Get a free quote <ArrowRight className="h-4 w-4" />
              </Link>
              <a href={site.phoneHref} className="btn-outline text-base">
                <PhoneIcon className="h-4 w-4" /> {site.phoneDisplay}
              </a>
            </div>

            <div className="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4 text-sm text-white/70">
              <span className="inline-flex items-center gap-2">
                <CheckIcon className="h-5 w-5 text-leaf-400" /> 100% satisfaction guarantee
              </span>
              <span className="inline-flex items-center gap-2">
                <CheckIcon className="h-5 w-5 text-leaf-400" /> Same-day appointments
              </span>
            </div>
          </div>

          {/* Floating stat card */}
          <div className="lg:col-span-5">
            <div className="relative mx-auto max-w-sm">
              <div className="animate-float rounded-3xl border border-white/10 bg-white/[0.06] p-6 backdrop-blur-md">
                <div className="flex items-center gap-3">
                  <div className="flex">
                    {Array.from({ length: 5 }).map((_, i) => (
                      <StarIcon key={i} className="h-5 w-5 text-leaf-400" />
                    ))}
                  </div>
                  <span className="text-sm font-semibold">4.9 / 5</span>
                </div>
                <p className="mt-3 text-sm text-white/75">
                  “Fast, friendly and our home has been pest-free ever since.
                  Genuinely the best in the area.”
                </p>
                <p className="mt-3 text-xs font-semibold text-white/60">
                  — 380+ verified local reviews
                </p>

                <div className="mt-6 grid grid-cols-3 gap-3 border-t border-white/10 pt-5 text-center">
                  <div>
                    <p className="font-display text-2xl font-extrabold text-leaf-400">15+</p>
                    <p className="text-[11px] text-white/60">Years local</p>
                  </div>
                  <div>
                    <p className="font-display text-2xl font-extrabold text-leaf-400">12k</p>
                    <p className="text-[11px] text-white/60">Homes treated</p>
                  </div>
                  <div>
                    <p className="font-display text-2xl font-extrabold text-leaf-400">100%</p>
                    <p className="text-[11px] text-white/60">Guaranteed</p>
                  </div>
                </div>
              </div>
              <div className="absolute -bottom-5 -left-5 hidden rounded-2xl bg-leaf-500 px-5 py-4 text-forest-950 shadow-xl sm:block">
                <ShieldIcon className="h-7 w-7" />
                <p className="mt-1 text-xs font-bold leading-tight">Licensed &<br />insured</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <TrustBadges />

      {/* ───────────────────── Intro / About ───────────────────── */}
      <section className="bg-white py-20 sm:py-24">
        <div className="container-px grid items-center gap-12 lg:grid-cols-2">
          <div className="relative">
            <div className="overflow-hidden rounded-[2rem] shadow-xl shadow-forest-900/10">
              <SmartImage src={IMG.family} alt="A clean, healthy family home" className="aspect-[4/3] w-full" />
            </div>
            <div className="absolute -bottom-6 -right-4 w-48 rounded-2xl border border-sand bg-white p-5 shadow-lg sm:right-6">
              <div className="flex -space-x-2">
                {["S", "J", "P", "M"].map((l) => (
                  <span key={l} className="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-brand-600/15 text-xs font-bold text-brand-700">
                    {l}
                  </span>
                ))}
              </div>
              <p className="mt-3 text-sm font-bold text-forest-900">Loved by locals</p>
              <p className="text-xs text-muted">12,000+ homes & businesses</p>
            </div>
          </div>

          <div>
            <span className="eyebrow">Committed to your home</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Safe and effective pest control, done properly
            </h2>
            <p className="mt-5 text-muted">
              We&apos;re a family-owned local business that treats your home like
              our own. Every job starts with a proper inspection — so we treat
              the source of the problem, not just the symptoms.
            </p>
            <ul className="mt-7 grid gap-4 sm:grid-cols-2">
              {[
                "Licensed, insured technicians",
                "Low-toxic, pet-safe products",
                "Upfront, honest pricing",
                "Free re-treatment warranty",
                "Carpet cleaning available",
                "Friendly, on-time service",
              ].map((f) => (
                <li key={f} className="flex items-start gap-3">
                  <span className="mt-0.5 grid h-6 w-6 shrink-0 place-items-center rounded-full bg-brand-600/10 text-brand-700">
                    <CheckIcon className="h-4 w-4" />
                  </span>
                  <span className="text-sm font-medium text-forest-900">{f}</span>
                </li>
              ))}
            </ul>
            <Link href="/about" className="btn-ghost mt-8">
              More about us <ArrowRight className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </section>

      {/* ───────────────────── Services grid ───────────────────── */}
      <section className="bg-cream py-20 sm:py-24">
        <div className="container-px">
          <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
            <div className="max-w-xl">
              <span className="eyebrow">What we treat</span>
              <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
                Complete pest control solutions
              </h2>
              <p className="mt-4 text-muted">
                One trusted team for every pest — plus professional carpet
                cleaning to keep your home fresh and healthy.
              </p>
            </div>
            <Link href="/services" className="btn-primary shrink-0">
              View all services <ArrowRight className="h-4 w-4" />
            </Link>
          </div>

          <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {pests.map((p) => (
              <Link
                key={p.slug}
                href="/services"
                className="card card-hover group flex flex-col"
              >
                <span className="grid h-14 w-14 place-items-center rounded-2xl bg-brand-600/10 text-brand-700 transition-colors group-hover:bg-brand-600 group-hover:text-white">
                  <PestIcon name={p.icon} className="h-8 w-8" />
                </span>
                <h3 className="mt-5 text-lg font-bold text-forest-900">{p.name}</h3>
                <p className="mt-2 flex-1 text-sm leading-relaxed text-muted">{p.blurb}</p>
                <span className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700">
                  Learn more <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" />
                </span>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* ───────────────── Why choose us (dark band) ───────────── */}
      <section className="relative overflow-hidden bg-forest-900 py-20 text-white sm:py-24">
        <LeafIcon className="pointer-events-none absolute -right-12 -top-12 h-72 w-72 text-white/[0.04]" />
        <div className="container-px grid items-center gap-12 lg:grid-cols-2">
          <div>
            <span className="inline-flex items-center gap-2 rounded-full bg-leaf-400/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-leaf-400">
              Why choose us
            </span>
            <h2 className="mt-4 text-3xl font-extrabold text-white sm:text-4xl">
              Local experts you can rely on
            </h2>
            <p className="mt-5 text-white/75">
              We&apos;ve protected thousands of homes across {site.area}. Our
              technicians are accredited, our products are safe, and our work is
              always backed by a guarantee.
            </p>
            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
              <Link href="/contact" className="btn-leaf">Book an inspection</Link>
              <Link href="/services" className="btn-outline">Explore services</Link>
            </div>
          </div>

          <div className="grid gap-5 sm:grid-cols-2">
            {[
              { k: "15+", v: "Years serving the local community" },
              { k: "12k+", v: "Homes & businesses protected" },
              { k: "4.9★", v: "Average rating from 380+ reviews" },
              { k: "100%", v: "Satisfaction guarantee on every job" },
            ].map((s) => (
              <div key={s.k} className="rounded-3xl border border-white/10 bg-white/[0.04] p-6">
                <p className="font-display text-4xl font-extrabold text-leaf-400">{s.k}</p>
                <p className="mt-2 text-sm text-white/70">{s.v}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Process />
      <Testimonials />
      <FAQ />
    </>
  );
}
