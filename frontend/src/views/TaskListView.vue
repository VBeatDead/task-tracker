<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import AppLayout from '@/components/AppLayout.vue'
import BadgeCategory from '@/components/BadgeCategory.vue'
import DueDateIndicator from '@/components/DueDateIndicator.vue'
import TaskFormModal from '@/components/TaskFormModal.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'
import { getTasks, deleteTask } from '@/services/task.service'
import { getCategories } from '@/services/category.service'
import { getProjects } from '@/services/project.service'
import { toast } from '@/plugins/toast.helper'
import type { Task, Category } from '@/types/task.types'
import type { Project } from '@/types/project.types'

const tasks = ref<Task[]>([])
const categories = ref<Category[]>([])
const projects = ref<Project[]>([])
const loading = ref(true)
const searchQuery = ref('')
const categoryFilter = ref<number | ''>('')
const projectFilter = ref<number | ''>('')
const showTaskForm = ref(false)
const taskFormMode = ref<'create' | 'edit'>('create')
const editingTask = ref<Task | undefined>(undefined)
const showDeleteModal = ref(false)
const deletingTask = ref<Task | null>(null)
const deleteLoading = ref(false)

async function fetchTasks(): Promise<void> {
  loading.value = true
  try {
    const params: Record<string, string | number> = {}
    if (searchQuery.value) params.search = searchQuery.value
    if (categoryFilter.value) params.category_id = categoryFilter.value
    if (projectFilter.value) params.project_id = projectFilter.value
    const res = await getTasks(params)
    tasks.value = res.data
  } finally {
    loading.value = false
  }
}

watch(searchQuery, (_, __, onCleanup) => {
  const timer = setTimeout(() => fetchTasks(), 300)
  onCleanup(() => clearTimeout(timer))
})
watch(categoryFilter, () => fetchTasks())
watch(projectFilter, () => fetchTasks())

onMounted(async () => {
  const [catRes, projRes] = await Promise.all([getCategories(), getProjects()])
  categories.value = catRes.data
  projects.value = projRes.data
  await fetchTasks()
})

function openCreate(): void {
  editingTask.value = undefined
  taskFormMode.value = 'create'
  showTaskForm.value = true
}

function openEdit(task: Task): void {
  editingTask.value = task
  taskFormMode.value = 'edit'
  showTaskForm.value = true
}

function onTaskSaved(savedTask: Task): void {
  if (taskFormMode.value === 'edit') {
    const idx = tasks.value.findIndex(t => t.id === savedTask.id)
    if (idx !== -1) tasks.value.splice(idx, 1, savedTask)
  } else {
    fetchTasks()
  }
}

function openDelete(task: Task): void {
  deletingTask.value = task
  showDeleteModal.value = true
}

async function confirmDelete(): Promise<void> {
  if (!deletingTask.value) return
  deleteLoading.value = true
  try {
    await deleteTask(deletingTask.value.id)
    tasks.value = tasks.value.filter(t => t.id !== deletingTask.value!.id)
    toast.success('Task berhasil dihapus.')
    showDeleteModal.value = false
  } catch {
    toast.error('Gagal menghapus task.')
  } finally {
    deleteLoading.value = false
  }
}
</script>

<template>
  <AppLayout>
    <div class="page-wrapper">
      <div class="page-header">
        <h1 class="page-title">Tasks</h1>
        <button class="btn btn-primary" type="button" @click="openCreate">+ Tambah Task</button>
      </div>

      <div class="filter-bar">
        <input v-model="searchQuery" class="form-input filter-input" type="text" placeholder="Cari judul task..." />
        <select v-model="categoryFilter" class="form-input filter-select">
          <option value="">Semua Kategori</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <select v-model="projectFilter" class="form-input filter-select">
          <option value="">Semua Project</option>
          <option v-for="proj in projects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
        </select>
      </div>

      <div class="table-wrapper">
        <div v-if="loading" class="skeleton-table">
          <div v-for="i in 5" :key="i" class="skeleton-row"><div class="skeleton skeleton-text" /></div>
        </div>
        <table v-else-if="tasks.length > 0" class="data-table">
          <thead>
            <tr>
              <th>Judul</th>
              <th>Project</th>
              <th>Kategori</th>
              <th>Due Date</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="task in tasks" :key="task.id">
              <td class="col-title">{{ task.title }}</td>
              <td>{{ task.project?.name ?? '-' }}</td>
              <td><BadgeCategory v-if="task.category" :name="task.category.name" /></td>
              <td><DueDateIndicator :due-date="task.due_date" /></td>
              <td class="col-actions">
                <button class="btn btn-ghost btn-sm" type="button" @click="openEdit(task)">Edit</button>
                <button class="btn btn-ghost btn-sm action-delete" type="button" @click="openDelete(task)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="empty-state">Belum ada task.</div>
      </div>
    </div>

    <TaskFormModal v-model:show="showTaskForm" :mode="taskFormMode" :task="editingTask" @saved="onTaskSaved" />
    <DeleteConfirmModal
      v-model:show="showDeleteModal"
      :task-title="deletingTask?.title ?? ''"
      :loading="deleteLoading"
      @confirmed="confirmDelete"
    />
  </AppLayout>
</template>

<style scoped>
.col-title { font-weight: 600; max-width: 280px; }
.col-actions { display: flex; gap: 6px; }
.action-delete { color: #C92A2A; }
.action-delete:hover { background: #fff0f0; }
.skeleton-table { padding: 8px 0; }
.skeleton-row { padding: 14px 16px; border-bottom: 1px solid #f1f3f5; }
.skeleton-text { height: 16px; width: 50%; }
</style>
