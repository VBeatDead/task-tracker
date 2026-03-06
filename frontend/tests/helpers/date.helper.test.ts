import { describe, it, expect } from 'vitest'
import { getDueDateInfo } from '@/plugins/date.helper'

function dateFromToday(offsetDays: number): string {
  const d = new Date()
  d.setDate(d.getDate() + offsetDays)
  return d.toISOString().split('T')[0]
}

describe('getDueDateInfo', () => {
  it('returns danger color for past dates', () => {
    const result = getDueDateInfo(dateFromToday(-3))
    expect(result.color).toBe('#C92A2A')
    expect(result.label).toBe('Overdue')
  })

  it('returns danger color for today', () => {
    const result = getDueDateInfo(dateFromToday(0))
    expect(result.color).toBe('#C92A2A')
    expect(result.label).toBe('Hari ini')
  })

  it('returns warning color for tomorrow', () => {
    const result = getDueDateInfo(dateFromToday(1))
    expect(result.color).toBe('#E67700')
    expect(result.label).toBe('Besok')
  })

  it('returns info color for 2-7 days ahead', () => {
    const result = getDueDateInfo(dateFromToday(5))
    expect(result.color).toBe('#1971C2')
  })

  it('returns neutral color for more than 7 days', () => {
    const result = getDueDateInfo(dateFromToday(10))
    expect(result.color).toBe('#495057')
  })
})
