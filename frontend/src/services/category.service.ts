import http from './http'
import type { ApiResponse } from '@/types/api.types'
import type { Category } from '@/types/task.types'

export function getCategories(): Promise<ApiResponse<Category[]>> {
  return http.get('/api/categories').then((r) => r.data)
}
