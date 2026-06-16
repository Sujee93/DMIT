import type { Metadata } from "next";
import Link from "next/link";
import PageHero from "@/components/PageHero";
import PestIcon from "@/components/PestIcon";
import SmartImage from "@/components/SmartImage";
import Process from "@/components/Process";
import FAQ from "@/components/FAQ";
import { pests, site } from "@/lib/site";
import {
  ArrowRight,
  CheckIcon,
  PhoneIcon,
  ShieldIcon,
  LeafIcon,
  CarpetIcon,
  TermiteIcon,
} from "@/components/icons";

export const metadata: Metadata = {
  title: "Pest Control Services",
  description:
    "Termites, cockroaches, ants, spiders, rodents, mosquitoes, fleas and carpet cleaning. Safe, guaranteed pest control across Sydney with free quotes.",
};

const plans = [
  {
    name: "General Pest Treatment",
    tag: "Most popular",
    price: "from $180",
    blurb: "Full interior & exterior treatment for common household pests.",
    features: [
      "Cockroaches, ants & spiders",
      "Interior, exterior & roof void",
      "Safe, low-toxic products",
      "6-month service warranty",
    ],
    featured: true,
  },
  {
    name: "Termite Protection",
    tag: "Home defence",
    price: "from $250",
    blurb: "Inspection, baiting and barrier protection for your biggest threat.",
    features: [
      "Detailed termite inspection",
      "Baiting & barrier options",
      "Detailed written report",
      "Annual monitoring available",
    ],
    featured: false,
  },
  {
    name: "End-of-Lease Combo",
    tag: "Best value",
    price: "from $320",
    blurb: "Pest treatment plus carpet steam clean — ideal for moving out.",
    features: [
      "Full general pest treatment",
      "Hot-water carpet steam clean",
      "Flea treatment included",
      "Bond-back friendly receipt",
    ],
    featured: false,
  },
];

const IMG = {
  termite:
    "https://images.unsplash.com/photo-1558036117-15d82a90b9b1?auto=format&fit=crop&w=1000&q=80",
  carpet:
    "https://images.unsplash.com/photo-1600585152220-90363fe7e115?auto=format&fit=crop&w=1000&q=80",
};

