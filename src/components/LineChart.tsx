interface Point {
  x: string
  y: number
}

export function LineChart({
  points,
  goal,
  height = 160,
  formatY = (v: number) => v.toFixed(1),
}: {
  points: Point[]
  goal?: number
  height?: number
  formatY?: (v: number) => string
}) {
  if (points.length === 0) {
    return (
      <div className="flex items-center justify-center text-[13px] text-muted" style={{ height }}>
        No entries yet
      </div>
    )
  }

  const width = 320
  const padX = 8
  const padY = 16
  const values = points.map((p) => p.y).concat(goal !== undefined ? [goal] : [])
  const min = Math.min(...values)
  const max = Math.max(...values)
  const range = max - min || 1
  const yFor = (v: number) => padY + (1 - (v - min) / range) * (height - padY * 2)
  const xFor = (i: number) => (points.length === 1 ? width / 2 : padX + (i / (points.length - 1)) * (width - padX * 2))

  const path = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${xFor(i)} ${yFor(p.y)}`).join(' ')
  const areaPath = `${path} L ${xFor(points.length - 1)} ${height - padY} L ${xFor(0)} ${height - padY} Z`

  return (
    <svg viewBox={`0 0 ${width} ${height}`} width="100%" height={height} preserveAspectRatio="none">
      <defs>
        <linearGradient id="weightFill" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stopColor="var(--color-coral)" stopOpacity="0.18" />
          <stop offset="100%" stopColor="var(--color-coral)" stopOpacity="0" />
        </linearGradient>
      </defs>
      {goal !== undefined && (
        <line x1={padX} x2={width - padX} y1={yFor(goal)} y2={yFor(goal)} stroke="var(--color-success)" strokeWidth={1} strokeDasharray="4 3" />
      )}
      <path d={areaPath} fill="url(#weightFill)" stroke="none" />
      <path d={path} fill="none" stroke="var(--color-coral)" strokeWidth={2.5} strokeLinecap="round" strokeLinejoin="round" />
      {points.map((p, i) => (
        <circle key={i} cx={xFor(i)} cy={yFor(p.y)} r={i === points.length - 1 ? 4 : 2.5} fill="var(--color-coral)" />
      ))}
      {goal !== undefined && (
        <text x={width - padX} y={yFor(goal) - 4} textAnchor="end" fontSize="9" fill="var(--color-success)">
          Goal {formatY(goal)}
        </text>
      )}
    </svg>
  )
}
