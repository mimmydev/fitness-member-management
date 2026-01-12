<script setup lang="ts">
import { RouterLink, useRoute } from 'vue-router';
import { Button } from '@/components/ui/button';
import { LayoutDashboard, Users, Dumbbell } from 'lucide-vue-next';
import { cn } from '@/lib/utils';

const route = useRoute();

const links = [
  { name: 'Dashboard', href: '/', icon: LayoutDashboard },
  { name: 'Members', href: '/members', icon: Users },
];
</script>

<template>
  <div
    class="hidden border-r border-slate-200 bg-white md:block w-64 min-h-screen sticky top-0"
  >
    <div class="flex h-full max-h-screen flex-col gap-2">
      <!-- Logo Area -->
      <div class="flex h-16 items-center border-b border-slate-200 px-6">
        <RouterLink
          to="/"
          class="flex items-center gap-2 font-semibold text-indigo-600"
        >
          <Dumbbell class="h-6 w-6" />
          <span class="text-lg tracking-tight text-slate-900"
            >FitnessCentre</span
          >
        </RouterLink>
      </div>

      <!-- Navigation -->
      <div class="flex-1 overflow-auto py-4 px-3">
        <nav
          class="grid gap-1 px-2 group-data-[collapsed=true]:justify-center group-data-[collapsed=true]:px-2"
        >
          <Button
            v-for="link in links"
            :key="link.href"
            :variant="route.path === link.href ? 'secondary' : 'ghost'"
            :class="
              cn(
                'w-full justify-start gap-3 mb-1',
                route.path === link.href
                  ? 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800'
                  : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'
              )
            "
            as-child
          >
            <RouterLink :to="link.href">
              <component :is="link.icon" class="h-4 w-4" />
              {{ link.name }}
            </RouterLink>
          </Button>
        </nav>
      </div>

      <!-- Sidebar Footer (Version) -->
      <div class="mt-auto p-4 border-t border-slate-200">
        <p class="text-xs text-slate-400 text-center">v1.0.0-mvp</p>
      </div>
    </div>
  </div>
</template>
