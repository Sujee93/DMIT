import { useMemo, useState } from 'react'
import { Header } from '../components/Header'
import { Card, Checkbox, ProgressBar, SectionTitle } from '../components/ui'
import { FlameIcon } from '../components/icons'
import { actions, getHabitLog, useAppData } from '../lib/store'
import { formatShort, lastNDays, todayISO } from '../lib/date'

export function Habits() {
  const data = useAppData()
  const today = todayISO()
  const log = getHabitLog(today)
  const [stepsInput, setStepsInput] = useState(String(log.steps || ''))
  const goal = data.settings.stepsGoal

  function commitSteps() {
    const val = parseInt(stepsInput, 10) || 0
    actions.setHabitLog({ ...log, steps: val })
  }

  function toggle(key: 'walkAfterLunch' | 'walkAfterDinner' | 'longWeekendWalk') {
    actions.setHabitLog({ ...log, [key]: !log[key] })
  }

  const streak = useMemo(() => {
    let count = 0
    let d = today
    for (;;) {
      const h = data.habitLogs.find((x) => x.date === d)
      if (h && h.steps >= goal) {
        count++
        d = lastNDays(2, d)[0]
      } else break
    }
    return count
  }, [data.habitLogs, goal, today])

  const days = lastNDays(28)
  const isWeekend = new Date(today + 'T00:00:00').getDay() % 6 === 0

  return (
    <>
      <Header title="Habits" subtitle="Walking is your secret weapon" />
      <div className="px-4 flex flex-col gap-3 mt-1">
        <Card>
          <div className="flex items-center justify-between mb-2">
            <div className="text-[13px] font-semibold text-ink">Steps today</div>
            <div className="flex items-center gap-1.5 text-coral">
              <FlameIcon size={16} />
              <span className="text-[13px] font-semibold">{streak}d streak</span>
            </div>
          </div>
          <input
            inputMode="numeric"
            value={stepsInput}
            onChange={(e) => setStepsInput(e.target.value)}
            onBlur={commitSteps}
            placeholder="0"
            className="w-full h-14 px-4 rounded-2xl border border-hairline text-[24px] font-bold text-ink bg-canvas mb-2"
          />
          <ProgressBar value={log.steps} max={goal} />
          <div className="text-[12px] text-muted mt-1.5">Goal: {goal.toLocaleString()} steps</div>
        </Card>

        <SectionTitle>Post-meal walks</SectionTitle>
        <Card className="p-3.5 flex flex-col divide-y divide-hairline">
          <Checkbox checked={log.walkAfterLunch} onChange={() => toggle('walkAfterLunch')} label="10–15 min walk after lunch" />
          <Checkbox checked={log.walkAfterDinner} onChange={() => toggle('walkAfterDinner')} label="10–15 min walk after dinner" />
          {isWeekend && (
            <Checkbox checked={log.longWeekendWalk} onChange={() => toggle('longWeekendWalk')} label="45–60 min brisk weekend walk" />
          )}
        </Card>

        <SectionTitle>Last 28 days</SectionTitle>
        <Card>
          <div className="grid grid-cols-7 gap-1.5">
            {days.map((d) => {
              const h = data.habitLogs.find((x) => x.date === d)
              const ratio = h ? h.steps / goal : 0
              const bg =
                ratio >= 1 ? 'var(--color-coral)' : ratio > 0.5 ? 'var(--color-coral-light)' : ratio > 0 ? 'var(--color-hairline)' : 'transparent'
              return (
                <div
                  key={d}
                  title={`${formatShort(d)}: ${h?.steps ?? 0} steps`}
                  className="aspect-square rounded-md border border-hairline"
                  style={{ background: bg }}
                />
              )
            })}
          </div>
        </Card>
      </div>
    </>
  )
}
