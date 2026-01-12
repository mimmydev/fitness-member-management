<script setup lang="ts">
import { PaginationRoot, type PaginationRootProps } from 'reka-ui'
import { cn } from '@/lib/utils'

const props = defineProps<PaginationRootProps & { class?: string }>()
</script>

<template>
  <PaginationRoot
    v-bind="props"
    v-slot="slotProps"
    :class="
      cn(
        'mx-auto flex w-full justify-center',
        props.class,
      )
    "
  >
    <slot v-bind="slotProps" />
  </PaginationRoot>
</template>
