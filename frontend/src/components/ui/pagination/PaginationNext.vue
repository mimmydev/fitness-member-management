<script setup lang="ts">
import { PaginationNext as PaginationNextPrimitive, type PaginationNextProps } from 'reka-ui'
import { Button } from '@/components/ui/button'
import { ChevronRight } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const props = defineProps<PaginationNextProps & { class?: string }>()
</script>

<template>
  <PaginationNextPrimitive
    v-bind="props"
    as-child
  >
    <Button
      variant="outline"
      size="sm"
      :class="cn('h-9 gap-1 pl-2.5', props.class)"
    >
      <span>Next</span>
      <ChevronRight class="h-4 w-4" />
    </Button>
  </PaginationNextPrimitive>
</template>
