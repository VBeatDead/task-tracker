<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import AppModal from './AppModal.vue'
import { createTask, updateTask } from '@/services/task.service'
import { getProjects } from '@/services/project.service'
import { getCategories } from '@/services/category.service'
import { toast } from '@/plugins/toast.helper'
import type { Task, Category } from '@/types/task.types'
import type { Project } from '@/types/project.types'

interface Props {
  show: boolean
  mode: 'create' | 'edit'
  task?: Task
  lockedProjectId?: number
}
const props = defineProps<Props>()

interface Emits {
  (e: 'update:show', value: boolean): void
  (e: 'saved', task: Task): void
}
const emit = defineEmits<Emits>()

const title = ref('')
const description = ref('')
const dueDate = ref('')
const categoryId = ref<number>(1)
const projectId = ref<number | null>(null)
const loading = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})
const categories = ref<Category[]>([])
const projects = ref<Project[]>([])

const todayStr = new Date().toISOString().split('T')[0]

onMounted(async () => {
  const [catRes, projRes] = await Promise.all([getCategories(), getProjects()])
  categories.value = catRes.data
  projects.value = projRes.data
})

watch(() => props.show, (visible) => {
  if (!visible) return
  fieldErrors.value = {}
  if (props.mode === 'edit' && props.task) {
    title.value = props.task.title
    description.value = props.task.description
    dueDate.value = (props.task.due_date ?? '').split('T')[0] ?? ''
    categoryId.value = props.task.category_id
    projectId.value = props.task.project_id
  } else {
    title.value = ''
    description.value = ''
    dueDate.value = ''
    categoryId.value = categories.value[0]?.id ?? 1
    projectId.value = props.lockedProjectId ?? null
  }
})

async function submit(): Promise<void> {
  fieldErrors.value = {}
  if (!projectId.value) {
    fieldErrors.value = { project_id: ['Project wajib dipilih.'] }
    return
  }
  loading.value = true
  try {
    const payload = {
      title: title.value,
      description: description.value,
      due_date: dueDate.value,
      category_id: categoryId.value,
      project_id: projectId.value,
    }
    let savedTask: Task
    if (props.mode === 'edit' && props.task) {
      const res = await updateTask(props.task.id, payload)
      savedTask = res.data
    } else {
      const res = await createTask(payload)
      savedTask = res.data
    }
    toast.success(`Task berhasil ${props.mode === 'edit' ? 'diperbarui' : 'dibuat'}.`)
    emit('saved', savedTask)
    emit('update:show', false)
  } catch (error) {
    if (axios.isAxiosError(error) && error.response?.status === 422) {
      fieldErrors.value = error.response.data.errors ?? {}
    } else {
      toast.error('Terjadi kesalahan server. Coba lagi.')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <AppModal
    :show="props.show"
    :title="props.mode === 'edit' ? 'Edit Task' : 'Tambah Task'"
    @update:show="emit('update:show', $event)"
  >
    <form @submit.prevent="submit">
      <div class="form-group">
        <label class="form-label">Judul</label>
        <input
          v-model="title"
          class="form-input"
          :class="{ 'has-error': fieldErrors.title }"
          type="text"
          placeholder="Judul task"
        />
        <span v-if="fieldErrors.title" class="form-error">{{ fieldErrors.title[0] }}</span>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea
          v-model="description"
          class="form-input"
          :class="{ 'has-error': fieldErrors.description }"
          placeholder="Deskripsi task"
        />
        <span v-if="fieldErrors.description" class="form-error">{{ fieldErrors.description[0] }}</span>
      </div>
      <div class="form-group">
        <label class="form-label">Due Date</label>
        <input
          v-model="dueDate"
          class="form-input"
          :class="{ 'has-error': fieldErrors.due_date }"
          type="date"
          :min="props.mode === 'create' ? todayStr : undefined"
        />
        <span v-if="fieldErrors.due_date" class="form-error">{{ fieldErrors.due_date[0] }}</span>
      </div>
      <div class="form-group">
        <label class="form-label">Kategori</label>
        <select v-model="categoryId" class="form-input" :class="{ 'has-error': fieldErrors.category_id }">
          <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
        </select>
        <span v-if="fieldErrors.category_id" class="form-error">{{ fieldErrors.category_id[0] }}</span>
      </div>
      <div class="form-group">
        <label class="form-label">Project</label>
        <select
          v-model="projectId"
          class="form-input"
          :class="{ 'has-error': fieldErrors.project_id }"
          :disabled="!!props.lockedProjectId"
        >
          <option :value="null" disabled>Pilih project...</option>
          <option v-for="proj in projects" :key="proj.id" :value="proj.id">{{ proj.name }}</option>
        </select>
        <span v-if="fieldErrors.project_id" class="form-error">{{ fieldErrors.project_id[0] }}</span>
      </div>
    </form>
    <template #footer>
      <button class="btn btn-secondary" type="button" @click="emit('update:show', false)">Batal</button>
      <button class="btn btn-primary" type="button" :disabled="loading" @click="submit">
        {{ loading ? 'Menyimpan...' : 'Simpan' }}
      </button>
    </template>
  </AppModal>
</template>
