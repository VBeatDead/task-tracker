<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import draggable from 'vuedraggable'
import AppLayout from '@/components/AppLayout.vue'
import BadgeStatus from '@/components/BadgeStatus.vue'
import TaskCard from '@/components/TaskCard.vue'
import TaskFormModal from '@/components/TaskFormModal.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'
import ProjectFormModal from '@/components/ProjectFormModal.vue'
import { getProject } from '@/services/project.service'
import { updateTaskCategory, deleteTask } from '@/services/task.service'
import { toast } from '@/plugins/toast.helper'
import type { Project } from '@/types/project.types'
import type { Task } from '@/types/task.types'
import { CATEGORIES } from '@/constants/categories'

const route = useRoute()
const projectId = Number(route.params.id)

const project = ref<Project | null>(null)
const taskGroups = ref<Record<number, Task[]>>({1:[], 2:[], 3:[], 4:[], 5:[]})
const loading = ref(true)

const showEditProject = ref(false)
const showTaskForm = ref(false)
const taskFormMode = ref<'create' | 'edit'>('create')
const editingTask = ref<Task | undefined>(undefined)
const showDeleteModal = ref(false)
const deletingTask = ref<Task | null>(null)
const deleteLoading = ref(false)

const totalTasks = computed(() => Object.values(taskGroups.value).flat().length)
const doneTasks = computed(() => taskGroups.value[4]?.length ?? 0)

async function loadProject(): Promise<void> {
  loading.value = true
  try {
    const res = await getProject(projectId)
    project.value = res.data
    distributeToGroups(res.data.tasks ?? [])
  } catch {
    toast.error('Gagal memuat project. Coba refresh halaman.')
  } finally {
    loading.value = false
  }
}

function distributeToGroups(tasks: Task[]): void {
  const groups: Record<number, Task[]> = {1:[], 2:[], 3:[], 4:[], 5:[]}
  for (const task of tasks) {
    if (groups[task.category_id]) groups[task.category_id]!.push(task)
  }
  taskGroups.value = groups
}

async function onTaskMoved(event: { added?: { element: Task } }, newCategoryId: number): Promise<void> {
  if (!event.added) return
  const movedTask = event.added.element
  const prevCategoryId = movedTask.category_id
  movedTask.category_id = newCategoryId
  try {
    await updateTaskCategory(movedTask.id, newCategoryId)
  } catch {
    movedTask.category_id = prevCategoryId
    const destGroup = taskGroups.value[newCategoryId]
    const srcGroup = taskGroups.value[prevCategoryId]
    if (destGroup && srcGroup) {
      const idx = destGroup.findIndex(t => t.id === movedTask.id)
      if (idx !== -1) destGroup.splice(idx, 1)
      srcGroup.push(movedTask)
    }
    toast.error('Gagal memindahkan task. Silakan coba lagi.')
  }
}

function openCreateTask(): void {
  editingTask.value = undefined
  taskFormMode.value = 'create'
  showTaskForm.value = true
}

function openEditTask(task: Task): void {
  editingTask.value = task
  taskFormMode.value = 'edit'
  showTaskForm.value = true
}

function onTaskSaved(savedTask: Task): void {
  if (taskFormMode.value === 'edit' && editingTask.value) {
    const prevCategoryId = editingTask.value.category_id
    const prevGroup = taskGroups.value[prevCategoryId]
    const idx = prevGroup?.findIndex(t => t.id === savedTask.id) ?? -1
    if (prevCategoryId === savedTask.category_id) {
      if (idx !== -1) prevGroup?.splice(idx, 1, savedTask)
    } else {
      if (idx !== -1) prevGroup?.splice(idx, 1)
      taskGroups.value[savedTask.category_id]?.push(savedTask)
    }
  } else {
    taskGroups.value[savedTask.category_id]?.push(savedTask)
  }
}

function openDeleteTask(task: Task): void {
  deletingTask.value = task
  showDeleteModal.value = true
}

async function confirmDelete(): Promise<void> {
  if (!deletingTask.value) return
  deleteLoading.value = true
  try {
    await deleteTask(deletingTask.value.id)
    const group = taskGroups.value[deletingTask.value.category_id]
    const taskId = deletingTask.value.id
    const idx = group?.findIndex(t => t.id === taskId) ?? -1
    if (idx !== -1) group?.splice(idx, 1)
    toast.success('Task berhasil dihapus.')
    showDeleteModal.value = false
  } catch {
    toast.error('Gagal menghapus task.')
  } finally {
    deleteLoading.value = false
  }
}

