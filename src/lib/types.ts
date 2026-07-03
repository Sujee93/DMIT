export type ISODate = string // 'YYYY-MM-DD'

export interface WeightEntry {
  id: string
  date: ISODate
  kg: number
}

export type WorkoutDay = 'A' | 'B' | 'C'

export interface ExerciseLog {
  name: string
  weightKg?: number
  notes?: string
}

export interface WorkoutSession {
  id: string
  date: ISODate
  day: WorkoutDay
  exercises: ExerciseLog[]
  completed: boolean
}

export interface MealChecklist {
  meal1: boolean
  lunch: boolean
  snack: boolean
  dinner: boolean
}

export interface DietLog {
  date: ISODate
  meals: MealChecklist
}

export interface HabitLog {
  date: ISODate
  steps: number
  walkAfterLunch: boolean
  walkAfterDinner: boolean
  longWeekendWalk: boolean
}

export interface Settings {
  startWeightKg: number
  goalWeightKg: number
  heightCm: number
  goalCalories: number
  goalProteinG: number
  goalCarbsG: number
  stepsGoal: number
  fastingWindowStart: string // 'HH:MM'
  fastingWindowEnd: string
  startDate: ISODate
  glucoseRecheckDate: ISODate
  hba1cRecheckDate: ISODate
}

export interface AppData {
  weightEntries: WeightEntry[]
  workoutSessions: WorkoutSession[]
  dietLogs: DietLog[]
  habitLogs: HabitLog[]
  settings: Settings
}
