import { ref, computed } from 'vue'
import apiClient from '@/lib/api'
import type {
  User,
  LoginRequest,
  RegisterRequest,
  LoginResponse,
  RegisterResponse
} from '@/types/api'
import type { ApiError } from '@/lib/api'

// Global reactive state (shared across all component instances)
const user = ref<User | null>(null)
const token = ref<string | null>(null)
const isLoading = ref(false)
const error = ref<string | null>(null)

// Initialize from localStorage on app start
const initializeAuth = () => {
  const storedToken = localStorage.getItem('auth_token')
  const storedUser = localStorage.getItem('auth_user')

  if (storedToken && storedUser) {
    token.value = storedToken
    try {
      user.value = JSON.parse(storedUser)
    } catch {
      // Invalid stored user, clear everything
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
    }
  }
}

// Call on module load
initializeAuth()

export function useAuth() {
  const isAuthenticated = computed(() => !!token.value && !!user.value)

  /**
   * Login with email and password
   */
  const login = async (credentials: LoginRequest): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.post<LoginResponse>('/auth/login', credentials)

      // Store token and user
      token.value = response.data.token
      user.value = response.data.user

      // Persist to localStorage
      localStorage.setItem('auth_token', response.data.token)
      localStorage.setItem('auth_user', JSON.stringify(response.data.user))

      return true
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Login failed'
      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Register new user
   */
  const register = async (data: RegisterRequest): Promise<boolean> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.post<RegisterResponse>('/auth/register', data)

      // Note: Backend returns user but not token on registration
      // User needs to login after registration
      return true
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Registration failed'
      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Logout current user
   */
  const logout = async (): Promise<void> => {
    isLoading.value = true
    error.value = null

    try {
      // Call backend to revoke token
      await apiClient.post('/auth/logout')
    } catch (err) {
      // Even if logout fails on backend, clear local state
      console.error('Logout error:', err)
    } finally {
      // Clear local state
      user.value = null
      token.value = null
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      isLoading.value = false
    }
  }

  /**
   * Fetch current user from backend
   */
  const fetchUser = async (): Promise<boolean> => {
    if (!token.value) return false

    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.get<{ user: User }>('/auth/me')
      user.value = response.data.user
      localStorage.setItem('auth_user', JSON.stringify(response.data.user))
      return true
    } catch (err) {
      const apiError = err as ApiError
      error.value = apiError.message || 'Failed to fetch user'

      // If 401, clear auth state (handled by interceptor, but just in case)
      if (apiError.status === 401) {
        user.value = null
        token.value = null
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
      }

      return false
    } finally {
      isLoading.value = false
    }
  }

  /**
   * Clear error message
   */
  const clearError = () => {
    error.value = null
  }

  return {
    // State
    user: computed(() => user.value),
    token: computed(() => token.value),
    isAuthenticated,
    isLoading: computed(() => isLoading.value),
    error: computed(() => error.value),

    // Actions
    login,
    register,
    logout,
    fetchUser,
    clearError,
  }
}
