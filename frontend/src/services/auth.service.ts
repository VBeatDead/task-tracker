import http from './http'
import type { ApiResponse } from '@/types/api.types'
import type { User } from '@/types/user.types'

interface LoginResponse {
  token: string
  user: User
}

export function login(email: string, password: string): Promise<ApiResponse<LoginResponse>> {
  return http.post('/api/auth/login', { email, password }).then((r) => r.data)
}

export function logout(): Promise<ApiResponse<null>> {
  return http.delete('/api/auth/logout').then((r) => r.data)
}
