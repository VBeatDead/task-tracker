import type { Project } from './project.types'

export interface Category {
  id: number
  name: string
}

export interface Task {
  id: number
  title: string
  description: string
  due_date: string
  project_id: number
  project?: Pick<Project, 'id' | 'name'>
  category_id: number
  category?: Category
  created_by: number
  deleted_at: string | null
}
