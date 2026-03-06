<script setup lang="ts">
import { computed } from 'vue'
import { getDueDateInfo, formatDate } from '@/plugins/date.helper'

interface Props {
  dueDate: string
}
const props = defineProps<Props>()

const info = computed(() => getDueDateInfo(props.dueDate))
const formatted = computed(() => formatDate(props.dueDate))
</script>

<template>
  <span class="due-date" :style="{ color: info.color }">
    {{ formatted }} &mdash; {{ info.label }}
  </span>
</template>

<style scoped>
.due-date {
  font-size: 13px;
  font-weight: 500;
}
</style>
