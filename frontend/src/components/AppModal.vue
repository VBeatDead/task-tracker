<script setup lang="ts">
interface Props {
  show: boolean
  title: string
}
const props = defineProps<Props>()

interface Emits {
  (e: 'update:show', value: boolean): void
}
const emit = defineEmits<Emits>()

function close(): void {
  emit('update:show', false)
}
</script>

<template>
  <Teleport to="body">
    <div v-if="props.show" class="modal-overlay" @click.self="close">
      <div class="modal-container" role="dialog" :aria-label="props.title">
        <div class="modal-header">
          <h3 class="modal-title">{{ props.title }}</h3>
          <button class="modal-close" type="button" @click="close">&times;</button>
        </div>
        <div class="modal-body">
          <slot />
        </div>
        <div v-if="$slots.footer" class="modal-footer">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}
.modal-container {
  background: #fff;
  border-radius: 8px;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #dee2e6;
}
.modal-title {
  font-size: 16px;
  font-weight: 600;
  color: #1a1b2e;
  margin: 0;
}
.modal-close {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #495057;
  line-height: 1;
  padding: 0 4px;
}
.modal-close:hover {
  color: #1a1b2e;
}
.modal-body {
  padding: 20px;
}
.modal-footer {
  padding: 12px 20px;
  border-top: 1px solid #dee2e6;
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}
</style>
