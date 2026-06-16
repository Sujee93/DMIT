import Link from "next/link";
import { LeafIcon } from "./icons";

export default function PageHero({
  eyebrow,
  title,
  subtitle,
  crumb,
}: {
  eyebrow: string;
  title: string;
  subtitle: string;
  crumb: string;
}) {
  return (
    <section className="relative overflow-hidden bg-forest-950 text-white">
      <div className="absolute inset-0 leaf-pattern opacity-[0.4]" />
      <LeafIcon className="pointer-events-none absolute -right-10 -top-10 h-64 w-64 text-white/[0.04]" />
      <LeafIcon className="pointer-events-none absolute -left-16 bottom-0 h-72 w-72 rotate-180 text-white/[0.03]" />

      <div className="container-px relative py-16 sm:py-20">
        <nav className="flex items-center gap-2 text-xs font-medium text-white/50">
          <Link href="/" className="hover:text-leaf-400">Home</Link>
          <span>/</span>
          <span className="text-leaf-400">{crumb}</span>
        </nav>

        <span className="mt-6 inline-flex items-center gap-2 rounded-full border border-leaf-400/30 bg-leaf-400/10 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.18em] text-leaf-400">
          {eyebrow}
        </span>
        <h1 className="mt-4 max-w-3xl text-balance text-4xl font-extrabold leading-tight text-white sm:text-5xl lg:text-6xl">
          {title}
        </h1>
        <p className="mt-5 max-w-xl text-lg text-white/70">{subtitle}</p>
      </div>
    </section>
  );
}
