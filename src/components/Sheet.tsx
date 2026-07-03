import type { ReactNode } from 'react'
import { XIcon } from './icons'

export function Sheet({ title, onClose, children }: { title: string; onClose: () => void; children: ReactNode }) {
  return (
    <div className="fixed inset-0 z-50 flex justify-center">
      <div className="absolute inset-0 bg-black/40" onClick={onClose} />
      <div
        className="relative w-full max-w-[480px] bg-surface rounded-t-[20px] mt-auto max-h-[88vh] flex flex-col animate-[slideUp_0.22s_ease-out]"
        style={{ paddingBottom: 'env(safe-area-inset-bottom)' }}
      >
        <div className="flex items-center justify-between px-4 pt-4 pb-2 flex-none">
          <h2 className="text-[17px] font-bold text-ink">{title}</h2>
          <button onClick={onClose} className="w-8 h-8 rounded-full bg-canvas flex items-center justify-center text-ink">
            <XIcon size={18} />
          </button>
        </div>
        <div className="overflow-y-auto px-4 pb-6">{children}</div>
      </div>
      <style>{`@keyframes slideUp { from { transform: translateY(24px); opacity: 0 } to { transform: translateY(0); opacity: 1 } }`}</style>
    </div>
  )
}
