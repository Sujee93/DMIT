import type { SVGProps } from "react";

type IconProps = SVGProps<SVGSVGElement>;

const base = {
  fill: "none",
  stroke: "currentColor",
  strokeWidth: 1.7,
  strokeLinecap: "round" as const,
  strokeLinejoin: "round" as const,
  viewBox: "0 0 24 24",
};

export function LeafIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M4 20C3 12 9 4 20 4c0 11-8 17-16 16Z" />
      <path d="M4 20C8 14 12 10 18 7" />
    </svg>
  );
}

export function ShieldIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M12 3 5 6v6c0 4.5 3 7.5 7 9 4-1.5 7-4.5 7-9V6l-7-3Z" />
      <path d="m9.2 12 2 2 3.6-3.8" />
    </svg>
  );
}

export function PhoneIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M6.5 4h2l1.4 3.5-1.7 1.3a11 11 0 0 0 5 5l1.3-1.7L18 16.5v2a2 2 0 0 1-2.2 2A14.5 14.5 0 0 1 4 8.2 2 2 0 0 1 6 6Z" />
    </svg>
  );
}

export function MailIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <rect x="3" y="5" width="18" height="14" rx="2.5" />
      <path d="m4 7 8 5 8-5" />
    </svg>
  );
}

export function PinIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M12 21s7-5.5 7-11a7 7 0 1 0-14 0c0 5.5 7 11 7 11Z" />
      <circle cx="12" cy="10" r="2.5" />
    </svg>
  );
}

export function ClockIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <circle cx="12" cy="12" r="8.5" />
      <path d="M12 7.5V12l3 2" />
    </svg>
  );
}

export function CheckIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="m5 12.5 4.5 4.5L19 7" />
    </svg>
  );
}

export function StarIcon(props: IconProps) {
  return (
    <svg viewBox="0 0 24 24" fill="currentColor" {...props}>
      <path d="m12 3 2.5 5.7 6.2.5-4.7 4 1.4 6L12 16.9 6.6 19.2l1.4-6-4.7-4 6.2-.5L12 3Z" />
    </svg>
  );
}

export function ArrowRight(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M5 12h14M13 6l6 6-6 6" />
    </svg>
  );
}

export function MenuIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M4 7h16M4 12h16M4 17h16" />
    </svg>
  );
}

export function CloseIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M6 6l12 12M18 6 6 18" />
    </svg>
  );
}

export function SparkleIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M12 4v4M12 16v4M4 12h4M16 12h4" />
      <path d="m7 7 2 2M15 15l2 2M17 7l-2 2M9 15l-2 2" />
    </svg>
  );
}

export function HouseHeart(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M4 11 12 4l8 7" />
      <path d="M6 10v9h12v-9" />
      <path d="M12 16.5c-2-1.3-2.8-2.4-2.8-3.4a1.4 1.4 0 0 1 2.8-.5 1.4 1.4 0 0 1 2.8.5c0 1-.8 2.1-2.8 3.4Z" />
    </svg>
  );
}

/* ---------------- Pest type icons ---------------- */

export function TermiteIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <ellipse cx="13" cy="13" rx="5" ry="3" />
      <circle cx="7" cy="12" r="2.2" />
      <path d="M5.2 11 3 9.5M5.2 13 3 14.5" />
      <path d="M9 14.5 8 17M12 15.5l-.5 2.5M15 15l.7 2.5" />
      <path d="M9 11.5 8 9M12 10.5l-.5-2.5M15 11l.7-2.5" />
    </svg>
  );
}

export function CockroachIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <ellipse cx="12" cy="13" rx="4.5" ry="6" />
      <path d="M12 7c0-2 1-3 1-3M12 7c0-2-1-3-1-3" />
      <path d="M7.5 10 4 8M16.5 10 20 8M7.5 13H3.5M16.5 13H20.5M7.8 16 5 18M16.2 16 19 18" />
    </svg>
  );
}

export function SpiderIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <circle cx="12" cy="12" r="3" />
      <path d="M9 11 4 8M9 13l-5 3M15 11l5-3M15 13l5 3" />
      <path d="M10 9.5 7 5M14 9.5 17 5M10 14.5 7 19M14 14.5 17 19" />
    </svg>
  );
}

export function RodentIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M14 16a4 4 0 1 0 0-7 5 5 0 0 0-5-3 3 3 0 0 0 0 6" />
      <circle cx="7" cy="9" r="2" />
      <circle cx="6.4" cy="9" r="0.4" fill="currentColor" />
      <path d="M14 16c3 0 5 1 6.5 3" />
    </svg>
  );
}

export function AntIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <circle cx="12" cy="6" r="2" />
      <circle cx="12" cy="11.5" r="1.8" />
      <ellipse cx="12" cy="17.5" rx="2.4" ry="3" />
      <path d="M11 4.5 9.5 3M13 4.5 14.5 3" />
      <path d="M10.5 11 6.5 9M13.5 11l4-2M10.5 12.5l-4 1.5M13.5 12.5l4 1.5" />
    </svg>
  );
}

export function MosquitoIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <circle cx="9" cy="9" r="2" />
      <path d="M9 11v4M9 15l-2 3M9 15l2 2.5" />
      <path d="M10.5 8 19 5M10.5 10l6 4" />
      <path d="M11 7c3-1 6 0 8 3M11 7c2.5.5 4.5 2 5.5 4.5" />
    </svg>
  );
}

export function FleaIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <path d="M9 18c-1-4 1-8 5-10" />
      <circle cx="9" cy="17" r="2.4" />
      <path d="M14 8c1.5-1 2.5-3 2-5M14 8c1.5.5 3 0 4-1.5" />
      <path d="M8 15 5 13M9 19l-1 2M11 17l1 2" />
    </svg>
  );
}

export function CarpetIcon(props: IconProps) {
  return (
    <svg {...base} {...props}>
      <rect x="3.5" y="6" width="17" height="11" rx="1.5" />
      <path d="M3.5 17c0 1 5 1 8.5 1s8.5 0 8.5-1" />
      <path d="M7 9h10M7 11.5h10M7 14h6" />
    </svg>
  );
}
