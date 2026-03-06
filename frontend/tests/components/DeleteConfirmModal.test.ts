import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'

const AppModalStub = {
  template: '<div><slot /><slot name="footer" /></div>',
  props: ['show', 'title'],
}

const mountModal = (props: Record<string, unknown>) =>
  mount(DeleteConfirmModal, {
    props,
    global: { stubs: { AppModal: AppModalStub } },
  })

describe('DeleteConfirmModal', () => {
  it('renders task title in confirmation text', () => {
    const wrapper = mountModal({ show: true, taskTitle: 'Setup Database' })
    expect(wrapper.text()).toContain('Setup Database')
  })

  it('emits confirmed when "Ya, Hapus" button clicked', async () => {
    const wrapper = mountModal({ show: true, taskTitle: 'Task X' })
    await wrapper.find('.btn-danger').trigger('click')
    expect(wrapper.emitted('confirmed')).toBeTruthy()
  })

  it('"Ya, Hapus" button is disabled when loading is true', () => {
    const wrapper = mountModal({ show: true, taskTitle: 'Task X', loading: true })
    const btn = wrapper.find('.btn-danger').element as HTMLButtonElement
    expect(btn.disabled).toBe(true)
  })

  it('shows "Menghapus..." text when loading', () => {
    const wrapper = mountModal({ show: true, taskTitle: 'Task X', loading: true })
    expect(wrapper.find('.btn-danger').text()).toBe('Menghapus...')
  })

  it('emits update:show=false when Batal clicked', async () => {
    const wrapper = mountModal({ show: true, taskTitle: 'Task X' })
    await wrapper.find('.btn-secondary').trigger('click')
    expect(wrapper.emitted('update:show')).toBeTruthy()
    expect(wrapper.emitted('update:show')![0]).toEqual([false])
  })
})
