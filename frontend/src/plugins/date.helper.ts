interface DueDateInfo {
  label: string
  color: string
}

// PRD 4.5 — 5 kondisi due date
export function getDueDateInfo(dueDateStr: string): DueDateInfo {
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  const dueDate = new Date(dueDateStr)
  dueDate.setHours(0, 0, 0, 0)

  const diffDays = Math.round((dueDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return { label: 'Overdue', color: '#C92A2A' }
  if (diffDays === 0) return { label: 'Hari ini', color: '#C92A2A' }
  if (diffDays === 1) return { label: 'Besok', color: '#E67700' }
  if (diffDays <= 7) return { label: `${diffDays} hari lagi`, color: '#1971C2' }
  return { label: `${diffDays} hari lagi`, color: '#495057' }
}

export function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  })
}
