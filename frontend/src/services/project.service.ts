import http from './http'
import type { ApiResponse } from '@/types/api.types'
import type { Project } from '@/types/project.types'

export function getProjects(params?: { search?: string; status?: string }): Promise<ApiResponse<Project[]>> {
  return http.get('/api/projects', { params }).then((r) => r.data)
}

export function getProject(id: number): Promise<ApiResponse<Project>> {
  return http.get(`/api/projects/${id}`).then((r) => r.data)
}

export function createProject(payload: Pick<Project, 'name' | 'description' | 'status'>): Promise<ApiResponse<Project>> {
  return http.post('/api/projects', payload).then((r) => r.data)
}

export function updateProject(id: number, payload: Pick<Project, 'name' | 'description' | 'status'>): Promise<ApiResponse<Project>> {
  return http.put(`/api/projects/${id}`, payload).then((r) => r.data)
}
