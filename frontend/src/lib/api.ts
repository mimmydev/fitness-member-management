/**
 * API Client Configuration
 *
 * Configures Axios for API communication with Laravel backend.
 * Includes request/response interceptors for token management and error handling.
 */

import axios, { AxiosError, AxiosInstance, InternalAxiosRequestConfig } from 'axios'

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
  status?: number
}

export interface ApiResponse<T = any> {
  data?: T
  message?: string
  errors?: Record<string, string[]> | any
}

// Create Axios instance with default configuration
const apiClient: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  timeout: parseInt(import.meta.env.VITE_API_TIMEOUT) || 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true, // Required for Laravel Sanctum cookie-based auth
})

/**
 * Request Interceptor
 *
 * Adds authentication token to all requests if available.
 */
apiClient.interceptors.request.use(
  (config: InternalAxiosRequestConfig) => {
    // Get token from localStorage
    const token = localStorage.getItem('auth_token')

    if (token && config.headers) {
      config.headers.Authorization = `Bearer ${token}`
    }

    return config
  },
  (error: AxiosError) => {
    return Promise.reject(error)
  }
)

/**
 * Response Interceptor
 *
 * Handles API responses and errors consistently.
 * Transforms errors into a standard format.
 */
apiClient.interceptors.response.use(
  (response) => {
    // Return successful response data
    return response
  },
  (error: AxiosError<ApiResponse>) => {
    // Transform error into standard ApiError format
    const apiError: ApiError = {
      message: error.response?.data?.message || error.message || 'An unexpected error occurred',
      errors: error.response?.data?.errors,
      status: error.response?.status,
    }

    // Handle specific error cases
    if (error.response?.status === 401) {
      // Unauthorized - clear auth data and redirect to login
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')

      // Only redirect if not already on login page
      if (window.location.pathname !== '/login') {
        window.location.href = '/login'
      }
    }

    return Promise.reject(apiError)
  }
)

export default apiClient
export type { ApiError as ApiErrorType, ApiResponse as ApiResponseType }
