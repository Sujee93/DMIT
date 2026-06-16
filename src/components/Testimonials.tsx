import { StarIcon } from "./icons";

const reviews = [
  {
    quote:
      "Booked a termite inspection and they found activity we had no idea about. Professional, thorough and the treatment plan was clearly explained. Highly recommend.",
    name: "Sarah M.",
    place: "Castle Hill",
  },
  {
    quote:
      "Had a serious cockroach problem in our rental. Healthy Homes sorted it in one visit and we haven't seen one since. Friendly tech, great value.",
    name: "James T.",
    place: "Parramatta",
  },
  {
    quote:
      "They did our pest control and steam-cleaned the carpets for our end-of-lease. Got our full bond back. Couldn't be happier — will use again.",
    name: "Priya K.",
    place: "Bondi",
  },
];

export default function Testimonials() {
  return (
    <section className="bg-cream py-20 sm:py-24">
      <div className="container-px">
        <div className="flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
          <div className="max-w-xl">
            <span className="eyebrow">Reviews</span>
            <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
              Trusted by local families
            </h2>
          </div>
          <div className="flex items-center gap-2">
            <div className="flex">
              {Array.from({ length: 5 }).map((_, i) => (
                <StarIcon key={i} className="h-5 w-5 text-leaf-500" />
              ))}
            </div>
            <p className="text-sm font-semibold text-forest-900">
              4.9 average from 380+ reviews
            </p>
          </div>
        </div>

        <div className="mt-12 grid gap-6 md:grid-cols-3">
          {reviews.map((r) => (
            <figure
              key={r.name}
              className="flex h-full flex-col rounded-3xl border border-sand bg-white p-7"
            >
              <div className="flex">
                {Array.from({ length: 5 }).map((_, i) => (
                  <StarIcon key={i} className="h-4 w-4 text-leaf-500" />
                ))}
              </div>
              <blockquote className="mt-4 flex-1 text-sm leading-relaxed text-forest-900/80">
                “{r.quote}”
              </blockquote>
              <figcaption className="mt-6 flex items-center gap-3">
                <span className="grid h-10 w-10 place-items-center rounded-full bg-brand-600/10 font-display text-sm font-bold text-brand-700">
                  {r.name.charAt(0)}
                </span>
                <span>
                  <span className="block text-sm font-bold text-forest-900">{r.name}</span>
                  <span className="block text-xs text-muted">{r.place}</span>
                </span>
              </figcaption>
            </figure>
          ))}
        </div>
      </div>
    </section>
  );
}
