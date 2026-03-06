import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import BadgeCategory from '@/components/BadgeCategory.vue'

describe('BadgeCategory', () => {
  it('renders category name', () => {
    const wrapper = mount(BadgeCategory, { props: { name: 'Todo' } })
    expect(wrapper.text()).toBe('Todo')
  })

  it('renders In Progress', () => {
    const wrapper = mount(BadgeCategory, { props: { name: 'In Progress' } })
    expect(wrapper.text()).toBe('In Progress')
  })

  it('applies background color for Todo', () => {
    const wrapper = mount(BadgeCategory, { props: { name: 'Todo' } })
    const style = wrapper.find('span').attributes('style') ?? ''
    expect(style).toMatch(/background-color:\s*(rgb\(255,\s*243,\s*191\)|#FFF3BF)/i)
  })

  it('applies background color for Done', () => {
    const wrapper = mount(BadgeCategory, { props: { name: 'Done' } })
    const style = wrapper.find('span').attributes('style') ?? ''
    expect(style).toMatch(/background-color:\s*(rgb\(211,\s*249,\s*216\)|#D3F9D8)/i)
  })
})
