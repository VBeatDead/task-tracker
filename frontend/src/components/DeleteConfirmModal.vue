<script setup lang="ts">
import AppModal from './AppModal.vue'

interface Props {
  show: boolean
  taskTitle: string
  loading?: boolean
}
const props = defineProps<Props>()

interface Emits {
  (e: 'update:show', value: boolean): void
  (e: 'confirmed'): void
}
const emit = defineEmits<Emits>()
</script>

<template>
  <AppModal :show="props.show" title="Hapus Task?" @update:show="emit('update:show', $event)">
    <p class="confirm-text">
      Yakin ingin menghapus task <strong>{{ props.taskTitle }}</strong>?
      Data masih tersimpan (soft delete).
    </p>
    <template #footer>
      <button class="btn btn-secondary" type="button" @click="emit('update:show', false)">Batal</button>
      <button class="btn btn-danger" type="button" :disabled="props.loading" @click="emit('confirmed')">
        {{ props.loading ? 'Menghapus...' : 'Ya, Hapus' }}
      </button>
    </template>
  </AppModal>
</template>

<style scoped>
.confirm-text { margin: 0; color: #495057; line-height: 1.6; }
</style>
