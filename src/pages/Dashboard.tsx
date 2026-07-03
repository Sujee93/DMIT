import { useMemo, useState } from 'react'
import { Header } from '../components/Header'
import { Card, Pill, ProgressBar, Stat } from '../components/ui'
import { SettingsIcon, FlameIcon, ChevronRightIcon } from '../components/icons'
import { useAppData, getHabitLog, getDietLog } from '../lib/store'
import { daysBetween, formatLong, lastNDays, todayISO } from '../lib/date'
import type { Tab } from '../App'
import { LogWeightSheet } from './Weight'

export function Dashboard({ onOpenSettings, onNavigate }: { onOpenSettings: () => void; onNavigate: (t: Tab) => void }) {
  const data = useAppData()
  const { settings, weightEntries, habitLogs } = data
  const today = todayISO()
  const [logOpen, setLogOpen] = useState(false)

  const latest = weightEntries[weightEntries.length - 1]
  const currentWeight = latest?.kg ?? settings.startWeightKg
  const totalToLose = settings.startWeightKg - settings.goalWeightKg
  const lost = settings.startWeightKg - currentWeight

  const fastingStatus = useMemo(() => {
    const now = new Date()
    const mins = now.getHours() * 60 + now.getMinutes()
    const [sh, sm] = settings.fastingWindowStart.split(':').map(Number)
    const [eh, em] = settings.fastingWindowEnd.split(':').map(Number)
    const start = sh * 60 + sm
    const end = eh * 60 + em
    return mins >= start && mins < end
  }, [settings.fastingWindowStart, settings.fastingWindowEnd])

  const streak = useMemo(() => {
    let count = 0
    let d = today
    for (;;) {
      const log = habitLogs.find((h) => h.date === d)
      if (log && log.steps >= settings.stepsGoal) {
        count++
        d = lastNDays(2, d)[0]
      } else break
    }
    return count
  }, [habitLogs, settings.stepsGoal, today])

  const todayHabit = getHabitLog(today)
  const todayDiet = getDietLog(today)
  const mealsChecked = Object.values(todayDiet.meals).filter(Boolean).length
  const daysIn = daysBetween(settings.startDate, today) + 1
  const glucoseDays = daysBetween(today, settings.glucoseRecheckDate)
  const hba1cDays = daysBetween(today, settings.hba1cRecheckDate)

  return (
    <>
      <Header
        title="Today"
        subtitle={formatLong(today)}
        action={
          <button onClick={onOpenSettings} className="w-10 h-10 rounded-full bg-surface shadow-card flex items-center justify-center text-ink">
            <SettingsIcon size={20} />
          </button>
        }
      />
      <div className="px-4 flex flex-col gap-3 mt-1">
        <Card className="flex flex-col gap-3">
          <div className="flex items-center justify-between">
            <Stat label={`Day ${daysIn} of your journey`} value={`${currentWeight.toFixed(1)} kg`} sub={`Goal ${settings.goalWeightKg} kg`} />
            <button onClick={() => setLogOpen(true)}>
              <Pill tone="default">Log weight</Pill>
            </button>
          </div>
          <ProgressBar value={lost > 0 ? lost : 0} max={totalToLose} />
          <div className="flex justify-between text-[12px] text-muted">
            <span>{settings.startWeightKg} kg</span>
            <span>{lost > 0 ? `${lost.toFixed(1)} kg lost` : 'Just starting'}</span>
            <span>{settings.goalWeightKg} kg</span>
          </div>
        </Card>

        <div className="grid grid-cols-2 gap-3">
          <Card onClick={() => onNavigate('habits')} className="cursor-pointer">
            <div className="flex items-center gap-1.5 text-coral mb-1">
              <FlameIcon size={18} />
              <span className="text-[13px] font-semibold">Streak</span>
            </div>
            <div className="text-[22px] font-bold text-ink">{streak} {streak === 1 ? 'day' : 'days'}</div>
            <div className="text-[12px] text-muted mt-0.5">Hit {settings.stepsGoal.toLocaleString()} steps</div>
          </Card>
          <Card onClick={() => onNavigate('home')} className="flex flex-col justify-between">
            <div className="text-[13px] font-semibold text-ink mb-1">Eating window</div>
            <div className="text-[15px] font-bold" style={{ color: fastingStatus ? 'var(--color-success)' : 'var(--color-muted)' }}>
              {fastingStatus ? 'Open now' : 'Fasting'}
            </div>
            <div className="text-[12px] text-muted mt-0.5">
              {settings.fastingWindowStart}–{settings.fastingWindowEnd}
            </div>
          </Card>
        </div>

        <Card onClick={() => onNavigate('diet')} className="cursor-pointer flex items-center justify-between">
          <div>
            <div className="text-[13px] font-semibold text-ink mb-1">Today's meals</div>
            <div className="text-[13px] text-muted">{mealsChecked} of 4 logged</div>
          </div>
          <div className="flex items-center gap-2">
            <ProgressBar value={mealsChecked} max={4} />
            <ChevronRightIcon size={18} className="text-muted flex-none" />
          </div>
        </Card>

        <Card onClick={() => onNavigate('habits')} className="cursor-pointer flex items-center justify-between">
          <div>
            <div className="text-[13px] font-semibold text-ink mb-1">Steps today</div>
            <div className="text-[20px] font-bold text-ink">{todayHabit.steps.toLocaleString()}</div>
          </div>
          <ChevronRightIcon size={18} className="text-muted" />
        </Card>

        <Card>
          <div className="text-[13px] font-semibold text-ink mb-2">Doctor follow-up</div>
          <div className="flex flex-col gap-1.5 text-[13px] text-ink-soft">
            <div className="flex justify-between">
              <span>Fasting glucose recheck</span>
              <span className="font-medium">{glucoseDays > 0 ? `in ${glucoseDays}d` : 'due'}</span>
            </div>
            <div className="flex justify-between">
              <span>HbA1c test</span>
              <span className="font-medium">{hba1cDays > 0 ? `in ${hba1cDays}d` : 'due'}</span>
            </div>
          </div>
        </Card>
      </div>
      {logOpen && <LogWeightSheet onClose={() => setLogOpen(false)} />}
    </>
  )
}
