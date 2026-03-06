<script setup lang="ts">
import DueDateIndicator from './DueDateIndicator.vue'
import type { Task } from '@/types/task.types'

interface Props {
  task: Task
}
const props = defineProps<Props>()

interface Emits {
  (e: 'edit', task: Task): void
  (e: 'delete', task: Task): void
}
const emit = defineEmits<Emits>()
</script>

<template>
  <div class="task-card">
    <p class="task-title">{{ props.task.title }}</p>
    <DueDateIndicator :due-date="props.task.due_date" />
    <div class="task-actions">
      <button class="btn btn-ghost btn-sm" type="button" @click="emit('edit', props.task)">Edit</button>
      <button class="btn btn-ghost btn-sm action-delete" type="button" @click="emit('delete', props.task)">Hapus</button>
    </div>
  </div>
</template>

<style scoped>
.task-card {
  background: #fff;
  border-radius: 6px;
  padding: 12px;
  margin-bottom: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,.08);
  cursor: grab;
}
.task-card:active { cursor: grabbing; }
.task-title { margin: 0 0 6px; font-size: 13px; font-weight: 600; color: #212529; line-height: 1.4; }
.task-actions { display: flex; gap: 6px; margin-top: 8px; }
.action-delete { color: #C92A2A; }
.action-delete:hover { background: #fff0f0; }
</style>
