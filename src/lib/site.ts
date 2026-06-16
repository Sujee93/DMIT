/**
 * Central site config. Swap the PLACEHOLDER contact details below for the
 * client's real phone / email / address before going live.
 */
export const site = {
  name: "Healthy Homes",
  tagline: "Carpets & Pest Control",
  // TODO: replace with the client's real details
  phoneDisplay: "1300 456 789",
  phoneHref: "tel:1300456789",
  email: "hello@healthyhomespestcontrol.com.au",
  area: "Sydney & surrounds",
  hours: "Mon–Sat, 7am–6pm",
  abn: "ABN 00 000 000 000",
};

export const nav = [
  { label: "Home", href: "/" },
  { label: "About", href: "/about" },
  { label: "Services", href: "/services" },
  { label: "Contact", href: "/contact" },
];

export type Pest = {
  slug: string;
  name: string;
  icon: string;
  blurb: string;
};

export const pests: Pest[] = [
  { slug: "termites", name: "Termites", icon: "termite", blurb: "Colony detection, baiting & barrier protection for your home's biggest threat." },
  { slug: "cockroaches", name: "Cockroaches", icon: "cockroach", blurb: "Targeted gel & residual treatments that break the breeding cycle for good." },
  { slug: "ants", name: "Ants", icon: "ant", blurb: "Nest-focused control for black, coastal brown and white-footed house ants." },
  { slug: "spiders", name: "Spiders", icon: "spider", blurb: "Eave-to-skirting treatments for redbacks, huntsmen and webbing spiders." },
  { slug: "rodents", name: "Rats & Mice", icon: "rodent", blurb: "Tamper-proof baiting, proofing and entry-point sealing — pet & child safe." },
  { slug: "mosquitoes", name: "Mosquitoes", icon: "mosquito", blurb: "Yard fogging and breeding-site treatment so you can enjoy outdoors again." },
  { slug: "fleas", name: "Fleas & Ticks", icon: "flea", blurb: "Indoor & outdoor flea cycle treatments, ideal for pet households." },
  { slug: "carpets", name: "Carpet Cleaning", icon: "carpet", blurb: "Hot-water steam extraction that lifts allergens, stains and odours." },
];
