# Healthy Homes — Carpets & Pest Control

A modern redesign of the [Healthy Homes Pest Control](https://healthyhomespestcontrol.com.au/)
website for a pest control & carpet cleaning business in Australia.

Built with **Next.js 16 (App Router)**, **React 19**, **Tailwind CSS v4** and
**TypeScript**. Fully responsive, accessible and statically rendered.

## Pages

| Route        | Page                                                        |
| ------------ | ----------------------------------------------------------- |
| `/`          | Home — hero, services, why-us, process, reviews, FAQ        |
| `/about`     | About — story, values, accreditation                       |
| `/services`  | **Pest control service template** — services, featured deep-dives, pricing plans, process, guarantee, FAQ |
| `/contact`   | Contact — details + quote-request form                     |

## Design

- **Green eco palette** drawn from the leaf logo — fresh greens, forest darks
  and warm creams (defined as Tailwind tokens in `src/app/globals.css`).
- **Fonts:** Plus Jakarta Sans (display) + Inter (body).
- **Iconography:** hand-built inline SVG pest icons (`src/components/icons.tsx`)
  so the design holds up even without photography.
- **Imagery:** `SmartImage` shows a branded gradient fallback if a photo can't
  load, so the client can drop in real photos without touching layout.

## Getting started

```bash
npm install
npm run dev      # http://localhost:3000
npm run build    # production build
npm run start    # serve the production build
```

## Before going live — client to-dos

Search the codebase for these placeholders and swap in real values:

1. **Contact details** — phone, email, address, ABN, service area and hours in
   `src/lib/site.ts`.
2. **Photography** — replace the Unsplash URLs (in `page.tsx`, `about`, and
   `services`) with the client's own photos.
3. **Contact form** — `src/components/ContactForm.tsx` currently shows a success
   state only. Wire `handleSubmit` to an email service / form backend (e.g.
   Formspree) or CRM.
4. **Google Map** — embed a real map in the placeholder on the Contact page.
5. **Pricing** — confirm the indicative figures on the Services page.

## Structure

```
src/
  app/            # routes (home, about, services, contact) + layout & globals
  components/     # Header, Footer, Logo, sections, icons, form
  lib/site.ts     # central site config (contact details, nav, services)
```
