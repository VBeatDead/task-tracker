<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import AppLayout from '@/components/AppLayout.vue'
import BadgeStatus from '@/components/BadgeStatus.vue'
import ProjectFormModal from '@/components/ProjectFormModal.vue'
import { getProjects } from '@/services/project.service'
import type { Project } from '@/types/project.types'

const router = useRouter()
const projects = ref<Project[]>([])
const loading = ref(true)
const searchQuery = ref('')
const statusFilter = ref('')
const showFormModal = ref(false)
const editingProject = ref<Project | undefined>(undefined)

async function fetchProjects(): Promise<void> {
  loading.value = true
  try {
    const params: Record<string, string> = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (statusFilter.value) params.status = statusFilter.value
    const res = await getProjects(params)
    projects.value = res.data
  } finally {
    loading.value = false
  }
}

watch(searchQuery, (_, __, onCleanup) => {
  const timer = setTimeout(() => fetchProjects(), 300)
  onCleanup(() => clearTimeout(timer))
})

watch(statusFilter, () => fetchProjects())

onMounted(() => fetchProjects())

function openCreate(): void {
  editingProject.value = undefined
  showFormModal.value = true
}

function openEdit(project: Project): void {
  editingProject.value = project
  showFormModal.value = true
}

function goToDetail(id: number): void {
  router.push({ name: 'project-detail', params: { id } })
}
</script>

<template>
  <AppLayout>
    <div class="page-wrapper">
      <div class="page-header">
        <h1 class="page-title">Projects</h1>
        <button class="btn btn-primary" type="button" @click="openCreate">+ Tambah Project</button>
      </div>

      <div class="filter-bar">
        <input
          v-model="searchQuery"
          class="form-input filter-input"
          type="text"
          placeholder="Cari nama project..."
        />
        <select v-model="statusFilter" class="form-input filter-select">
          <option value="">Semua Status</option>
          <option value="active">Active</option>
          <option value="archived">Archived</option>
        </select>
      </div>

      <div class="table-wrapper">
        <div v-if="loading" class="skeleton-table">
          <div v-for="i in 3" :key="i" class="skeleton-row">
            <div class="skeleton skeleton-text" />
          </div>
        </div>
        <table v-else-if="projects.length > 0" class="data-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Deskripsi</th>
              <th>Status</th>
              <th>Task</th>
              <th>Dibuat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="project in projects" :key="project.id">
              <td class="col-name">{{ project.name }}</td>
              <td class="col-desc">{{ project.description }}</td>
              <td><BadgeStatus :status="project.status" /></td>
              <td>{{ project.task_count }}</td>
              <td>{{ new Date(project.created_at).toLocaleDateString('id-ID') }}</td>
              <td class="col-actions">
                <button class="btn btn-ghost btn-sm" type="button" @click="goToDetail(project.id)">Detail</button>
                <button class="btn btn-ghost btn-sm" type="button" @click="openEdit(project)">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty-state">Belum ada project.</div>
      </div>
    </div>

    <ProjectFormModal
      v-model:show="showFormModal"
      :project="editingProject"
      @saved="fetchProjects"
    />
  </AppLayout>
</template>

<style scoped>
.col-name { font-weight: 600; min-width: 140px; }
.col-desc { color: #6c757d; max-width: 280px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.col-actions { display: flex; gap: 6px; }
.skeleton-table { padding: 8px 0; }
.skeleton-row { padding: 14px 16px; border-bottom: 1px solid #f1f3f5; }
.skeleton-text { height: 16px; width: 55%; }
</style>
