<script setup lang="ts">
import { PaginationListItem, type PaginationListItemProps } from 'reka-ui'
import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'

const props = defineProps<PaginationListItemProps & {
  class?: string
  isActive?: boolean
}>()
</script>

<template>
  <PaginationListItem
    v-bind="props"
    as-child
  >
    <Button
      :variant="isActive ? 'default' : 'outline'"
      size="sm"
      :class="cn('h-9 w-9 p-0', props.class)"
    >
      <slot />
    </Button>
  </PaginationListItem>
</template>
