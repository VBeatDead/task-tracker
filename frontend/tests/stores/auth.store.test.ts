import { describe, it, expect, beforeEach, vi } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useAuthStore } from '@/stores/auth.store'

vi.mock('@/services/auth.service', () => ({
  login: vi.fn().mockResolvedValue({
    data: {
      token: 'test-token-123',
      user: { id: 1, name: 'Admin', email: 'admin@energeek.id', is_admin: true },
    },
    message: 'Login berhasil.',
  }),
  logout: vi.fn().mockResolvedValue({ message: 'Logout berhasil.' }),
}))

describe('useAuthStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
    localStorage.clear()
  })

  it('initializes with null token and user', () => {
    const store = useAuthStore()
    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isLoggedIn).toBe(false)
  })

  it('initFromStorage restores token from localStorage', () => {
    localStorage.setItem('auth_token', 'saved-token')
    const store = useAuthStore()
    store.initFromStorage()
    expect(store.token).toBe('saved-token')
    expect(store.isLoggedIn).toBe(true)
  })

  it('initFromStorage does nothing when localStorage is empty', () => {
    const store = useAuthStore()
    store.initFromStorage()
    expect(store.token).toBeNull()
    expect(store.isLoggedIn).toBe(false)
  })

  it('login sets token, user, and localStorage', async () => {
    const store = useAuthStore()
    await store.login('admin@energeek.id', 'password123')

    expect(store.token).toBe('test-token-123')
    expect(store.user?.email).toBe('admin@energeek.id')
    expect(store.isLoggedIn).toBe(true)
    expect(localStorage.getItem('auth_token')).toBe('test-token-123')
  })

  it('logout clears token, user, and localStorage', async () => {
    const store = useAuthStore()
    await store.login('admin@energeek.id', 'password123')

    await store.logout()

    expect(store.token).toBeNull()
    expect(store.user).toBeNull()
    expect(store.isLoggedIn).toBe(false)
    expect(localStorage.getItem('auth_token')).toBeNull()
  })
})