onMounted(() => loadProject())
</script>

<template>
  <AppLayout>
    <div class="page-wrapper">
      <div v-if="loading" class="skeleton-header">
        <div class="skeleton skeleton-title" />
        <div class="skeleton skeleton-desc" />
      </div>
      <template v-else-if="project">
        <div class="project-header">
          <div class="project-info">
            <div class="project-title-row">
              <h1 class="page-title">{{ project.name }}</h1>
              <BadgeStatus :status="project.status" />
            </div>
            <p class="project-desc">{{ project.description }}</p>
            <p class="project-meta">
              Total Task: {{ totalTasks }} &nbsp;|&nbsp; Selesai: {{ doneTasks }}/{{ totalTasks }}
            </p>
          </div>
          <div class="project-actions">
            <button class="btn btn-ghost" type="button" @click="showEditProject = true">Edit Project</button>
            <button class="btn btn-primary" type="button" @click="openCreateTask">+ Tambah Task</button>
          </div>
        </div>

        <div class="kanban-board">
          <div v-for="cat in CATEGORIES" :key="cat.id" class="kanban-column">
            <div class="kanban-header">
              <span class="kanban-name">{{ cat.name }}</span>
              <span class="kanban-count">{{ taskGroups[cat.id]?.length ?? 0 }}</span>
            </div>
            <draggable
              v-model="taskGroups[cat.id]"
              group="tasks"
              item-key="id"
              class="kanban-list"
              ghost-class="ghost-card"
              @change="(e: { added?: { element: Task } }) => onTaskMoved(e, cat.id)"
            >
              <template #item="{ element }">
                <TaskCard
                  :task="element"
                  @edit="openEditTask"
                  @delete="openDeleteTask"
                />
              </template>
            </draggable>
          </div>
        </div>
      </template>
    </div>

    <ProjectFormModal v-model:show="showEditProject" :project="project ?? undefined" @saved="loadProject" />
    <TaskFormModal
      v-model:show="showTaskForm"
      :mode="taskFormMode"
      :task="editingTask"
      :locked-project-id="projectId"
      @saved="onTaskSaved"
    />
    <DeleteConfirmModal
      v-model:show="showDeleteModal"
      :task-title="deletingTask?.title ?? ''"
      :loading="deleteLoading"
      @confirmed="confirmDelete"
    />
  </AppLayout>
</template>

<style scoped>
.skeleton-header { margin-bottom: 24px; }
.skeleton-title { height: 28px; max-width: 300px; width: 70%; margin-bottom: 12px; }
.skeleton-desc { height: 16px; max-width: 500px; width: 90%; }
.project-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; gap: 16px; }
.project-title-row { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; flex-wrap: wrap; }
.project-desc { margin: 0 0 6px; color: #6c757d; font-size: 13px; }
.project-meta { margin: 0; font-size: 13px; color: #495057; font-weight: 500; }
.project-actions { display: flex; gap: 8px; flex-shrink: 0; }
.kanban-board { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 16px; align-items: flex-start; scroll-snap-type: x mandatory; }
.kanban-column { background: #f1f3f5; border-radius: 8px; padding: 12px; min-width: 220px; flex: 0 0 220px; scroll-snap-align: start; }
.kanban-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.kanban-name { font-size: 13px; font-weight: 700; color: #1a1b2e; }
.kanban-count { background: #dee2e6; color: #6c757d; font-size: 11px; font-weight: 700; border-radius: 10px; padding: 2px 8px; }
.kanban-list { min-height: 60px; }
.ghost-card { opacity: 0.4; }

@media (max-width: 768px) {
  .project-header { flex-direction: column; }
  .project-actions { width: 100%; }
  .project-actions .btn { flex: 1; justify-content: center; }
  .kanban-board { gap: 10px; }
  .kanban-column { min-width: 200px; flex: 0 0 200px; }
}

@media (max-width: 480px) {
  .kanban-column { min-width: 80vw; flex: 0 0 80vw; }
}
</style>
