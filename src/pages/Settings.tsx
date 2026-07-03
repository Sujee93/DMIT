import { useRef, useState, type ChangeEvent } from 'react'
import { Sheet } from '../components/Sheet'
import { Button, SectionTitle } from '../components/ui'
import { DownloadIcon, UploadIcon } from '../components/icons'
import { actions, getData, useAppData } from '../lib/store'
import type { AppData } from '../lib/types'

function Field({ label, unit, value, onChange, type = 'number' }: { label: string; unit?: string; value: string | number; onChange: (v: string) => void; type?: string }) {
  return (
    <label className="flex items-center justify-between py-2.5 border-b border-hairline last:border-0">
      <span className="text-[13.5px] text-ink-soft">{label}</span>
      <span className="flex items-center gap-1">
        <input
          type={type}
          inputMode={type === 'number' ? 'decimal' : undefined}
          value={value}
          onChange={(e) => onChange(e.target.value)}
          className="w-24 h-9 px-2 rounded-lg border border-hairline text-[14px] text-ink bg-canvas text-right"
        />
        {unit && <span className="text-[12.5px] text-muted w-8">{unit}</span>}
      </span>
    </label>
  )
}

export function Settings({ onClose }: { onClose: () => void }) {
  const data = useAppData()
  const s = data.settings
  const fileRef = useRef<HTMLInputElement>(null)
  const [importError, setImportError] = useState('')

  function num(v: string) {
    return v === '' ? 0 : parseFloat(v)
  }

  function exportData() {
    const blob = new Blob([JSON.stringify(getData(), null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `dmit-backup-${new Date().toISOString().slice(0, 10)}.json`
    a.click()
    URL.revokeObjectURL(url)
  }

  function onImportFile(e: ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0]
    if (!file) return
    setImportError('')
    file
      .text()
      .then((text) => {
        const parsed = JSON.parse(text) as AppData
        if (!parsed.settings || !Array.isArray(parsed.weightEntries)) throw new Error('invalid')
        actions.importData(parsed)
      })
      .catch(() => setImportError('Could not read that file — make sure it’s a DMIT backup JSON.'))
    e.target.value = ''
  }

  function resetAll() {
    if (confirm('This deletes all logged data on this device. This cannot be undone. Continue?')) {
      actions.resetAll()
    }
  }

  return (
    <Sheet title="Settings" onClose={onClose}>
      <div className="flex flex-col gap-1 pt-1">
        <SectionTitle>Goals</SectionTitle>
        <div className="bg-canvas rounded-2xl px-3.5 mb-3">
          <Field label="Current weight" unit="kg" value={s.startWeightKg} onChange={(v) => actions.updateSettings({ startWeightKg: num(v) })} />
          <Field label="Goal weight" unit="kg" value={s.goalWeightKg} onChange={(v) => actions.updateSettings({ goalWeightKg: num(v) })} />
          <Field label="Height" unit="cm" value={s.heightCm} onChange={(v) => actions.updateSettings({ heightCm: num(v) })} />
          <Field label="Calories" unit="kcal" value={s.goalCalories} onChange={(v) => actions.updateSettings({ goalCalories: num(v) })} />
          <Field label="Protein" unit="g" value={s.goalProteinG} onChange={(v) => actions.updateSettings({ goalProteinG: num(v) })} />
          <Field label="Carbs" unit="g" value={s.goalCarbsG} onChange={(v) => actions.updateSettings({ goalCarbsG: num(v) })} />
          <Field label="Steps goal" value={s.stepsGoal} onChange={(v) => actions.updateSettings({ stepsGoal: num(v) })} />
        </div>

        <SectionTitle>Eating window</SectionTitle>
        <div className="bg-canvas rounded-2xl px-3.5 mb-3">
          <Field label="Opens" type="time" value={s.fastingWindowStart} onChange={(v) => actions.updateSettings({ fastingWindowStart: v })} />
          <Field label="Closes" type="time" value={s.fastingWindowEnd} onChange={(v) => actions.updateSettings({ fastingWindowEnd: v })} />
        </div>

        <SectionTitle>Doctor follow-up</SectionTitle>
        <div className="bg-canvas rounded-2xl px-3.5 mb-3">
          <Field label="Glucose recheck" type="date" value={s.glucoseRecheckDate} onChange={(v) => actions.updateSettings({ glucoseRecheckDate: v })} />
          <Field label="HbA1c test" type="date" value={s.hba1cRecheckDate} onChange={(v) => actions.updateSettings({ hba1cRecheckDate: v })} />
        </div>

        <SectionTitle>Data</SectionTitle>
        <div className="flex flex-col gap-2 mb-2">
          <Button variant="ghost" className="w-full flex items-center justify-center gap-2" onClick={exportData}>
            <DownloadIcon size={17} /> Export backup
          </Button>
          <Button variant="ghost" className="w-full flex items-center justify-center gap-2" onClick={() => fileRef.current?.click()}>
            <UploadIcon size={17} /> Import backup
          </Button>
          <input ref={fileRef} type="file" accept="application/json" hidden onChange={onImportFile} />
          {importError && <p className="text-[12.5px] text-warning px-1">{importError}</p>}
          <button onClick={resetAll} className="text-[13px] text-warning py-2">
            Erase all data on this device
          </button>
        </div>
        <p className="text-[11.5px] text-muted px-1 pb-2">
          All data stays on this device only. Export a backup before switching phones or clearing browser data.
        </p>
      </div>
    </Sheet>
  )
}
