import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import TaskCard from '@/components/TaskCard.vue'
import type { Task } from '@/types/task.types'

const mockTask: Task = {
  id: 1,
  title: 'Implementasi Login',
  description: 'Buat halaman login dengan validasi.',
  due_date: new Date(Date.now() + 5 * 86400000).toISOString().split('T')[0],
  project_id: 1,
  category_id: 1,
  created_by: 1,
  deleted_at: null,
}

describe('TaskCard', () => {
  it('renders task title', () => {
    const wrapper = mount(TaskCard, { props: { task: mockTask } })
    expect(wrapper.text()).toContain('Implementasi Login')
  })

  it('renders DueDateIndicator component', () => {
    const wrapper = mount(TaskCard, { props: { task: mockTask } })
    expect(wrapper.findComponent({ name: 'DueDateIndicator' }).exists()).toBe(true)
  })

  it('emits edit event with task when edit button clicked', async () => {
    const wrapper = mount(TaskCard, { props: { task: mockTask } })
    await wrapper.find('button:first-of-type').trigger('click')
    expect(wrapper.emitted('edit')).toBeTruthy()
    expect(wrapper.emitted('edit')![0]).toEqual([mockTask])
  })

  it('emits delete event with task when hapus button clicked', async () => {
    const wrapper = mount(TaskCard, { props: { task: mockTask } })
    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click')
    expect(wrapper.emitted('delete')).toBeTruthy()
    expect(wrapper.emitted('delete')![0]).toEqual([mockTask])
  })
})
