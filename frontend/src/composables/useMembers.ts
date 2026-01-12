import { ref, computed } from 'vue'
import apiClient from '@/lib/api'
import type {
  MemberProfile,
  MemberListResponse,
  MemberResponse,
  CreateMemberRequest,
  UpdateMemberRequest,
  PaginationMeta,
} from '@/types/api'
import type { ApiError } from '@/lib/api'

// Module-level state for member list (shared)
const members = ref<MemberProfile[]>([])
const currentMember = ref<MemberProfile | null>(null)
const paginationMeta = ref<PaginationMeta | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)

export function useMembers() {
  /**
   * Fetch all members from API (paginated)
   * Now supports server-side search via optional search parameter
   */
  const fetchMembers = async (
    status?: 'active',
    page: number = 1,
    perPage: number = 15,
    search?: string
  ): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      const params: Record<string, any> = {
        page,
        per_page: perPage,
      }

      if (status) {
        params.status = status
      }

      if (search && search.trim().length >= 2) {
        params.search = search.trim()
      }

      const response = await apiClient.get<MemberListResponse>('/members', { params })
      members.value = response.data.data
      paginationMeta.value = response.data.meta
      return true
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Failed to fetch members'
      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Fetch single member by ID
   */
  const fetchMember = async (id: number): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.get<MemberResponse>(`/members/${id}`)
      currentMember.value = response.data.data
      return true
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Failed to fetch member'
      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Create new member
   */
  const createMember = async (data: CreateMemberRequest): Promise<MemberProfile | null> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.post<MemberResponse>('/members', data)
      const newMember = response.data.data

      // Add to local list
      members.value.unshift(newMember)

      return newMember
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Failed to create member'
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Update existing member
   */
  const updateMember = async (
    id: number,
    data: UpdateMemberRequest
  ): Promise<MemberProfile | null> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.put<MemberResponse>(`/members/${id}`, data)
      const updatedMember = response.data.data

      // Update in local list
      const index = members.value.findIndex(m => m.id === id)
      if (index !== -1) {
        members.value[index] = updatedMember
      }

      // Update current member if it's the one being edited
      if (currentMember.value?.id === id) {
        currentMember.value = updatedMember
      }

      return updatedMember
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Failed to update member'
      return null
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Delete member (soft delete on backend)
   */
  const deleteMember = async (id: number): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      await apiClient.delete(`/members/${id}`)

      // Remove from local list
      members.value = members.value.filter(m => m.id !== id)

      // Clear current member if it was deleted
      if (currentMember.value?.id === id) {
        currentMember.value = null
      }

      return true
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Failed to delete member'
      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Clear current member
   */
  const clearCurrentMember = () => {
    currentMember.value = null
  }

  /**
   * Clear error message
   */
  const clearError = () => {
    error.value = null
  }

  return {
    // State
    members: computed(() => members.value),
    currentMember: computed(() => currentMember.value),
    paginationMeta: computed(() => paginationMeta.value),
    isLoading: computed(() => isLoading.value),
    error: computed(() => error.value),

    // Actions
    fetchMembers,
    fetchMember,
    createMember,
    updateMember,
    deleteMember,
    clearCurrentMember,
    clearError,
  }
}
