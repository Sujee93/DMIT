import { useState } from 'react'
import { Header } from '../components/Header'
import { Card, Button, SectionTitle } from '../components/ui'
import { Sheet } from '../components/Sheet'
import { LineChart } from '../components/LineChart'
import { TrashIcon, PlusIcon } from '../components/icons'
import { actions, useAppData } from '../lib/store'
import { formatShort, formatWeekday, todayISO } from '../lib/date'

export function Weight() {
  const data = useAppData()
  const [logOpen, setLogOpen] = useState(false)
  const entries = data.weightEntries
  const chartPoints = entries.slice(-30).map((e) => ({ x: e.date, y: e.kg }))
  const recent = [...entries].reverse().slice(0, 20)

  return (
    <>
      <Header title="Weight" subtitle="Track your progress toward your goal" />
      <div className="px-4 flex flex-col gap-3 mt-1">
        <Card>
          <LineChart points={chartPoints} goal={data.settings.goalWeightKg} formatY={(v) => `${v.toFixed(1)} kg`} />
        </Card>

        <Button onClick={() => setLogOpen(true)} className="w-full flex items-center justify-center gap-1.5">
          <PlusIcon size={18} /> Log today's weight
        </Button>

        <SectionTitle>History</SectionTitle>
        <Card className="p-0 divide-y divide-hairline">
          {recent.length === 0 && <div className="p-4 text-[13px] text-muted">No entries yet — log your first weigh-in.</div>}
          {recent.map((e) => (
            <div key={e.id} className="flex items-center justify-between px-4 py-3">
              <div>
                <div className="text-[14.5px] font-medium text-ink">{e.kg.toFixed(1)} kg</div>
                <div className="text-[12.5px] text-muted">
                  {formatWeekday(e.date)}, {formatShort(e.date)}
                </div>
              </div>
              <button onClick={() => actions.deleteWeightEntry(e.id)} className="text-muted p-2">
                <TrashIcon size={17} />
              </button>
            </div>
          ))}
        </Card>
      </div>
      {logOpen && <LogWeightSheet onClose={() => setLogOpen(false)} />}
    </>
  )
}

export function LogWeightSheet({ onClose }: { onClose: () => void }) {
  const data = useAppData()
  const today = todayISO()
  const existing = data.weightEntries.find((e) => e.date === today)
  const [kg, setKg] = useState(existing ? String(existing.kg) : '')

  function save() {
    const val = parseFloat(kg)
    if (!val || val <= 0) return
    actions.addWeightEntry({ date: today, kg: val })
    onClose()
  }

  return (
    <Sheet title="Log weight" onClose={onClose}>
      <div className="flex flex-col gap-4 pt-1">
        <div>
          <label className="text-[13px] text-muted">Weight (kg)</label>
          <input
            autoFocus
            inputMode="decimal"
            value={kg}
            onChange={(e) => setKg(e.target.value)}
            placeholder="e.g. 86.4"
            className="w-full mt-1 h-14 px-4 rounded-2xl border border-hairline text-[22px] font-semibold text-ink bg-canvas"
          />
        </div>
        <Button onClick={save} className="w-full">
          Save
        </Button>
      </div>
    </Sheet>
  )
}
