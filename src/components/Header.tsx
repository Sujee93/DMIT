import type { ReactNode } from 'react'

export function Header({ title, subtitle, action }: { title: string; subtitle?: string; action?: ReactNode }) {
  return (
    <header
      className="sticky top-0 z-10 bg-canvas/90 backdrop-blur px-4 pb-2 flex items-start justify-between"
      style={{ paddingTop: 'calc(env(safe-area-inset-top) + 14px)' }}
    >
      <div>
        <h1 className="text-[26px] font-bold text-ink tracking-tight leading-tight">{title}</h1>
        {subtitle && <p className="text-[13px] text-muted mt-0.5">{subtitle}</p>}
      </div>
      {action}
    </header>
  )
}
