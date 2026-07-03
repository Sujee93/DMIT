import { useState } from 'react'
import { TabBar } from './components/TabBar'
import { Dashboard } from './pages/Dashboard'
import { Weight } from './pages/Weight'
import { Diet } from './pages/Diet'
import { Workouts } from './pages/Workouts'
import { Habits } from './pages/Habits'
import { Settings } from './pages/Settings'

export type Tab = 'home' | 'weight' | 'diet' | 'workouts' | 'habits'

export default function App() {
  const [tab, setTab] = useState<Tab>('home')
  const [settingsOpen, setSettingsOpen] = useState(false)

  return (
    <>
      <main className="flex-1 min-h-0 overflow-y-auto pb-6">
        {tab === 'home' && <Dashboard onOpenSettings={() => setSettingsOpen(true)} onNavigate={setTab} />}
        {tab === 'weight' && <Weight />}
        {tab === 'diet' && <Diet />}
        {tab === 'workouts' && <Workouts />}
        {tab === 'habits' && <Habits />}
      </main>
      <TabBar active={tab} onChange={setTab} />
      {settingsOpen && <Settings onClose={() => setSettingsOpen(false)} />}
    </>
  )
}
