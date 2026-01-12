<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';
import { computed } from 'vue';

const { user, logout } = useAuth();
const router = useRouter();

// Get initials from user name
const initials = computed(() => {
  if (!user.value?.name) return 'U';
  return user.value.name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
});

const handleLogout = async () => {
  await logout();
  router.push('/login');
};
</script>

<template>
  <header
    class="sticky top-0 z-10 flex h-16 items-center gap-4 border-b border-slate-200 bg-white px-6 shadow-sm"
  >
    <div class="w-full flex-1">
      <h1 class="text-lg font-semibold text-slate-800">Dashboard Overview</h1>
    </div>

    <div class="flex items-center gap-4">
      <div class="flex flex-col items-end sm:flex">
        <span class="text-sm font-medium text-slate-700">
          {{ user?.name || 'User' }}
        </span>
        <span class="text-xs text-slate-500">
          {{ user?.email || '' }}
        </span>
      </div>
      <Avatar class="h-9 w-9 border border-slate-200 cursor-pointer">
        <AvatarFallback>{{ initials }}</AvatarFallback>
      </Avatar>
      <Button
        variant="ghost"
        size="sm"
        class="text-red-500 hover:text-red-600 hover:bg-red-50"
        @click="handleLogout"
      >
        Logout
      </Button>
    </div>
  </header>
</template>
