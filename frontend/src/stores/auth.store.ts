import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/user.types'
import * as authService from '@/services/auth.service'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(null)
  const user = ref<User | null>(null)

  const isLoggedIn = computed(() => token.value !== null)
  const currentUser = computed(() => user.value)

  async function login(email: string, password: string): Promise<void> {
    const res = await authService.login(email, password)
    token.value = res.data.token
    user.value = res.data.user
    localStorage.setItem('auth_token', res.data.token)
  }

  async function logout(): Promise<void> {
    try {
      await authService.logout()
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
    }
  }

  function initFromStorage(): void {
    const storedToken = localStorage.getItem('auth_token')
    if (storedToken) token.value = storedToken
  }

  return { token, user, isLoggedIn, currentUser, login, logout, initFromStorage }
})
