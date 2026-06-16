"use client";

import { useState } from "react";

type QA = { q: string; a: string };

const defaultFaqs: QA[] = [
  {
    q: "Are your pest control treatments safe for my family and pets?",
    a: "Yes. We use low-toxic, registered products applied by licensed technicians, and we follow strict safety guidelines. We'll let you know if there's anything to keep clear of and for how long — in most cases your home is safe to use very soon after treatment.",
  },
  {
    q: "How soon can you come out?",
    a: "For most jobs we offer same-day or next-day appointments across our service area. For urgent infestations, call us and we'll prioritise getting a technician to you as fast as possible.",
  },
  {
    q: "Do you guarantee your work?",
    a: "Absolutely. Our general pest treatments come with a service warranty — if covered pests return within the warranty period, we'll re-treat your home free of charge.",
  },
  {
    q: "How much does pest control cost?",
    a: "Pricing depends on the pest, the size of your property and the level of activity. We provide free, upfront quotes with no hidden fees — you'll know the exact cost before we start any work.",
  },
  {
    q: "Do you offer carpet cleaning as well?",
    a: "Yes — we're carpets and pest control. We offer hot-water steam extraction carpet cleaning, which pairs perfectly with flea treatments and end-of-lease cleans. Bundle both and save.",
  },
];

export default function FAQ({
  faqs = defaultFaqs,
  variant = "light",
}: {
  faqs?: QA[];
  variant?: "light" | "plain";
}) {
  const [open, setOpen] = useState<number | null>(0);

  return (
    <section className={variant === "light" ? "bg-white py-20 sm:py-24" : "py-4"}>
      <div className={variant === "light" ? "container-px" : ""}>
        <div className="grid gap-12 lg:grid-cols-12">
          <div className="lg:col-span-4">
            <span className="eyebrow">FAQ</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Questions, answered
            </h2>
            <p className="mt-4 text-muted">
              Can&apos;t find what you&apos;re looking for? Our team is happy to
              help — just give us a call.
            </p>
          </div>

          <div className="lg:col-span-8">
            <div className="divide-y divide-sand overflow-hidden rounded-3xl border border-sand bg-white">
              {faqs.map((item, i) => {
                const isOpen = open === i;
                return (
                  <div key={item.q}>
                    <button
                      type="button"
                      onClick={() => setOpen(isOpen ? null : i)}
                      aria-expanded={isOpen}
                      className="flex w-full items-center justify-between gap-4 px-6 py-5 text-left"
                    >
                      <span className="font-display text-base font-bold text-forest-900">
                        {item.q}
                      </span>
                      <span
                        className={`grid h-7 w-7 shrink-0 place-items-center rounded-full border border-brand-600/30 text-brand-700 transition-transform duration-300 ${
                          isOpen ? "rotate-45 bg-brand-600 text-white" : ""
                        }`}
                      >
                        <svg viewBox="0 0 24 24" className="h-4 w-4" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round">
                          <path d="M12 5v14M5 12h14" />
                        </svg>
                      </span>
                    </button>
                    <div
                      className={`grid overflow-hidden px-6 transition-all duration-300 ${
                        isOpen ? "grid-rows-[1fr] pb-5 opacity-100" : "grid-rows-[0fr] opacity-0"
                      }`}
                    >
                      <p className="min-h-0 text-sm leading-relaxed text-muted">
                        {item.a}
                      </p>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
