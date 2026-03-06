<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import AppModal from './AppModal.vue'
import { createProject, updateProject } from '@/services/project.service'
import { toast } from '@/plugins/toast.helper'
import type { Project } from '@/types/project.types'

interface Props {
  show: boolean
  project?: Project
}
const props = defineProps<Props>()

interface Emits {
  (e: 'update:show', value: boolean): void
  (e: 'saved'): void
}
const emit = defineEmits<Emits>()

const isEdit = () => !!props.project

const name = ref('')
const description = ref('')
const status = ref<'active' | 'archived'>('active')
const loading = ref(false)
const fieldErrors = ref<Record<string, string[]>>({})

watch(() => props.show, (visible) => {
  if (!visible) return
  fieldErrors.value = {}
  if (props.project) {
    name.value = props.project.name
    description.value = props.project.description
    status.value = props.project.status
  } else {
    name.value = ''
    description.value = ''
    status.value = 'active'
  }
})

async function submit(): Promise<void> {
  fieldErrors.value = {}
  loading.value = true
  try {
    const payload = { name: name.value, description: description.value, status: status.value }
    if (isEdit() && props.project) {
      await updateProject(props.project.id, payload)
    } else {
      await createProject(payload)
    }
    toast.success(`Project berhasil ${isEdit() ? 'diperbarui' : 'dibuat'}.`)
    emit('saved')
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
    :title="isEdit() ? 'Edit Project' : 'Tambah Project'"
    @update:show="emit('update:show', $event)"
  >
    <form @submit.prevent="submit">
      <div class="form-group">
        <label class="form-label">Nama Project</label>
        <input
          v-model="name"
          class="form-input"
          :class="{ 'has-error': fieldErrors.name }"
          type="text"
          placeholder="Nama project"
        />
        <span v-if="fieldErrors.name" class="form-error">{{ fieldErrors.name[0] }}</span>
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea
          v-model="description"
          class="form-input"
          :class="{ 'has-error': fieldErrors.description }"
          placeholder="Deskripsi project"
        />
        <span v-if="fieldErrors.description" class="form-error">{{ fieldErrors.description[0] }}</span>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select v-model="status" class="form-input" :class="{ 'has-error': fieldErrors.status }">
          <option value="active">Active</option>
          <option value="archived">Archived</option>
        </select>
        <span v-if="fieldErrors.status" class="form-error">{{ fieldErrors.status[0] }}</span>
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
