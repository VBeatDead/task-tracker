import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import DueDateIndicator from '@/components/DueDateIndicator.vue'

function dateFromToday(offsetDays: number): string {
  const d = new Date()
  d.setDate(d.getDate() + offsetDays)
  return d.toISOString().split('T')[0]!
}

describe('DueDateIndicator', () => {
  it('shows danger color for past due date', () => {
    const wrapper = mount(DueDateIndicator, { props: { dueDate: dateFromToday(-3) } })
    expect(wrapper.find('.due-date').attributes('style')).toContain('color: rgb(201, 42, 42)')
    expect(wrapper.text()).toContain('Overdue')
  })

  it('shows danger color for today', () => {
    const wrapper = mount(DueDateIndicator, { props: { dueDate: dateFromToday(0) } })
    expect(wrapper.find('.due-date').attributes('style')).toContain('color: rgb(201, 42, 42)')
    expect(wrapper.text()).toContain('Hari ini')
  })

  it('shows warning color for tomorrow', () => {
    const wrapper = mount(DueDateIndicator, { props: { dueDate: dateFromToday(1) } })
    expect(wrapper.find('.due-date').attributes('style')).toContain('color: rgb(230, 119, 0)')
    expect(wrapper.text()).toContain('Besok')
  })

  it('shows info color for 2-7 days ahead', () => {
    const wrapper = mount(DueDateIndicator, { props: { dueDate: dateFromToday(5) } })
    expect(wrapper.find('.due-date').attributes('style')).toContain('color: rgb(25, 113, 194)')
  })

  it('shows neutral color for more than 7 days', () => {
    const wrapper = mount(DueDateIndicator, { props: { dueDate: dateFromToday(10) } })
    expect(wrapper.find('.due-date').attributes('style')).toContain('color: rgb(73, 80, 87)')
  })
})
