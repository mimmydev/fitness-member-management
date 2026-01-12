<script setup lang="ts">
import { RouterView, useRoute } from 'vue-router';
import AppHeader from '@/components/layouts/AppHeader.vue';
import Sidebar from '@/components/layouts/Sidebar.vue';
import AppFooter from '@/components/layouts/AppFooter.vue';

const route = useRoute();
</script>

<template>
  <!-- IF layout is blank (Login), just show content -->
  <div v-if="route.meta.layout === 'blank'" class="min-h-screen bg-slate-50">
    <RouterView />
  </div>

  <!-- ELSE show the Dashboard Layout -->
  <div
    v-else
    class="flex min-h-screen bg-background font-sans antialiased text-foreground"
  >
    <Sidebar />
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
      <AppHeader />
      <main class="flex-1 overflow-y-auto p-8 pt-6">
        <RouterView />
      </main>

      <!-- Optional Simple Footer -->
      <footer class="border-t border-slate-200 py-4 px-8 bg-background">
        <p class="text-xs text-muted-foreground text-center">
          &copy; 2026 Fitness Centre Management
        </p>
      </footer>
    </div>
  </div>
</template>
