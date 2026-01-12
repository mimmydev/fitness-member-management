<script setup lang="ts">
import { PaginationPrev, type PaginationPrevProps } from 'reka-ui'
import { Button } from '@/components/ui/button'
import { ChevronLeft } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const props = defineProps<PaginationPrevProps & { class?: string }>()
</script>

<template>
  <PaginationPrev
    v-bind="props"
    as-child
  >
    <Button
      variant="outline"
      size="sm"
      :class="cn('h-9 gap-1 pr-2.5', props.class)"
    >
      <ChevronLeft class="h-4 w-4" />
      <span>Previous</span>
    </Button>
  </PaginationPrev>
</template>
