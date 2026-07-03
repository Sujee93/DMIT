import type { SVGProps } from 'react'

type IconProps = SVGProps<SVGSVGElement> & { size?: number }

function base(children: React.ReactNode, { size = 24, ...props }: IconProps) {
  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth={1.8}
      strokeLinecap="round"
      strokeLinejoin="round"
      {...props}
    >
      {children}
    </svg>
  )
}

export const HomeIcon = (p: IconProps) =>
  base(
    <>
      <path d="M3 11.5 12 4l9 7.5" />
      <path d="M5.5 10v9a1 1 0 0 0 1 1H9a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1v-9" />
    </>,
    p,
  )

export const ScaleIcon = (p: IconProps) =>
  base(
    <>
      <rect x="3" y="4" width="18" height="16" rx="4" />
      <circle cx="12" cy="13" r="3.2" />
      <path d="M12 13 13.6 11" />
      <path d="M9.5 7h5" />
    </>,
    p,
  )

export const PlateIcon = (p: IconProps) =>
  base(
    <>
      <circle cx="12" cy="12" r="9" />
      <circle cx="12" cy="12" r="4" />
    </>,
    p,
  )

export const DumbbellIcon = (p: IconProps) =>
  base(
    <>
      <rect x="2.5" y="9.5" width="3" height="5" rx="1" />
      <rect x="18.5" y="9.5" width="3" height="5" rx="1" />
      <path d="M5.5 12h1.5M17 12h1.5" />
      <rect x="7" y="8" width="2.4" height="8" rx="1" />
      <rect x="14.6" y="8" width="2.4" height="8" rx="1" />
      <path d="M9.4 12h5.2" />
    </>,
    p,
  )

export const FootprintsIcon = (p: IconProps) =>
  base(
    <>
      <path d="M8 4.5c1.7 0 2.5 1.4 2.5 3.4 0 1.6-.6 2.4-.9 3.3-.3.9-.2 2 .1 2.9.4 1.2-.3 2.4-1.7 2.4-1.6 0-2.5-1.2-2.5-3 0-1.5.4-2 .7-3 .3-1 .2-1.9-.1-2.8-.3-1.1.3-3.2 1.9-3.2Z" />
      <path d="M16 10.5c1.7 0 2.5 1.4 2.5 3.4 0 1.6-.6 2.4-.9 3.3-.3.9-.2 2 .1 2.9.4 1.2-.3 2.4-1.7 2.4-1.6 0-2.5-1.2-2.5-3 0-1.5.4-2 .7-3 .3-1 .2-1.9-.1-2.8-.3-1.1.3-3.2 1.9-3.2Z" />
    </>,
    p,
  )

export const SettingsIcon = (p: IconProps) =>
  base(
    <>
      <circle cx="12" cy="12" r="3" />
      <path d="M19.4 13.5a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.9 2.9l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V20a2 2 0 1 1-4 0v-.2a1.7 1.7 0 0 0-1.1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.9-2.9l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H4a2 2 0 1 1 0-4h.2a1.7 1.7 0 0 0 1.6-1.1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.9-2.9l.1.1a1.7 1.7 0 0 0 1.9.3H10a1.7 1.7 0 0 0 1-1.6V4a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.9 2.9l-.1.1a1.7 1.7 0 0 0-.3 1.9V10c.1.7.6 1.3 1.6 1.4h.2a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.6 1.1Z" />
    </>,
    p,
  )

export const CheckIcon = (p: IconProps) => base(<path d="M20 6 9 17l-5-5" />, p)
export const PlusIcon = (p: IconProps) => base(<path d="M12 5v14M5 12h14" />, p)
export const XIcon = (p: IconProps) => base(<path d="M18 6 6 18M6 6l12 12" />, p)
export const ChevronRightIcon = (p: IconProps) => base(<path d="m9 6 6 6-6 6" />, p)
export const TrashIcon = (p: IconProps) =>
  base(
    <>
      <path d="M4 7h16" />
      <path d="M6 7v13a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7M9 7V4.5A1.5 1.5 0 0 1 10.5 3h3A1.5 1.5 0 0 1 15 4.5V7" />
      <path d="M10 11v6M14 11v6" />
    </>,
    p,
  )
export const DownloadIcon = (p: IconProps) =>
  base(
    <>
      <path d="M12 3v12m0 0-4-4m4 4 4-4" />
      <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
    </>,
    p,
  )
export const UploadIcon = (p: IconProps) =>
  base(
    <>
      <path d="M12 21V9m0 0-4 4m4-4 4 4" />
      <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
    </>,
    p,
  )
export const FlameIcon = (p: IconProps) =>
  base(
    <path d="M12 3c1 3-3 4-3 8a3 3 0 0 0 6 0c1.2.9 2 2.4 2 4a5 5 0 1 1-10 0c0-4 3-5 3-8 0 2 1 2 2 0Z" />,
    p,
  )
