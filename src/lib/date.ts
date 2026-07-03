import type { ISODate } from './types'

export function todayISO(): ISODate {
  return toISO(new Date())
}

export function toISO(d: Date): ISODate {
  const y = d.getFullYear()
  const m = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${y}-${m}-${day}`
}

export function addDays(date: ISODate, days: number): ISODate {
  const d = new Date(date + 'T00:00:00')
  d.setDate(d.getDate() + days)
  return toISO(d)
}

export function formatShort(date: ISODate): string {
  const d = new Date(date + 'T00:00:00')
  return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })
}

export function formatWeekday(date: ISODate): string {
  const d = new Date(date + 'T00:00:00')
  return d.toLocaleDateString(undefined, { weekday: 'short' })
}

export function formatLong(date: ISODate): string {
  const d = new Date(date + 'T00:00:00')
  return d.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' })
}

export function daysBetween(a: ISODate, b: ISODate): number {
  const da = new Date(a + 'T00:00:00').getTime()
  const db = new Date(b + 'T00:00:00').getTime()
  return Math.round((db - da) / 86_400_000)
}

export function lastNDays(n: number, endDate: ISODate = todayISO()): ISODate[] {
  const out: ISODate[] = []
  for (let i = n - 1; i >= 0; i--) out.push(addDays(endDate, -i))
  return out
}
