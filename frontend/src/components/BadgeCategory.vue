<script setup lang="ts">
interface Props {
  name: string
}
const props = defineProps<Props>()

const colorMap: Record<string, string> = {
  Todo: '#FFF3BF',
  'In Progress': '#D0EBFF',
  Testing: '#FFE8CC',
  Done: '#D3F9D8',
  Pending: '#F3D9FA',
}

const textColorMap: Record<string, string> = {
  Todo: '#E67700',
  'In Progress': '#1971C2',
  Testing: '#D9480F',
  Done: '#2F9E44',
  Pending: '#9C36B5',
}

const backgroundColor = colorMap[props.name] ?? '#E9ECEF'
const textColor = textColorMap[props.name] ?? '#495057'
</script>

<template>
  <span
    class="badge-category"
    :style="{ backgroundColor, color: textColor }"
  >
    {{ props.name }}
  </span>
</template>

<style scoped>
.badge-category {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}
</style>
