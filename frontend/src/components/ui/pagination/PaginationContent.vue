<script setup lang="ts">
import { PaginationList, type PaginationListProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<PaginationListProps & { class?: string }>()
</script>

<template>
  <PaginationList
    v-bind="props"
    v-slot="slotProps"
    :class="cn('flex flex-row items-center gap-1', props.class)"
  >
    <slot v-bind="slotProps" />
  </PaginationList>
</template>
