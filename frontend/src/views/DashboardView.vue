<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AppLayout from '@/components/AppLayout.vue'
import DueDateIndicator from '@/components/DueDateIndicator.vue'
import { getDashboard } from '@/services/dashboard.service'
import { toast } from '@/plugins/toast.helper'
import type { DashboardData } from '@/types/dashboard.types'

const loading = ref(true)
const dashboardData = ref<DashboardData | null>(null)

onMounted(async () => {
  try {
    const res = await getDashboard()
    dashboardData.value = res.data
  } catch {
    toast.error('Gagal memuat dashboard. Coba refresh halaman.')
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AppLayout>
    <div class="page-wrapper">
      <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
      </div>

      <div class="stat-cards">
        <div class="stat-card">
          <div class="stat-card-label">Project Aktif</div>
          <div v-if="loading" class="skeleton stat-skeleton" />
          <div v-else class="stat-card-value">{{ dashboardData?.total_active_projects ?? 0 }}</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-label">Task Belum Selesai</div>
          <div v-if="loading" class="skeleton stat-skeleton" />
          <div v-else class="stat-card-value">{{ dashboardData?.total_incomplete_tasks ?? 0 }}</div>
        </div>
      </div>

      <div class="upcoming-section">
        <h2 class="section-title">Task Mendekati Due Date</h2>
        <div v-if="loading" class="table-wrapper">
          <div v-for="i in 4" :key="i" class="skeleton-row">
            <div class="skeleton skeleton-text" />
          </div>
        </div>
        <div v-else-if="dashboardData?.upcoming_tasks.length === 0" class="empty-state">
          Tidak ada task yang mendekati due date 🎉
        </div>
        <div v-else class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>Task</th>
                <th>Project</th>
                <th>Due Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="task in dashboardData?.upcoming_tasks" :key="task.id">
                <td>{{ task.title }}</td>
                <td>{{ task.project.name }}</td>
                <td><DueDateIndicator :due-date="task.due_date" /></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
.stat-skeleton { height: 40px; width: 80px; margin-top: 8px; }
.section-title { font-size: 16px; font-weight: 700; color: #1a1b2e; margin-bottom: 16px; }
.skeleton-row { padding: 16px; border-bottom: 1px solid #f1f3f5; }
.skeleton-text { height: 16px; width: 60%; }
</style>
