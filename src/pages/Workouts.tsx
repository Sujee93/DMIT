import { useState } from 'react'
import { Header } from '../components/Header'
import { Card, Button, SectionTitle, Pill } from '../components/ui'
import { Sheet } from '../components/Sheet'
import { TrashIcon } from '../components/icons'
import { WORKOUT_PLAN } from '../lib/plan'
import { actions, useAppData } from '../lib/store'
import { formatShort, formatWeekday, todayISO } from '../lib/date'
import type { WorkoutDay } from '../lib/types'

const DAY_KEYS: WorkoutDay[] = ['A', 'B', 'C']

export function Workouts() {
  const data = useAppData()
  const [logDay, setLogDay] = useState<WorkoutDay | null>(null)
  const sessions = [...data.workoutSessions].reverse()

  function lastWeightFor(day: WorkoutDay, exerciseName: string): number | undefined {
    for (const s of sessions) {
      if (s.day !== day) continue
      const ex = s.exercises.find((e) => e.name === exerciseName)
      if (ex?.weightKg) return ex.weightKg
    }
    return undefined
  }

  return (
    <>
      <Header title="Train" subtitle="3 strength days + daily walking" />
      <div className="px-4 flex flex-col gap-3 mt-1">
        {DAY_KEYS.map((day) => {
          const plan = WORKOUT_PLAN[day]
          const doneToday = data.workoutSessions.some((s) => s.date === todayISO() && s.day === day)
          return (
            <Card key={day}>
              <div className="flex items-center justify-between mb-2">
                <div>
                  <div className="text-[15px] font-bold text-ink">Day {day} · {plan.title}</div>
                </div>
                {doneToday && <Pill tone="success">Done today</Pill>}
              </div>
              <div className="flex flex-col gap-1.5 mb-3">
                {plan.exercises.map((ex) => {
                  const last = lastWeightFor(day, ex.name)
                  return (
                    <div key={ex.name} className="flex items-center justify-between text-[13px]">
                      <span className="text-ink-soft">{ex.name}</span>
                      <span className="text-muted flex-none pl-2 text-right">
                        {ex.target}
                        {last !== undefined && <span className="text-coral"> · last {last}kg</span>}
                      </span>
                    </div>
                  )
                })}
              </div>
              <Button variant={doneToday ? 'secondary' : 'primary'} className="w-full" onClick={() => setLogDay(day)}>
                {doneToday ? 'Update today’s log' : 'Log this workout'}
              </Button>
            </Card>
          )
        })}

        <SectionTitle>History</SectionTitle>
        <Card className="p-0 divide-y divide-hairline">
          {sessions.length === 0 && <div className="p-4 text-[13px] text-muted">No sessions logged yet.</div>}
          {sessions.slice(0, 15).map((s) => (
            <div key={s.id} className="flex items-center justify-between px-4 py-3">
              <div>
                <div className="text-[14.5px] font-medium text-ink">Day {s.day} · {WORKOUT_PLAN[s.day].title}</div>
                <div className="text-[12.5px] text-muted">
                  {formatWeekday(s.date)}, {formatShort(s.date)}
                </div>
              </div>
              <button onClick={() => actions.deleteWorkoutSession(s.id)} className="text-muted p-2">
                <TrashIcon size={17} />
              </button>
            </div>
          ))}
        </Card>
      </div>
      {logDay && <LogWorkoutSheet day={logDay} onClose={() => setLogDay(null)} />}
    </>
  )
}

function LogWorkoutSheet({ day, onClose }: { day: WorkoutDay; onClose: () => void }) {
  const data = useAppData()
  const today = todayISO()
  const plan = WORKOUT_PLAN[day]
  const existing = data.workoutSessions.find((s) => s.date === today && s.day === day)

  const [weights, setWeights] = useState<Record<string, string>>(() => {
    const init: Record<string, string> = {}
    for (const ex of plan.exercises) {
      const found = existing?.exercises.find((e) => e.name === ex.name)
      init[ex.name] = found?.weightKg ? String(found.weightKg) : ''
    }
    return init
  })

  function save() {
    actions.upsertWorkoutSession({
      id: existing?.id,
      date: today,
      day,
      completed: true,
      exercises: plan.exercises.map((ex) => ({
        name: ex.name,
        weightKg: weights[ex.name] ? parseFloat(weights[ex.name]) : undefined,
      })),
    })
    onClose()
  }

  return (
    <Sheet title={`Day ${day} · ${plan.title}`} onClose={onClose}>
      <div className="flex flex-col gap-3 pt-1">
        <p className="text-[12.5px] text-muted -mt-1">Enter the weight you used for each lift (leave blank for bodyweight moves).</p>
        {plan.exercises.map((ex) => (
          <div key={ex.name} className="flex items-center justify-between gap-3">
            <div className="min-w-0">
              <div className="text-[13.5px] font-medium text-ink truncate">{ex.name}</div>
              <div className="text-[12px] text-muted">{ex.target}</div>
            </div>
            <input
              inputMode="decimal"
              placeholder="kg"
              value={weights[ex.name]}
              onChange={(e) => setWeights((w) => ({ ...w, [ex.name]: e.target.value }))}
              className="w-20 flex-none h-11 px-3 rounded-xl border border-hairline text-[14px] text-ink bg-canvas text-right"
            />
          </div>
        ))}
        <Button onClick={save} className="w-full mt-2">
          Save workout
        </Button>
      </div>
    </Sheet>
  )
}
