import type { Metadata } from "next";
import PageHero from "@/components/PageHero";
import ContactForm from "@/components/ContactForm";
import { site } from "@/lib/site";
import {
  PhoneIcon,
  MailIcon,
  PinIcon,
  ClockIcon,
  ShieldIcon,
  LeafIcon,
} from "@/components/icons";

export const metadata: Metadata = {
  title: "Contact Us",
  description:
    "Get a free, no-obligation pest control quote. Call Healthy Homes or send a message and our local team will be in touch the same day.",
};

const details = [
  { icon: PhoneIcon, label: "Call us", value: site.phoneDisplay, href: site.phoneHref },
  { icon: MailIcon, label: "Email", value: site.email, href: `mailto:${site.email}` },
  { icon: PinIcon, label: "Service area", value: `Servicing ${site.area}` },
  { icon: ClockIcon, label: "Opening hours", value: site.hours },
];

export default function ContactPage() {
  return (
    <>
      <PageHero
        crumb="Contact"
        eyebrow="Get in touch"
        title="Book your free quote today"
        subtitle="Tell us what's bugging you and our friendly local team will get back to you the same day with honest advice and upfront pricing."
      />

      <section className="bg-white py-20 sm:py-24">
        <div className="container-px grid gap-12 lg:grid-cols-12">
          {/* Left: details */}
          <div className="lg:col-span-5">
            <span className="eyebrow">We&apos;d love to help</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Speak to a local expert
            </h2>
            <p className="mt-4 text-muted">
              Prefer to talk it through? Give us a call — or fill out the form and
              we&apos;ll come back to you fast.
            </p>

            <div className="mt-8 grid gap-4 sm:grid-cols-2">
              {details.map((d) => {
                const Inner = (
                  <div className="flex items-start gap-4 rounded-2xl border border-sand bg-cream p-5 transition hover:border-brand-500/40">
                    <span className="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-600/10 text-brand-700">
                      <d.icon className="h-6 w-6" />
                    </span>
                    <div>
                      <p className="text-xs font-semibold uppercase tracking-wider text-muted">{d.label}</p>
                      <p className="mt-0.5 text-sm font-bold text-forest-900">{d.value}</p>
                    </div>
                  </div>
                );
                return d.href ? (
                  <a key={d.label} href={d.href} className="block">{Inner}</a>
                ) : (
                  <div key={d.label}>{Inner}</div>
                );
              })}
            </div>

            {/* Reassurance card */}
            <div className="mt-8 overflow-hidden rounded-3xl bg-forest-900 p-7 text-white">
              <div className="flex items-center gap-3">
                <span className="grid h-11 w-11 place-items-center rounded-xl bg-leaf-400/15 text-leaf-400">
                  <ShieldIcon className="h-6 w-6" />
                </span>
                <p className="font-display text-lg font-bold">Our promise to you</p>
              </div>
              <ul className="mt-5 space-y-3 text-sm text-white/80">
                {[
                  "Free, no-obligation quotes",
                  "Same-day response",
                  "Licensed & fully insured",
                  "100% satisfaction guarantee",
                ].map((t) => (
                  <li key={t} className="flex items-center gap-2.5">
                    <LeafIcon className="h-5 w-5 text-leaf-400" /> {t}
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Right: form */}
          <div className="lg:col-span-7">
            <ContactForm />
          </div>
        </div>
      </section>

      {/* Map placeholder band */}
      <section className="bg-cream py-16">
        <div className="container-px">
          <div className="relative grid h-64 place-items-center overflow-hidden rounded-3xl border border-sand bg-forest-800 leaf-pattern sm:h-80">
            <div className="text-center text-white">
              <PinIcon className="mx-auto h-10 w-10 text-leaf-400" />
              <p className="mt-3 font-display text-lg font-bold">Servicing {site.area}</p>
              <p className="text-sm text-white/70">
                Embed your Google Map here to show your service area.
              </p>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
