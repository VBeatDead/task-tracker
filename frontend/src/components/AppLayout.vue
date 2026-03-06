<script setup lang="ts">
import { ref } from 'vue'
import AppSidebar from './AppSidebar.vue'

const sidebarOpen = ref(false)
</script>

<template>
  <div class="app-layout">
    <AppSidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

    <div v-if="sidebarOpen" class="sidebar-overlay" @click="sidebarOpen = false" />

    <main class="app-content">
      <div class="mobile-topbar">
        <button class="hamburger" type="button" aria-label="Buka menu" @click="sidebarOpen = true">
          <span /><span /><span />
        </button>
        <span class="topbar-logo">TaskTracker</span>
      </div>
      <slot />
    </main>
  </div>
</template>

<style scoped>
.app-layout {
  display: flex;
  min-height: 100vh;
  background: #f8f9fa;
}

.app-content {
  flex: 1;
  overflow-y: auto;
  min-width: 0;
}

.sidebar-overlay {
  display: none;
}

.mobile-topbar {
  display: none;
}

@media (max-width: 768px) {
  .sidebar-overlay {
    display: block;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 800;
  }

  .mobile-topbar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #1a1b2e;
    position: sticky;
    top: 0;
    z-index: 100;
  }

  .topbar-logo {
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  .hamburger {
    display: flex;
    flex-direction: column;
    gap: 5px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
  }

  .hamburger span {
    display: block;
    width: 22px;
    height: 2px;
    background: #fff;
    border-radius: 2px;
  }
}
</style>
