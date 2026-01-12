import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import MemberListView from '../views/MemberListView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { layout: 'blank', requiresAuth: false }
    },
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: '/members',
      name: 'members',
      component: MemberListView,
      meta: { requiresAuth: true }
    }
  ]
})

/**
 * Validate redirect URL to prevent open redirect attacks
 * Only allow internal app routes (no external URLs)
 */
function isValidRedirect(path: string): boolean {
  // Must start with / (internal path)
  if (!path.startsWith('/')) return false

  // Must not start with // (protocol-relative URL)
  if (path.startsWith('//')) return false

  // Must not contain protocol (http:, https:, javascript:, etc.)
  if (path.includes(':')) return false

  return true
}

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  const requiresAuth = to.meta.requiresAuth !== false // Default to true

  if (requiresAuth && !token) {
    // Protected route without token - redirect to login
    // Validate redirect URL to prevent open redirect attacks
    const redirectPath = isValidRedirect(to.fullPath) ? to.fullPath : '/'
    next({ name: 'login', query: { redirect: redirectPath } })
  } else if (to.name === 'login' && token) {
    // Already logged in, trying to access login - redirect to home
    next({ name: 'home' })
  } else {
    // Allow navigation
    next()
  }
})

export default router