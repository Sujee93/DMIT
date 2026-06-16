"use client";

import { useState } from "react";
import { pests } from "@/lib/site";
import { CheckIcon, ArrowRight } from "./icons";

export default function ContactForm() {
  const [sent, setSent] = useState(false);

  // NOTE: This is a front-end demo handler. Wire it to your email service,
  // form backend (e.g. Formspree) or CRM before going live.
  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setSent(true);
  }

  if (sent) {
    return (
      <div className="flex flex-col items-center justify-center rounded-3xl border border-sand bg-cream p-10 text-center">
        <span className="grid h-16 w-16 place-items-center rounded-full bg-brand-600 text-white">
          <CheckIcon className="h-9 w-9" />
        </span>
        <h3 className="mt-5 text-2xl font-extrabold text-forest-900">Thanks — we&apos;re on it!</h3>
        <p className="mt-3 max-w-sm text-muted">
          Your request has been received. One of our team will be in touch
          shortly to confirm your free quote.
        </p>
        <button
          type="button"
          onClick={() => setSent(false)}
          className="btn-ghost mt-6"
        >
          Send another request
        </button>
      </div>
    );
  }

  const inputCls =
    "w-full rounded-xl border border-sand bg-white px-4 py-3 text-sm text-forest-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20";

  return (
    <form
      onSubmit={handleSubmit}
      className="rounded-3xl border border-sand bg-white p-7 shadow-sm sm:p-8"
    >
      <div className="grid gap-5 sm:grid-cols-2">
        <div className="sm:col-span-1">
          <label className="mb-1.5 block text-sm font-semibold text-forest-900">Full name</label>
          <input required name="name" placeholder="Jane Smith" className={inputCls} />
        </div>
        <div className="sm:col-span-1">
          <label className="mb-1.5 block text-sm font-semibold text-forest-900">Phone</label>
          <input required name="phone" type="tel" placeholder="0400 000 000" className={inputCls} />
        </div>
        <div className="sm:col-span-2">
          <label className="mb-1.5 block text-sm font-semibold text-forest-900">Email</label>
          <input required name="email" type="email" placeholder="jane@email.com" className={inputCls} />
        </div>
        <div className="sm:col-span-1">
          <label className="mb-1.5 block text-sm font-semibold text-forest-900">Suburb / postcode</label>
          <input name="suburb" placeholder="e.g. Parramatta 2150" className={inputCls} />
        </div>
        <div className="sm:col-span-1">
          <label className="mb-1.5 block text-sm font-semibold text-forest-900">Service needed</label>
          <select name="service" className={inputCls} defaultValue="">
            <option value="" disabled>Select a service…</option>
            {pests.map((p) => (
              <option key={p.slug} value={p.name}>{p.name}</option>
            ))}
            <option value="General pest">General pest treatment</option>
            <option value="Not sure">Not sure — need advice</option>
          </select>
        </div>
        <div className="sm:col-span-2">
          <label className="mb-1.5 block text-sm font-semibold text-forest-900">How can we help?</label>
          <textarea
            name="message"
            rows={4}
            placeholder="Tell us a little about the problem…"
            className={`${inputCls} resize-none`}
          />
        </div>
      </div>

      <button type="submit" className="btn-primary mt-6 w-full text-base">
        Request my free quote <ArrowRight className="h-4 w-4" />
      </button>
      <p className="mt-3 text-center text-xs text-muted">
        No obligation. We&apos;ll never share your details.
      </p>
    </form>
  );
}
