import http from './http'
import type { ApiResponse } from '@/types/api.types'
import type { Task } from '@/types/task.types'

interface TaskPayload {
  title: string
  description: string
  due_date: string
  category_id: number
  project_id: number
}

export function getTasks(params?: { search?: string; category_id?: number; project_id?: number }): Promise<ApiResponse<Task[]>> {
  return http.get('/api/tasks', { params }).then((r) => r.data)
}

export function createTask(payload: TaskPayload): Promise<ApiResponse<Task>> {
  return http.post('/api/tasks', payload).then((r) => r.data)
}

export function updateTask(id: number, payload: TaskPayload): Promise<ApiResponse<Task>> {
  return http.put(`/api/tasks/${id}`, payload).then((r) => r.data)
}

export function updateTaskCategory(id: number, categoryId: number): Promise<ApiResponse<Task>> {
  return http.patch(`/api/tasks/${id}/category`, { category_id: categoryId }).then((r) => r.data)
}

export function deleteTask(id: number): Promise<ApiResponse<null>> {
  return http.delete(`/api/tasks/${id}`).then((r) => r.data)
}
