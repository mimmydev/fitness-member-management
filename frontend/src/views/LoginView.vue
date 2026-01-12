<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from '@/components/ui/card'
import { Dumbbell, Loader2, AlertCircle } from 'lucide-vue-next'
import { ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const route = useRoute()
const { login, isLoading, error, clearError } = useAuth()

// Form state
const email = ref('')
const password = ref('')

// Clear error when inputs change
watch([email, password], () => {
  clearError()
})

const handleLogin = async () => {
  const success = await login({
    email: email.value,
    password: password.value,
  })

  if (success) {
    // Redirect to intended page or home
    const redirect = route.query.redirect as string
    router.push(redirect || '/')
  }
}

// Handle Enter key submission
const handleKeyPress = (e: KeyboardEvent) => {
  if (e.key === 'Enter' && !isLoading.value) {
    handleLogin()
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-50">
    <Card class="w-full max-w-md">
      <CardHeader class="space-y-1 text-center">
        <div class="flex justify-center mb-4">
          <div class="bg-indigo-50 p-3 rounded-full">
            <Dumbbell class="h-8 w-8 text-indigo-600" />
          </div>
        </div>
        <CardTitle class="text-2xl font-bold">Welcome back</CardTitle>
        <CardDescription>
          Enter your credentials to access the admin panel
        </CardDescription>
      </CardHeader>

      <CardContent class="space-y-4">
        <!-- Error Alert -->
        <div
          v-if="error"
          class="flex items-center gap-2 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-md"
        >
          <AlertCircle class="h-4 w-4" />
          <span>{{ error }}</span>
        </div>

        <div class="space-y-2">
          <Label for="email">Email</Label>
          <Input
            id="email"
            type="email"
            placeholder="admin@fitness.com"
            v-model="email"
            :disabled="isLoading"
            @keypress="handleKeyPress"
            autocomplete="email"
          />
        </div>

        <div class="space-y-2">
          <Label for="password">Password</Label>
          <Input
            id="password"
            type="password"
            v-model="password"
            :disabled="isLoading"
            @keypress="handleKeyPress"
            autocomplete="current-password"
          />
        </div>
      </CardContent>

      <CardFooter>
        <Button
          class="w-full"
          @click="handleLogin"
          :disabled="isLoading || !email || !password"
        >
          <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
          {{ isLoading ? 'Signing in...' : 'Sign In' }}
        </Button>
      </CardFooter>
    </Card>
  </div>
</template>

