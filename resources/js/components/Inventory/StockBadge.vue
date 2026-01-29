<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { computed } from 'vue';

interface Props {
  status: 'in_stock' | 'low_stock' | 'out_of_stock';
}

const props = defineProps<Props>();

const config = computed(() => {
  switch (props.status) {
    case 'in_stock':
      return {
        label: 'In Stock',
        variant: 'default' as const,
        class: 'bg-green-100 text-green-800 hover:bg-green-100',
      };
    case 'low_stock':
      return {
        label: 'Low Stock',
        variant: 'secondary' as const,
        class: 'bg-orange-100 text-orange-800 hover:bg-orange-100',
      };
    case 'out_of_stock':
      return {
        label: 'Out of Stock',
        variant: 'destructive' as const,
        class: 'bg-red-100 text-red-800 hover:bg-red-100',
      };
    default:
      return {
        label: 'Unknown',
        variant: 'outline' as const,
        class: '',
      };
  }
});
</script>

<template>
  <Badge :variant="config.variant" :class="config.class">
    {{ config.label }}
  </Badge>
</template>
