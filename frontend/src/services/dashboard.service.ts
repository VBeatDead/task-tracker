import http from './http'
import type { ApiResponse } from '@/types/api.types'
import type { DashboardData } from '@/types/dashboard.types'

export function getDashboard(): Promise<ApiResponse<DashboardData>> {
  return http.get('/api/dashboard').then((r) => r.data)
}
