import type { ButtonHTMLAttributes, HTMLAttributes, ReactNode } from 'react'

export function Card({ className = '', ...props }: HTMLAttributes<HTMLDivElement>) {
  return (
    <div
      className={`bg-surface rounded-card shadow-card p-4 ${className}`}
      {...props}
    />
  )
}

export function SectionTitle({ children, action }: { children: ReactNode; action?: ReactNode }) {
  return (
    <div className="flex items-center justify-between mb-2.5 px-0.5">
      <h2 className="text-[15px] font-semibold text-ink tracking-tight">{children}</h2>
      {action}
    </div>
  )
}

export function Button({
  variant = 'primary',
  className = '',
  ...props
}: ButtonHTMLAttributes<HTMLButtonElement> & { variant?: 'primary' | 'secondary' | 'ghost' }) {
  const styles = {
    primary: 'bg-coral text-white active:bg-coral-dark',
    secondary: 'bg-coral-light text-coral-dark',
    ghost: 'bg-canvas text-ink border border-hairline',
  }[variant]
  return (
    <button
      className={`h-11 px-5 rounded-pill font-semibold text-[15px] transition-colors disabled:opacity-40 ${styles} ${className}`}
      {...props}
    />
  )
}

export function ProgressBar({ value, max, color = 'var(--color-coral)' }: { value: number; max: number; color?: string }) {
  const pct = Math.max(0, Math.min(100, (value / max) * 100))
  return (
    <div className="h-2 rounded-pill bg-hairline overflow-hidden">
      <div className="h-full rounded-pill transition-all" style={{ width: `${pct}%`, background: color }} />
    </div>
  )
}

export function Stat({ label, value, sub }: { label: string; value: string; sub?: string }) {
  return (
    <div>
      <div className="text-[22px] font-bold text-ink leading-tight">{value}</div>
      <div className="text-[13px] text-muted">{label}</div>
      {sub && <div className="text-[12px] text-muted mt-0.5">{sub}</div>}
    </div>
  )
}

export function Pill({ children, tone = 'default' }: { children: ReactNode; tone?: 'default' | 'success' | 'warning' }) {
  const toneClass = {
    default: 'bg-canvas text-muted',
    success: 'bg-[color-mix(in_srgb,var(--color-success)_12%,transparent)] text-success',
    warning: 'bg-[color-mix(in_srgb,var(--color-warning)_12%,transparent)] text-warning',
  }[tone]
  return <span className={`inline-flex items-center px-2.5 py-1 rounded-pill text-[12px] font-medium ${toneClass}`}>{children}</span>
}

export function Checkbox({ checked, onChange, label, sub }: { checked: boolean; onChange: () => void; label: string; sub?: string }) {
  return (
    <button
      onClick={onChange}
      className="w-full flex items-center gap-3 py-2.5 text-left"
    >
      <span
        className={`flex-none w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors ${
          checked ? 'bg-coral border-coral' : 'border-hairline'
        }`}
      >
        {checked && (
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth={3} strokeLinecap="round" strokeLinejoin="round">
            <path d="M20 6 9 17l-5-5" />
          </svg>
        )}
      </span>
      <span className="flex-1 min-w-0">
        <div className={`text-[14.5px] font-medium ${checked ? 'text-ink line-through decoration-hairline' : 'text-ink'}`}>{label}</div>
        {sub && <div className="text-[12.5px] text-muted mt-0.5">{sub}</div>}
      </span>
    </button>
  )
}
