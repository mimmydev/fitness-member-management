<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Users, UserCheck, UserX, Loader2, AlertCircle } from 'lucide-vue-next';
import { useMembers } from '@/composables/useMembers';

// Initialize composable
const { members, isLoading, error, fetchMembers, clearError } = useMembers();

// Fetch data on component mount (fetch all members for statistics, paginated)
onMounted(async () => {
  await fetchMembers(undefined, 1, 100); // Fetch more for better statistics
});

// Computed statistics
const totalMembers = computed(() => members.value.length);

const activeMembers = computed(() =>
  members.value.filter(
    member => member.membership_status === 'active' && !member.is_expired
  ).length
);

const expiredMembers = computed(() =>
  members.value.filter(
    member => member.is_expired || member.membership_status === 'expired'
  ).length
);

const hasMembers = computed(() => members.value.length > 0);
</script>

<template>
  <div class="space-y-6">
    <!-- Error Alert -->
    <div
      v-if="error"
      class="flex items-center gap-2 p-3 text-sm text-red-800 bg-red-50 border border-red-200 rounded-md"
    >
      <AlertCircle class="h-4 w-4" />
      <span>{{ error }}</span>
      <Button variant="ghost" size="sm" class="ml-auto" @click="clearError">
        Dismiss
      </Button>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center py-12">
      <Loader2 class="h-8 w-8 animate-spin text-indigo-600" />
    </div>

    <div v-if="!isLoading" class="grid gap-4 md:grid-cols-3">
      <Card>
        <CardHeader
          class="flex flex-row items-center justify-between space-y-0 pb-2"
        >
          <CardTitle class="text-sm font-medium">Total Members</CardTitle>
          <Users class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ totalMembers }}</div>
          <p class="text-xs text-muted-foreground">Registered in system</p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader
          class="flex flex-row items-center justify-between space-y-0 pb-2"
        >
          <CardTitle class="text-sm font-medium">Active Members</CardTitle>
          <UserCheck class="h-4 w-4 text-green-500" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ activeMembers }}</div>
          <p class="text-xs text-muted-foreground">
            Currently valid memberships
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader
          class="flex flex-row items-center justify-between space-y-0 pb-2"
        >
          <CardTitle class="text-sm font-medium">Expired</CardTitle>
          <UserX class="h-4 w-4 text-red-500" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ expiredMembers }}</div>
          <p class="text-xs text-muted-foreground">Needs renewal</p>
        </CardContent>
      </Card>
    </div>

    <!-- Empty State / Placeholder -->
    <div
      v-if="!isLoading && !hasMembers"
      class="flex h-112.5 shrink-0 items-center justify-center rounded-md border border-dashed"
    >
      <div
        class="mx-auto flex max-w-105 flex-col items-center justify-center text-center"
      >
        <Users class="h-10 w-10 text-muted-foreground" />
        <h3 class="mt-4 text-lg font-semibold">No members added</h3>
        <p class="mb-4 mt-2 text-sm text-muted-foreground">
          You haven't added any members to the system yet.
        </p>
      </div>
    </div>
  </div>
</template>
