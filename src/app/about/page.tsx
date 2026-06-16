import type { Metadata } from "next";
import Link from "next/link";
import PageHero from "@/components/PageHero";
import SmartImage from "@/components/SmartImage";
import Testimonials from "@/components/Testimonials";
import { site } from "@/lib/site";
import {
  CheckIcon,
  LeafIcon,
  ShieldIcon,
  HouseHeart,
  SparkleIcon,
  ArrowRight,
} from "@/components/icons";

export const metadata: Metadata = {
  title: "About Us",
  description:
    "Healthy Homes is a family-owned carpet cleaning and pest control business serving Sydney with safe, effective and guaranteed treatments.",
};

const values = [
  {
    icon: LeafIcon,
    title: "Safe by default",
    body: "We choose low-toxic, family- and pet-friendly products and apply them responsibly — your wellbeing comes first.",
  },
  {
    icon: ShieldIcon,
    title: "Always accountable",
    body: "Licensed, insured and guarantee-backed. If covered pests come back within warranty, so do we — free of charge.",
  },
  {
    icon: HouseHeart,
    title: "Genuinely local",
    body: "We live and work here too. You'll get honest advice and friendly faces, not a faceless call centre.",
  },
  {
    icon: SparkleIcon,
    title: "Done properly",
    body: "We treat the source, not just the symptom — and we leave your home cleaner than we found it.",
  },
];

const IMG = {
  team: "https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1100&q=80",
};

export default function AboutPage() {
  return (
    <>
      <PageHero
        crumb="About"
        eyebrow="Our story"
        title="A family business keeping local homes healthy"
        subtitle="For over 15 years we've helped families and businesses across our community live pest-free — safely, honestly and with care."
      />

      {/* Story */}
      <section className="bg-white py-20 sm:py-24">
        <div className="container-px grid items-center gap-12 lg:grid-cols-2">
          <div className="relative order-2 lg:order-1">
            <div className="overflow-hidden rounded-[2rem] shadow-xl shadow-forest-900/10">
              <SmartImage src={IMG.team} alt="The Healthy Homes team" className="aspect-[4/3] w-full" />
            </div>
            <div className="absolute -right-3 -top-5 rounded-2xl bg-brand-600 px-5 py-4 text-white shadow-xl sm:-right-6">
              <p className="font-display text-3xl font-extrabold">15+</p>
              <p className="text-xs text-white/80">Years local</p>
            </div>
          </div>

          <div className="order-1 lg:order-2">
            <span className="eyebrow">Who we are</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Built on trust, one home at a time
            </h2>
            <div className="mt-5 space-y-4 text-muted">
              <p>
                Healthy Homes started with a simple belief: pest control should
                be safe, effective and something you never have to think twice
                about. What began as a one-van operation has grown into a trusted
                local team — but our values haven&apos;t changed.
              </p>
              <p>
                Because we&apos;re carpets <em>and</em> pest control, we see your
                home as a whole. From a termite barrier to a deep steam clean
                before a new bub arrives, we bring the same care, honesty and
                attention to detail to every single job.
              </p>
              <p>
                Today we look after thousands of homes and businesses across{" "}
                {site.area} — and we&apos;d love to look after yours.
              </p>
            </div>
            <Link href="/contact" className="btn-primary mt-8">
              Work with us <ArrowRight className="h-4 w-4" />
            </Link>
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="bg-cream py-20 sm:py-24">
        <div className="container-px">
          <div className="mx-auto max-w-2xl text-center">
            <span className="eyebrow">What we stand for</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Values that guide every visit
            </h2>
          </div>
          <div className="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            {values.map((v) => (
              <div key={v.title} className="card card-hover">
                <span className="grid h-14 w-14 place-items-center rounded-2xl bg-brand-600/10 text-brand-700">
                  <v.icon className="h-7 w-7" />
                </span>
                <h3 className="mt-5 text-lg font-bold text-forest-900">{v.title}</h3>
                <p className="mt-2 text-sm leading-relaxed text-muted">{v.body}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Accreditation band */}
      <section className="relative overflow-hidden bg-forest-900 py-16 text-white">
        <div className="container-px grid items-center gap-10 lg:grid-cols-12">
          <div className="lg:col-span-5">
            <h2 className="text-3xl font-extrabold text-white sm:text-4xl">
              Qualified, insured &amp; accredited
            </h2>
            <p className="mt-4 text-white/70">
              Our technicians hold full pest management licences and ongoing
              accreditation, so you can be confident every treatment meets
              Australian safety standards.
            </p>
          </div>
          <div className="grid gap-4 sm:grid-cols-2 lg:col-span-7">
            {[
              "Licensed pest technicians",
              "Fully insured for your peace of mind",
              "Registered, approved products only",
              "Ongoing industry accreditation",
            ].map((t) => (
              <div key={t} className="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.04] px-5 py-4">
                <span className="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-leaf-400/15 text-leaf-400">
                  <CheckIcon className="h-5 w-5" />
                </span>
                <span className="text-sm font-medium text-white/85">{t}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Testimonials />
    </>
  );
}
