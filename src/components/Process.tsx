const steps = [
  {
    n: "01",
    title: "Book your inspection",
    body: "Call or request a quote online. We'll lock in a time that suits you — often same day.",
  },
  {
    n: "02",
    title: "Thorough inspection",
    body: "Our technician identifies the pest, the source and any entry points around your property.",
  },
  {
    n: "03",
    title: "Targeted treatment",
    body: "We apply safe, low-toxic treatments tailored to your home, pets and family.",
  },
  {
    n: "04",
    title: "Protect & follow up",
    body: "Prevention advice, proofing and a warranty — with free re-treatment if pests return.",
  },
];

export default function Process() {
  return (
    <section className="bg-white py-20 sm:py-24">
      <div className="container-px">
        <div className="mx-auto max-w-2xl text-center">
          <span className="eyebrow">How we work</span>
          <h2 className="mt-4 text-3xl font-extrabold sm:text-4xl">
            A simple, proven process
          </h2>
          <p className="mt-4 text-muted">
            From first call to a pest-free home — clear steps, no surprises, no
            jargon.
          </p>
        </div>

        <div className="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          {steps.map((s, i) => (
            <div key={s.n} className="relative">
              <div className="card card-hover h-full">
                <span className="font-display text-4xl font-extrabold text-brand-600/15">
                  {s.n}
                </span>
                <h3 className="mt-3 text-lg font-bold text-forest-900">{s.title}</h3>
                <p className="mt-2 text-sm leading-relaxed text-muted">{s.body}</p>
              </div>
              {i < steps.length - 1 && (
                <div className="absolute -right-3 top-1/2 hidden h-px w-6 bg-brand-600/30 lg:block" />
              )}
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
