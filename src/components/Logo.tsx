import Link from "next/link";

export default function Logo({
  variant = "dark",
  className = "",
}: {
  variant?: "dark" | "light";
  className?: string;
}) {
  const main = variant === "light" ? "#ffffff" : "#2f6f2c";
  const sub = variant === "light" ? "rgba(255,255,255,0.75)" : "#4aa33d";

  return (
    <Link
      href="/"
      aria-label="Healthy Homes — Carpets & Pest Control, home"
      className={`group inline-flex items-center gap-3 ${className}`}
    >
      {/* Leaf mark */}
      <span className="relative grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-brand-600/10 transition-transform duration-300 group-hover:-rotate-6">
        <svg viewBox="0 0 24 24" className="h-7 w-7" aria-hidden="true">
          <path
            d="M4 20C3 12 9 4 20 4c0 11-8 17-16 16Z"
            fill="#84c43e"
          />
          <path
            d="M11 17C9 13 11 8 16 6"
            fill="none"
            stroke="#2f6f2c"
            strokeWidth="1.6"
            strokeLinecap="round"
          />
          <path
            d="M16 4.5C18 3 20.5 3.2 22 4c-.3 2.2-1.8 4-4 4.4"
            fill="#6aae2a"
          />
        </svg>
      </span>

      {/* Wordmark */}
      <span className="flex flex-col leading-none">
        <span
          className="font-display text-[1.35rem] font-extrabold tracking-tight"
          style={{ color: main }}
        >
          Healthy Homes
        </span>
        <span
          className="text-[0.62rem] font-semibold uppercase tracking-[0.28em]"
          style={{ color: sub }}
        >
          Carpets &amp; Pest Control
        </span>
      </span>
    </Link>
  );
}
