<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import StockBadge from './StockBadge.vue';
import type { Item } from '@/types/inventory';

interface Props {
  items: Item[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  viewHistory: [itemId: number];
}>();

const deleteItem = (item: Item) => {
  if (confirm(`Are you sure you want to delete "${item.name}"?`)) {
    // TODO: Implement delete functionality with composable
    console.log('Delete item:', item.id);
  }
};
</script>

<template>
  <div class="bg-white rounded-lg shadow overflow-hidden">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Name</TableHead>
          <TableHead>Description</TableHead>
          <TableHead>Current Stock</TableHead>
          <TableHead>Unit Type</TableHead>
          <TableHead>Status</TableHead>
          <TableHead class="text-right">Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="item in props.items" :key="item.id">
          <TableCell class="font-medium">{{ item.name }}</TableCell>
          <TableCell class="text-gray-600">
            {{ item.description || '-' }}
          </TableCell>
          <TableCell>
            <span class="font-semibold">{{ item.quantity }}</span>
            <span v-if="item.reorder_level" class="text-sm text-gray-500 ml-1">
              / {{ item.reorder_level }} reorder
            </span>
          </TableCell>
          <TableCell>
            <span class="text-sm bg-gray-100 px-2 py-1 rounded">{{ item.unit_type }}</span>
          </TableCell>
          <TableCell>
            <StockBadge :quantity="item.quantity" :reorder-level="item.reorder_level" />
          </TableCell>
          <TableCell class="text-right">
            <div class="flex justify-end gap-2">
              <Button variant="ghost" size="sm" @click="emit('viewHistory', item.id)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </Button>
              <Button variant="ghost" size="sm" @click="deleteItem(item)">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </Button>
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
