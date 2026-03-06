import type { Task } from './task.types'

export type ProjectStatus = 'active' | 'archived'

export interface Project {
  id: number
  name: string
  description: string
  status: ProjectStatus
  task_count: number
  created_at: string
  created_by: number
  tasks?: Task[]
}
