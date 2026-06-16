import { ShieldIcon, LeafIcon, StarIcon, ClockIcon, HouseHeart } from "./icons";

const badges = [
  { icon: ShieldIcon, title: "Licensed & insured", sub: "Fully accredited technicians" },
  { icon: LeafIcon, title: "Family & pet safe", sub: "Low-toxic, eco-conscious products" },
  { icon: ClockIcon, title: "Same-day service", sub: "Fast response, on time" },
  { icon: HouseHeart, title: "100% guarantee", sub: "Free re-treatment if pests return" },
];

export default function TrustBadges() {
  return (
    <section className="border-y border-sand bg-cream">
      <div className="container-px grid grid-cols-2 gap-6 py-10 lg:grid-cols-4">
        {badges.map((b) => (
          <div key={b.title} className="flex items-center gap-4">
            <span className="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-600/10 text-brand-700">
              <b.icon className="h-6 w-6" />
            </span>
            <div>
              <p className="font-display text-sm font-bold text-forest-900">{b.title}</p>
              <p className="text-xs text-muted">{b.sub}</p>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

export function ReviewBadge() {
  return (
    <div className="inline-flex items-center gap-3 rounded-2xl border border-sand bg-white px-4 py-3 shadow-sm">
      <div className="flex">
        {Array.from({ length: 5 }).map((_, i) => (
          <StarIcon key={i} className="h-4 w-4 text-leaf-500" />
        ))}
      </div>
      <p className="text-sm font-semibold text-forest-900">
        4.9/5 <span className="font-normal text-muted">· 380+ reviews</span>
      </p>
    </div>
  );
}
