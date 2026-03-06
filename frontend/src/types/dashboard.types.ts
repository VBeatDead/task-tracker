import type { Category } from './task.types'

export interface UpcomingTask {
  id: number
  title: string
  due_date: string
  project: { id: number; name: string }
  category: Category
}

export interface DashboardData {
  total_active_projects: number
  total_incomplete_tasks: number
  upcoming_tasks: UpcomingTask[]
}
