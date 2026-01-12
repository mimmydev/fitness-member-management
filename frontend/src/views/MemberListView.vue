<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
  MoreHorizontal,
  Plus,
  Search,
  FileEdit,
  Trash2,
  Loader2,
  AlertCircle,
} from 'lucide-vue-next';
import { useMembers } from '@/composables/useMembers';
import MemberForm from '@/components/members/MemberForm.vue';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from '@/components/ui/pagination';
import type { MemberFormData } from '@/lib/validations';

const {
  members,
  currentMember,
  paginationMeta,
  isLoading,
  error,
  fetchMembers,
  fetchMember,
  createMember,
  updateMember,
  deleteMember,
  clearError,
} = useMembers();

// Dialog state
const showMemberDialog = ref(false);
const showDeleteDialog = ref(false);
const selectedMemberId = ref<number | null>(null);
const dialogMode = ref<'create' | 'edit'>('create');
const isSubmitting = ref(false);

// Search
const searchQuery = ref('');

// Pagination state
const currentPage = ref(1);
const perPage = ref(15);

// Fetch members on mount with pagination
onMounted(async () => {
  await fetchMembers(
    undefined,
    currentPage.value,
    perPage.value,
    searchQuery.value
  );
});

// Handle page change
const handlePageChange = async (page: number) => {
  currentPage.value = page;
  await fetchMembers(undefined, page, perPage.value, searchQuery.value);
};

// Handle page size change
const handlePageSizeChange = async () => {
  currentPage.value = 1; // Reset to page 1
  await fetchMembers(undefined, 1, perPage.value, searchQuery.value);
};

// Handle search input with debounce (500ms delay)
let searchTimeout: number | null = null;
const handleSearchChange = async () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(async () => {
    currentPage.value = 1; // Reset to page 1 on search
    await fetchMembers(undefined, 1, perPage.value, searchQuery.value);
  }, 500);
};

// Status Badge Helper
const getStatusVariant = (status: string) => {
  switch (status.toLowerCase()) {
    case 'active':
      return 'default';
    case 'expired':
      return 'destructive';
    default:
      return 'secondary';
  }
};

// Dialog handlers
const openCreateDialog = () => {
  dialogMode.value = 'create';
  showMemberDialog.value = true;
};

const openEditDialog = async (id: number) => {
  dialogMode.value = 'edit';
  await fetchMember(id);
  showMemberDialog.value = true;
};

const closeDialog = () => {
  showMemberDialog.value = false;
};

const handleFormSubmit = async (data: MemberFormData) => {
  isSubmitting.value = true;
  clearError();

  let success = false;

  if (dialogMode.value === 'create') {
    const result = await createMember(data);
    success = !!result;
  } else if (dialogMode.value === 'edit' && currentMember.value) {
    const result = await updateMember(currentMember.value.id, data);
    success = !!result;
  }

  isSubmitting.value = false;

  if (success) {
    showMemberDialog.value = false;
  }
};

// Delete handlers
const confirmDelete = (id: number) => {
  selectedMemberId.value = id;
  showDeleteDialog.value = true;
};

