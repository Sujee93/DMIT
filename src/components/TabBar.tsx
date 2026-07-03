import { DumbbellIcon, FootprintsIcon, HomeIcon, PlateIcon, ScaleIcon } from './icons'
import type { Tab } from '../App'

const TABS: { key: Tab; label: string; Icon: typeof HomeIcon }[] = [
  { key: 'home', label: 'Home', Icon: HomeIcon },
  { key: 'weight', label: 'Weight', Icon: ScaleIcon },
  { key: 'diet', label: 'Diet', Icon: PlateIcon },
  { key: 'workouts', label: 'Train', Icon: DumbbellIcon },
  { key: 'habits', label: 'Habits', Icon: FootprintsIcon },
]

export function TabBar({ active, onChange }: { active: Tab; onChange: (t: Tab) => void }) {
  return (
    <nav
      className="sticky bottom-0 left-0 right-0 bg-surface/95 backdrop-blur border-t border-hairline flex"
      style={{ paddingBottom: 'env(safe-area-inset-bottom)' }}
    >
      {TABS.map(({ key, label, Icon }) => {
        const isActive = active === key
        return (
          <button
            key={key}
            onClick={() => onChange(key)}
            className="flex-1 flex flex-col items-center justify-center gap-0.5 pt-2 pb-1.5"
          >
            <Icon size={24} className={isActive ? 'text-coral' : 'text-muted'} strokeWidth={isActive ? 2.1 : 1.8} />
            <span className={`text-[10.5px] font-medium ${isActive ? 'text-coral' : 'text-muted'}`}>{label}</span>
          </button>
        )
      })}
    </nav>
  )
}
