import { useSyncExternalStore } from 'react'
import type { AppData, DietLog, HabitLog, Settings, WeightEntry, WorkoutSession } from './types'
import { todayISO } from './date'
import { TARGETS } from './plan'

const STORAGE_KEY = 'dmit.appdata.v1'

function defaultSettings(): Settings {
  return {
    startWeightKg: 87,
    goalWeightKg: 76,
    heightCm: 178,
    goalCalories: 1950,
    goalProteinG: 150,
    goalCarbsG: 100,
    stepsGoal: TARGETS.stepsGoal,
    fastingWindowStart: TARGETS.fastingWindowStart,
    fastingWindowEnd: TARGETS.fastingWindowEnd,
    startDate: todayISO(),
    glucoseRecheckDate: addWeeks(todayISO(), 7),
    hba1cRecheckDate: addMonths(todayISO(), 3),
  }
}

function addWeeks(date: string, weeks: number): string {
  const d = new Date(date + 'T00:00:00')
  d.setDate(d.getDate() + weeks * 7)
  return d.toISOString().slice(0, 10)
}

function addMonths(date: string, months: number): string {
  const d = new Date(date + 'T00:00:00')
  d.setMonth(d.getMonth() + months)
  return d.toISOString().slice(0, 10)
}

function defaultData(): AppData {
  return {
    weightEntries: [],
    workoutSessions: [],
    dietLogs: [],
    habitLogs: [],
    settings: defaultSettings(),
  }
}

function load(): AppData {
  try {
    const raw = localStorage.getItem(STORAGE_KEY)
    if (!raw) return defaultData()
    const parsed = JSON.parse(raw)
    // merge with defaults so new fields added later don't crash old data
    return {
      ...defaultData(),
      ...parsed,
      settings: { ...defaultSettings(), ...parsed.settings },
    }
  } catch {
    return defaultData()
  }
}

let data: AppData = load()
const listeners = new Set<() => void>()

function persist() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(data))
  listeners.forEach((l) => l())
}

function subscribe(listener: () => void) {
  listeners.add(listener)
  return () => listeners.delete(listener)
}

function getSnapshot() {
  return data
}

export function useAppData(): AppData {
  return useSyncExternalStore(subscribe, getSnapshot)
}

function uid(): string {
  return Math.random().toString(36).slice(2, 10) + Date.now().toString(36)
}

export const actions = {
  addWeightEntry(entry: Omit<WeightEntry, 'id'>) {
    const filtered = data.weightEntries.filter((e) => e.date !== entry.date)
    data = { ...data, weightEntries: [...filtered, { ...entry, id: uid() }].sort((a, b) => a.date.localeCompare(b.date)) }
    persist()
  },
  deleteWeightEntry(id: string) {
    data = { ...data, weightEntries: data.weightEntries.filter((e) => e.id !== id) }
    persist()
  },

  upsertWorkoutSession(session: Omit<WorkoutSession, 'id'> & { id?: string }) {
    const id = session.id ?? uid()
    const others = data.workoutSessions.filter((s) => s.id !== id)
    data = { ...data, workoutSessions: [...others, { ...session, id }].sort((a, b) => a.date.localeCompare(b.date)) }
    persist()
  },
  deleteWorkoutSession(id: string) {
    data = { ...data, workoutSessions: data.workoutSessions.filter((s) => s.id !== id) }
    persist()
  },

  setDietLog(log: DietLog) {
    const others = data.dietLogs.filter((d) => d.date !== log.date)
    data = { ...data, dietLogs: [...others, log] }
    persist()
  },

  setHabitLog(log: HabitLog) {
    const others = data.habitLogs.filter((h) => h.date !== log.date)
    data = { ...data, habitLogs: [...others, log] }
    persist()
  },

  updateSettings(patch: Partial<Settings>) {
    data = { ...data, settings: { ...data.settings, ...patch } }
    persist()
  },

  importData(next: AppData) {
    data = { ...defaultData(), ...next, settings: { ...defaultSettings(), ...next.settings } }
    persist()
  },

  resetAll() {
    data = defaultData()
    persist()
  },
}

export function getData(): AppData {
  return data
}

export function getDietLog(date: string): DietLog {
  return (
    data.dietLogs.find((d) => d.date === date) ?? {
      date,
      meals: { meal1: false, lunch: false, snack: false, dinner: false },
    }
  )
}

export function getHabitLog(date: string): HabitLog {
  return (
    data.habitLogs.find((h) => h.date === date) ?? {
      date,
      steps: 0,
      walkAfterLunch: false,
      walkAfterDinner: false,
      longWeekendWalk: false,
    }
  )
}
