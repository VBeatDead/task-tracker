<script setup lang="ts">
import type { ProjectStatus } from '@/types/project.types'

interface Props {
  status: ProjectStatus
}
const props = defineProps<Props>()

const labelMap: Record<ProjectStatus, string> = {
  active: 'Active',
  archived: 'Archived',
}
</script>

<template>
  <span
    class="badge-status"
    :class="`badge-status--${props.status}`"
  >
    {{ labelMap[props.status] }}
  </span>
</template>

<style scoped>
.badge-status {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}
.badge-status--active {
  background: #D3F9D8;
  color: #2F9E44;
}
.badge-status--archived {
  background: #E9ECEF;
  color: #495057;
}
</style>
