import { Header } from '../components/Header'
import { Card, Checkbox, Pill, SectionTitle } from '../components/ui'
import { AVOID_LIST, FRUIT_NOTE, MEALS } from '../lib/plan'
import { actions, getDietLog, useAppData } from '../lib/store'
import { todayISO } from '../lib/date'

export function Diet() {
  const data = useAppData()
  const today = todayISO()
  const log = getDietLog(today)
  const { settings } = data

  function toggle(key: keyof typeof log.meals) {
    actions.setDietLog({ date: today, meals: { ...log.meals, [key]: !log.meals[key] } })
  }

  return (
    <>
      <Header title="Diet" subtitle="Low-carb, high-protein, Sri Lankan style" />
      <div className="px-4 flex flex-col gap-3 mt-1">
        <Card className="flex justify-between text-center">
          <div>
            <div className="text-[15px] font-bold text-ink">
              {settings.goalCalories}
            </div>
            <div className="text-[11.5px] text-muted">kcal target</div>
          </div>
          <div>
            <div className="text-[15px] font-bold text-ink">{settings.goalProteinG}g</div>
            <div className="text-[11.5px] text-muted">protein</div>
          </div>
          <div>
            <div className="text-[15px] font-bold text-ink">{settings.goalCarbsG}g</div>
            <div className="text-[11.5px] text-muted">carbs</div>
          </div>
        </Card>

        <SectionTitle action={<Pill>{Object.values(log.meals).filter(Boolean).length}/4 today</Pill>}>Today's plan</SectionTitle>
        <div className="flex flex-col gap-2.5">
          {MEALS.map((meal) => (
            <Card key={meal.key} className="p-3.5">
              <Checkbox checked={log.meals[meal.key]} onChange={() => toggle(meal.key)} label={`${meal.title} · ${meal.time}`} sub={meal.detail} />
            </Card>
          ))}
        </div>

        <SectionTitle>Avoid / minimise</SectionTitle>
        <Card className="flex flex-col gap-2">
          {AVOID_LIST.map((item) => (
            <div key={item} className="text-[13.5px] text-ink-soft flex gap-2">
              <span className="text-coral">•</span>
              <span>{item}</span>
            </div>
          ))}
          <div className="text-[12.5px] text-muted pt-1 border-t border-hairline mt-1">{FRUIT_NOTE}</div>
        </Card>
      </div>
    </>
  )
}
