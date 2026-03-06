import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import BadgeStatus from '@/components/BadgeStatus.vue'

describe('BadgeStatus', () => {
  it('renders "Active" for active status', () => {
    const wrapper = mount(BadgeStatus, { props: { status: 'active' } })
    expect(wrapper.text()).toContain('Active')
  })

  it('renders "Archived" for archived status', () => {
    const wrapper = mount(BadgeStatus, { props: { status: 'archived' } })
    expect(wrapper.text()).toContain('Archived')
  })

  it('applies correct CSS class for active', () => {
    const wrapper = mount(BadgeStatus, { props: { status: 'active' } })
    expect(wrapper.find('span').classes()).toContain('badge-status--active')
  })

  it('applies correct CSS class for archived', () => {
    const wrapper = mount(BadgeStatus, { props: { status: 'archived' } })
    expect(wrapper.find('span').classes()).toContain('badge-status--archived')
  })
})