const executeDelete = async () => {
  if (selectedMemberId.value === null) return;

  await deleteMember(selectedMemberId.value);
  showDeleteDialog.value = false;
  selectedMemberId.value = null;
};
</script>

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold tracking-tight">Members</h2>
        <p class="text-muted-foreground">
          Manage your fitness centre members and memberships.
        </p>
      </div>
      <Button @click="openCreateDialog">
        <Plus class="mr-2 h-4 w-4" /> Add Member
      </Button>
    </div>

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

    <!-- Toolbar -->
    <div class="flex items-center justify-between gap-4 pt-2 pb-4">
      <div class="relative w-full max-w-sm">
        <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
        <Input
          placeholder="Search members..."
          class="pl-8"
          v-model="searchQuery"
          @input="handleSearchChange"
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center py-12">
      <Loader2 class="h-8 w-8 animate-spin text-indigo-600" />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="!isLoading && members.length === 0"
      class="flex flex-col items-center justify-center py-12 text-center"
    >
      <p class="text-muted-foreground mb-4">
        {{
          searchQuery
            ? `No members found matching "${searchQuery}"`
            : 'No members found'
        }}
      </p>
      <Button @click="openCreateDialog">
        <Plus class="mr-2 h-4 w-4" /> Add Your First Member
      </Button>
    </div>

    <!-- Data Table -->
    <div v-else class="rounded-md border border-slate-300 bg-white">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>Name</TableHead>
            <TableHead>Membership</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>End Date</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="member in members" :key="member.id">
            <TableCell class="font-medium">
              <div>{{ member.full_name }}</div>
              <div class="text-xs text-muted-foreground">
                {{ member.user?.email || 'No email' }}
              </div>
            </TableCell>
            <TableCell class="capitalize">{{
              member.membership_type
            }}</TableCell>
            <TableCell>
              <Badge
                :variant="getStatusVariant(member.membership_status)"
                class="capitalize"
              >
                {{ member.membership_status }}
              </Badge>
            </TableCell>
            <TableCell>{{ member.membership_end_date || 'N/A' }}</TableCell>
            <TableCell class="text-right">
              <DropdownMenu>
                <DropdownMenuTrigger as-child>
                  <Button variant="ghost" size="icon">
                    <MoreHorizontal class="h-4 w-4" />
                    <span class="sr-only">Open menu</span>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                  <DropdownMenuLabel>Actions</DropdownMenuLabel>
                  <DropdownMenuItem @click="openEditDialog(member.id)">
                    <FileEdit class="mr-2 h-4 w-4" /> Edit
                  </DropdownMenuItem>
                  <DropdownMenuItem
                    class="text-red-600 focus:text-red-600"
                    @click="confirmDelete(member.id)"
                  >
                    <Trash2 class="mr-2 h-4 w-4" /> Delete
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>

      <!-- Pagination Controls -->
      <div
        v-if="paginationMeta && paginationMeta.last_page > 1"
        class="flex items-center justify-between px-4 py-4 border-t"
      >
        <!-- Page Info -->
        <div class="text-sm text-muted-foreground">
          Showing {{ paginationMeta.from }} to {{ paginationMeta.to }} of
          {{ paginationMeta.total }} members
        </div>

        <!-- Page Size Selector -->
        <div class="flex items-center gap-2">
          <span class="text-sm">Show:</span>
          <select
            v-model.number="perPage"
            @change="handlePageSizeChange"
            class="border rounded px-2 py-1 text-sm"
          >
            <option :value="10">10</option>
            <option :value="15">15</option>
            <option :value="25">25</option>
            <option :value="50">50</option>
          </select>
        </div>

        <!-- Pagination Component -->
        <Pagination
          v-slot="{ page }"
          :total="paginationMeta.total"
          :sibling-count="1"
          :items-per-page="perPage"
          :default-page="currentPage"
          @update:page="handlePageChange"
        >
          <PaginationContent v-slot="{ items }">
            <PaginationPrevious />

            <template v-for="(item, index) in items" :key="index">
              <PaginationItem
                v-if="item.type === 'page'"
                :value="item.value"
                :is-active="item.value === page"
              >
                {{ item.value }}
              </PaginationItem>
              <PaginationEllipsis v-else :index="index" />
            </template>

            <PaginationNext />
          </PaginationContent>
        </Pagination>
      </div>
    </div>

    <!-- Member Form Dialog -->
    <Dialog :open="showMemberDialog" @update:open="showMemberDialog = $event">
      <!-- Added sm:max-w-2xl to make it wider and more comfortable -->
      <DialogContent
        class="sm:max-w-2xl max-h-[90vh] overflow-y-auto p-0 gap-0"
      >
        <!-- Clean Header with light background -->
        <DialogHeader
          class="p-6 pb-2 border-b border-slate-100 bg-white sticky top-0 z-10"
        >
          <DialogTitle class="text-xl font-bold text-slate-900">
            {{ dialogMode === 'create' ? 'Add Member' : 'Edit Member' }}
          </DialogTitle>
          <DialogDescription>
            {{
              dialogMode === 'create'
                ? 'Create a new profile.'
                : 'Update member details.'
            }}
          </DialogDescription>
        </DialogHeader>

        <div class="p-6 pt-4">
          <MemberForm
            :mode="dialogMode"
            :initial-data="
              dialogMode === 'edit' ? currentMember || undefined : undefined
            "
            :is-submitting="isSubmitting"
            @submit="handleFormSubmit"
            @cancel="closeDialog"
          />
        </div>
      </DialogContent>
    </Dialog>

    <!-- Delete Alert Dialog -->
    <AlertDialog
      :open="showDeleteDialog"
      @update:open="showDeleteDialog = $event"
    >
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
          <AlertDialogDescription>
            This action cannot be undone. This will permanently soft-delete the
            member account and remove their data from active views.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction
            class="bg-red-600 hover:bg-red-700"
            @click="executeDelete"
          >
            Delete Member
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </div>
</template>
