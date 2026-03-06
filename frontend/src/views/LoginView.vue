<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth.store'
import { toast } from '@/plugins/toast.helper'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})

async function handleLogin(): Promise<void> {
  fieldErrors.value = {}
  loading.value = true
  try {
    await authStore.login(email.value, password.value)
    router.push({ name: 'dashboard' })
  } catch (error) {
    if (axios.isAxiosError(error)) {
      if (error.response?.status === 422) {
        fieldErrors.value = error.response.data.errors ?? {}
      } else if (error.response?.status === 401) {
        toast.error('Email atau password salah.')
      } else if (error.response?.status && error.response.status >= 500) {
        toast.error('Terjadi kesalahan server. Coba lagi.')
      } else {
        toast.error('Koneksi gagal. Periksa jaringan Anda.')
      }
    } else {
      toast.error('Koneksi gagal. Periksa jaringan Anda.')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-logo">
        <span class="logo-dark">Task</span><span class="logo-blue">Tracker</span>
      </div>
      <form class="login-form" @submit.prevent="handleLogin">
        <div class="form-group">
          <label class="form-label">Email</label>
          <input
            v-model="email"
            class="form-input"
            :class="{ 'has-error': fieldErrors.email }"
            type="email"
            placeholder="admin@energeek.id"
            autocomplete="email"
          />
          <span v-if="fieldErrors.email" class="form-error">{{ fieldErrors.email[0] }}</span>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input
            v-model="password"
            class="form-input"
            :class="{ 'has-error': fieldErrors.password }"
            type="password"
            placeholder="Password"
            autocomplete="current-password"
          />
          <span v-if="fieldErrors.password" class="form-error">{{ fieldErrors.password[0] }}</span>
        </div>
        <button class="btn btn-primary btn-full" type="submit" :disabled="loading">
          {{ loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  background: #1a1b2e;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
}
.login-card {
  background: #fff;
  border-radius: 12px;
  padding: 40px 36px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 8px 32px rgba(0,0,0,.25);
}
.login-logo {
  text-align: center;
  font-size: 28px;
  font-weight: 800;
  margin-bottom: 32px;
}
.logo-dark { color: #1a1b2e; }
.logo-blue { color: #3B5BDB; }
.login-form { display: flex; flex-direction: column; gap: 0; }
.btn-full { width: 100%; justify-content: center; padding: 11px; font-size: 14px; margin-top: 8px; }
</style>
