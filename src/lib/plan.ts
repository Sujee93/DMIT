import type { WorkoutDay } from './types'

export interface Meal {
  key: 'meal1' | 'lunch' | 'snack' | 'dinner'
  time: string
  title: string
  detail: string
}

export const MEALS: Meal[] = [
  {
    key: 'meal1',
    time: '10–11 am',
    title: 'Break-fast',
    detail:
      '3–4 eggs any style with a big vegetable serving (no pol sambol) — or 2 eggs + a scoop of protein powder in water. Add half an avocado or a handful of nuts when available.',
  },
  {
    key: 'lunch',
    time: '1–2 pm',
    title: 'Lunch (main rice meal)',
    detail:
      '½ cup cooked red/basmati rice only (fist-sized). Fill the rest with fish/chicken curry or dhal + a fried egg, mallum (kale/gotukola/mukunuwenna), a vegetable curry (not potato/manioc/jackfruit), cucumber or tomato salad. Go easy on coconut milk gravies.',
  },
  {
    key: 'snack',
    time: '~4 pm',
    title: 'Evening snack',
    detail: 'Protein shake, or a cup of kadala/mung, or roasted peanuts + a boiled egg.',
  },
  {
    key: 'dinner',
    time: '6–7 pm',
    title: 'Dinner (before window closes)',
    detail:
      'No rice, no string hoppers, no bread. Grilled or curried fish/chicken (salaya, hurulla, tuna, kelawalla), a big plate of stir-fried vegetables or salad, dhal if you want something hearty.',
  },
]

export const AVOID_LIST = [
  'White bread, string hoppers, hoppers, kottu, pittu',
  'Sugary tea — plain tea or a tiny bit of milk, zero sugar',
  'Biscuits, fruit juice, kithul treacle, sweets',
  'Large portions of mango and banana (small portions only)',
]

export const FRUIT_NOTE =
  'Whole fruit is fine — 1–2 servings a day, favour papaya, guava, wood apple pulp without sugar.'

export interface Exercise {
  name: string
  target: string
}

export const WORKOUT_PLAN: Record<WorkoutDay, { title: string; exercises: Exercise[] }> = {
  A: {
    title: 'Legs & Push',
    exercises: [
      { name: 'Goblet squats or barbell back squats', target: '4 × 8–12' },
      { name: 'Romanian deadlifts (bar)', target: '3 × 10' },
      { name: 'Standing overhead press (bar or dumbbells)', target: '4 × 8–10' },
      { name: 'Dumbbell floor press', target: '3 × 10–12' },
      { name: 'Walking lunges with dumbbells', target: '3 × 10 / leg' },
    ],
  },
  B: {
    title: 'Pull & Core',
    exercises: [
      { name: 'Barbell bent-over rows', target: '4 × 8–10' },
      { name: 'Single-arm dumbbell rows', target: '3 × 10 / side' },
      { name: 'Barbell or dumbbell deadlifts', target: '3 × 8' },
      { name: 'Dumbbell curls', target: '3 × 12' },
      { name: 'Plank', target: '3 × 45–60 sec' },
    ],
  },
  C: {
    title: 'Full body',
    exercises: [
      { name: 'Barbell squats', target: '3 × 10' },
      { name: 'Push-ups', target: '3 × max' },
      { name: 'Dumbbell shoulder press', target: '3 × 10' },
      { name: 'Barbell hip thrusts', target: '3 × 12' },
      { name: "Farmer's carries with dumbbells", target: '3 × 30–40 m' },
    ],
  },
}

export const TARGETS = {
  calories: [1900, 2000] as [number, number],
  proteinG: [140, 160] as [number, number],
  carbsG: [80, 120] as [number, number],
  stepsGoal: 8000,
  fastingWindowStart: '10:00',
  fastingWindowEnd: '18:00',
}
