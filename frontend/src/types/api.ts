/**
 * API Type Definitions
 *
 * TypeScript interfaces matching Laravel backend resources.
 * These types ensure type safety across API interactions.
 */

// User types matching UserResource from backend
export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
  member_profile?: MemberProfile
}

// Member types matching MemberResource from backend
export interface MemberProfile {
  id: number
  user_id: number
  first_name: string
  last_name: string
  full_name: string
  phone: string | null
  date_of_birth: string | null
  gender: string | null
  address: string | null
  city: string | null
  state: string | null
  postal_code: string | null
  membership_start_date: string
  membership_end_date: string | null
  membership_status: 'active' | 'inactive' | 'expired'
  membership_type: 'basic' | 'premium' | 'vip'
  is_expired: boolean
  emergency_contact_name: string | null
  emergency_contact_phone: string | null
  created_at: string
  updated_at: string
  user?: User
}

// Request types
export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface CreateMemberRequest {
  first_name: string
  last_name: string
  phone?: string
  date_of_birth?: string
  gender?: string
  address?: string
  city?: string
  state?: string
  postal_code?: string
  membership_start_date: string
  membership_end_date?: string
  membership_status: string
  membership_type: string
  emergency_contact_name?: string
  emergency_contact_phone?: string
}

export type UpdateMemberRequest = Partial<CreateMemberRequest>

// Pagination types
export interface PaginationMeta {
  total: number          // Total number of records
  per_page: number       // Records per page
  current_page: number   // Current page number (1-indexed)
  last_page: number      // Last page number
  from: number | null    // First record number on current page
  to: number | null      // Last record number on current page
}

// Generic paginated response
export interface PaginatedResponse<T> {
  data: T[]
  meta: PaginationMeta
}

// Response types
export interface LoginResponse {
  message: string
  user: User
  token: string
}

export interface RegisterResponse {
  message: string
  user: User
}

export interface MemberListResponse {
  data: MemberProfile[]
  meta: PaginationMeta
}

export interface MemberResponse {
  data: MemberProfile
  message?: string
}