export default function ServicesPage() {
  return (
    <>
      <PageHero
        crumb="Services"
        eyebrow="Our services"
        title="Pest control & carpet cleaning, done right"
        subtitle="Safe, thorough and guaranteed treatments for every pest — backed by friendly local technicians and honest, upfront pricing."
      />

      {/* Quick value strip */}
      <section className="border-b border-sand bg-white">
        <div className="container-px grid gap-6 py-8 sm:grid-cols-3">
          {[
            { icon: ShieldIcon, t: "Guaranteed results", s: "Free re-treatment if pests return" },
            { icon: LeafIcon, t: "Family & pet safe", s: "Low-toxic, registered products" },
            { icon: PhoneIcon, t: "Free quotes", s: "Upfront pricing, no surprises" },
          ].map((b) => (
            <div key={b.t} className="flex items-center gap-4">
              <span className="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-600/10 text-brand-700">
                <b.icon className="h-6 w-6" />
              </span>
              <div>
                <p className="font-display text-sm font-bold text-forest-900">{b.t}</p>
                <p className="text-xs text-muted">{b.s}</p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* All services grid */}
      <section className="bg-cream py-20 sm:py-24">
        <div className="container-px">
          <div className="mx-auto max-w-2xl text-center">
            <span className="eyebrow">What we treat</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              One team for every pest in your home
            </h2>
            <p className="mt-4 text-muted">
              From termites to fleas — and a professional carpet clean to finish.
              Select a service or call us for tailored advice.
            </p>
          </div>

          <div className="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {pests.map((p) => (
              <div key={p.slug} className="card card-hover group flex flex-col">
                <span className="grid h-14 w-14 place-items-center rounded-2xl bg-brand-600/10 text-brand-700 transition-colors group-hover:bg-brand-600 group-hover:text-white">
                  <PestIcon name={p.icon} className="h-8 w-8" />
                </span>
                <h3 className="mt-5 text-lg font-bold text-forest-900">{p.name}</h3>
                <p className="mt-2 flex-1 text-sm leading-relaxed text-muted">{p.blurb}</p>
                <Link
                  href="/contact"
                  className="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-700"
                >
                  Book this service{" "}
                  <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" />
                </Link>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Featured: Termites */}
      <section className="bg-white py-20 sm:py-24">
        <div className="container-px grid items-center gap-12 lg:grid-cols-2">
          <div className="relative">
            <div className="overflow-hidden rounded-[2rem] shadow-xl shadow-forest-900/10">
              <SmartImage src={IMG.termite} alt="Termite inspection and treatment" className="aspect-[4/3] w-full" />
            </div>
            <div className="absolute -bottom-6 left-6 flex items-center gap-3 rounded-2xl border border-sand bg-white px-5 py-4 shadow-lg">
              <span className="grid h-11 w-11 place-items-center rounded-xl bg-brand-600/10 text-brand-700">
                <TermiteIcon className="h-7 w-7" />
              </span>
              <div>
                <p className="text-sm font-bold text-forest-900">1 in 3 homes</p>
                <p className="text-xs text-muted">affected by termites</p>
              </div>
            </div>
          </div>

          <div>
            <span className="eyebrow">Featured service</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Termite inspection &amp; protection
            </h2>
            <p className="mt-5 text-muted">
              Termites cause more damage to Australian homes than fire, floods
              and storms combined — and home insurance rarely covers it. Our
              thorough inspections and tailored protection plans stop them before
              they cost you thousands.
            </p>

            <div className="mt-7 grid gap-3 sm:grid-cols-2">
              {[
                "Comprehensive property inspection",
                "Thermal & moisture detection",
                "Baiting & chemical barriers",
                "Detailed written report",
                "Pre-purchase inspections",
                "Annual monitoring plans",
              ].map((f) => (
                <div key={f} className="flex items-start gap-2.5">
                  <CheckIcon className="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                  <span className="text-sm font-medium text-forest-900">{f}</span>
                </div>
              ))}
            </div>

            <div className="mt-8 flex flex-col gap-3 sm:flex-row">
              <Link href="/contact" className="btn-primary">Book an inspection</Link>
              <a href={site.phoneHref} className="btn-ghost">
                <PhoneIcon className="h-4 w-4" /> {site.phoneDisplay}
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Featured: Carpet cleaning (reversed) */}
      <section className="bg-cream py-20 sm:py-24">
        <div className="container-px grid items-center gap-12 lg:grid-cols-2">
          <div className="order-1">
            <span className="eyebrow">Also available</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Professional carpet steam cleaning
            </h2>
            <p className="mt-5 text-muted">
              Our hot-water extraction lifts deep-set dirt, allergens, stains and
              odours — leaving carpets fresher, healthier and faster-drying.
              Perfect alongside a flea treatment or end-of-lease clean.
            </p>
            <div className="mt-7 grid gap-3 sm:grid-cols-2">
              {[
                "Deep hot-water extraction",
                "Stain & odour treatment",
                "Allergen & dust-mite removal",
                "Fast-drying process",
                "Rugs & upholstery too",
                "Bond-clean friendly",
              ].map((f) => (
                <div key={f} className="flex items-start gap-2.5">
                  <CheckIcon className="mt-0.5 h-5 w-5 shrink-0 text-brand-600" />
                  <span className="text-sm font-medium text-forest-900">{f}</span>
                </div>
              ))}
            </div>
            <Link href="/contact" className="btn-primary mt-8">
              Get a carpet quote <ArrowRight className="h-4 w-4" />
            </Link>
          </div>

          <div className="relative order-0 lg:order-2">
            <div className="overflow-hidden rounded-[2rem] shadow-xl shadow-forest-900/10">
              <SmartImage src={IMG.carpet} alt="Professional carpet steam cleaning" className="aspect-[4/3] w-full" />
            </div>
            <div className="absolute -top-5 right-6 flex items-center gap-3 rounded-2xl bg-brand-600 px-5 py-4 text-white shadow-xl">
              <CarpetIcon className="h-7 w-7" />
              <p className="text-sm font-bold leading-tight">Carpets &<br />pest in one visit</p>
            </div>
          </div>
        </div>
      </section>

      {/* Pricing / plans */}
      <section className="bg-white py-20 sm:py-24">
        <div className="container-px">
          <div className="mx-auto max-w-2xl text-center">
            <span className="eyebrow">Treatment plans</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Simple, upfront pricing
            </h2>
            <p className="mt-4 text-muted">
              Indicative pricing for a standard home — every quote is tailored to
              your property and confirmed before we begin. No hidden fees, ever.
            </p>
          </div>

          <div className="mt-12 grid gap-6 lg:grid-cols-3">
            {plans.map((plan) => (
              <div
                key={plan.name}
                className={`relative flex flex-col rounded-3xl p-8 transition-all ${
                  plan.featured
                    ? "bg-forest-900 text-white shadow-2xl shadow-forest-900/20 lg:-translate-y-3"
                    : "border border-sand bg-white"
                }`}
              >
                <span
                  className={`inline-flex w-fit items-center gap-1 rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider ${
                    plan.featured ? "bg-leaf-400 text-forest-950" : "bg-brand-600/10 text-brand-700"
                  }`}
                >
                  {plan.tag}
                </span>
                <h3 className={`mt-5 text-xl font-extrabold ${plan.featured ? "text-white" : "text-forest-900"}`}>
                  {plan.name}
                </h3>
                <p className={`mt-2 text-sm ${plan.featured ? "text-white/70" : "text-muted"}`}>
                  {plan.blurb}
                </p>
                <p className={`mt-5 font-display text-3xl font-extrabold ${plan.featured ? "text-leaf-400" : "text-brand-700"}`}>
                  {plan.price}
                </p>

                <ul className="mt-6 flex-1 space-y-3">
                  {plan.features.map((f) => (
                    <li key={f} className="flex items-start gap-2.5">
                      <CheckIcon className={`mt-0.5 h-5 w-5 shrink-0 ${plan.featured ? "text-leaf-400" : "text-brand-600"}`} />
                      <span className={`text-sm ${plan.featured ? "text-white/85" : "text-forest-900"}`}>{f}</span>
                    </li>
                  ))}
                </ul>

                <Link
                  href="/contact"
                  className={`mt-8 ${plan.featured ? "btn-leaf" : "btn-ghost"} w-full`}
                >
                  Get a quote
                </Link>
              </div>
            ))}
          </div>

          <p className="mt-8 text-center text-xs text-muted">
            Prices are a guide only and may vary with property size, pest type
            and severity. Contact us for an exact, no-obligation quote.
          </p>
        </div>
      </section>

      <Process />

      {/* Guarantee band */}
      <section className="bg-forest-950 py-16 text-white">
        <div className="container-px flex flex-col items-center gap-6 text-center">
          <span className="grid h-16 w-16 place-items-center rounded-2xl bg-leaf-400/15 text-leaf-400">
            <ShieldIcon className="h-9 w-9" />
          </span>
          <h2 className="max-w-2xl text-3xl font-extrabold text-white sm:text-4xl">
            Backed by our 100% satisfaction guarantee
          </h2>
          <p className="max-w-xl text-white/70">
            If covered pests return within your warranty period, we&apos;ll come
            back and re-treat your home free of charge. That&apos;s our promise.
          </p>
          <div className="mt-2 flex flex-col gap-3 sm:flex-row">
            <Link href="/contact" className="btn-leaf">Get a free quote</Link>
            <a href={site.phoneHref} className="btn-outline">
              <PhoneIcon className="h-4 w-4" /> Call {site.phoneDisplay}
            </a>
          </div>
        </div>
      </section>

      <FAQ />
    </>
  );
}
