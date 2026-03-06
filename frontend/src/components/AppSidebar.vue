<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import { toast } from '@/plugins/toast.helper'

interface Props {
  isOpen: boolean
}
defineProps<Props>()

interface Emits {
  (e: 'close'): void
}
const emit = defineEmits<Emits>()

const router = useRouter()
const authStore = useAuthStore()

async function handleLogout(): Promise<void> {
  await authStore.logout()
  toast.success('Logout berhasil.')
  router.push({ name: 'login' })
}
</script>

<template>
  <aside class="sidebar" :class="{ 'sidebar--open': isOpen }">
    <div class="sidebar-logo">
      <span class="logo-text">TaskTracker</span>
    </div>
    <nav class="sidebar-nav">
      <RouterLink class="nav-link" :to="{ name: 'dashboard' }" @click="emit('close')">Dashboard</RouterLink>
      <RouterLink class="nav-link" :to="{ name: 'projects' }" @click="emit('close')">Projects</RouterLink>
      <RouterLink class="nav-link" :to="{ name: 'tasks' }" @click="emit('close')">Tasks</RouterLink>
    </nav>
    <div class="sidebar-footer">
      <div v-if="authStore.currentUser" class="user-info">
        <span class="user-name">{{ authStore.currentUser.name }}</span>
        <span class="user-email">{{ authStore.currentUser.email }}</span>
      </div>
      <button class="logout-btn" type="button" @click="handleLogout">Logout</button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  width: 220px;
  min-height: 100vh;
  background: #1a1b2e;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.sidebar-logo {
  padding: 24px 20px 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.logo-text {
  color: #fff;
  font-size: 18px;
  font-weight: 700;
  letter-spacing: 0.5px;
}

.sidebar-nav {
  flex: 1;
  padding: 16px 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-link {
  display: block;
  padding: 10px 20px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.15s, color 0.15s;
}

.nav-link:hover,
.nav-link.router-link-active {
  background: rgba(59, 91, 219, 0.25);
  color: #fff;
}

.sidebar-footer {
  padding: 16px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.user-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  margin-bottom: 12px;
}

.user-name {
  color: #fff;
  font-size: 13px;
  font-weight: 600;
}

.user-email {
  color: rgba(255, 255, 255, 0.5);
  font-size: 11px;
}

.logout-btn {
  width: 100%;
  padding: 8px;
  background: rgba(201, 42, 42, 0.2);
  color: #ff6b6b;
  border: 1px solid rgba(201, 42, 42, 0.3);
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}

.logout-btn:hover {
  background: rgba(201, 42, 42, 0.35);
}

@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 900;
    transform: translateX(-100%);
    transition: transform 0.25s ease;
  }

  .sidebar--open {
    transform: translateX(0);
  }
}
</style>
