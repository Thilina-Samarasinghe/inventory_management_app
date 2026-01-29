<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import StockBadge from '@/components/Inventory/StockBadge.vue';
import { useInventory } from '@/composables/useInventory';

const emit = defineEmits<{
  navigate: [page: string, itemId?: number];
}>();

const { stats, recentTransactions, lowStockItems, items } = useInventory();

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getItemName = (itemId: number) => {
  return items.value.find((i) => i.id === itemId)?.name || 'Unknown Item';
};
</script>

<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h2 class="text-3xl font-bold text-gray-900">Inventory Dashboard</h2>
          <p class="mt-1 text-sm text-gray-600">Welcome back! Here's your inventory overview.</p>
        </div>
        <div class="flex gap-3">
          <Button @click="emit('navigate', 'items-create')">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Items
          </Button>
        </div>
      </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <Card>
            <CardHeader class="pb-3">
              <CardDescription>Total Items</CardDescription>
              <CardTitle class="text-3xl">{{ stats.total_items }}</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm text-primary hover:underline cursor-pointer" @click="emit('navigate', 'items')">
                View all items →
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader class="pb-3">
              <CardDescription>Low Stock Items</CardDescription>
              <CardTitle class="text-3xl text-orange-600">{{ stats.low_stock_items }}</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm text-gray-600">Needs restocking</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader class="pb-3">
              <CardDescription>Out of Stock</CardDescription>
              <CardTitle class="text-3xl text-red-600">{{ stats.out_of_stock_items }}</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm text-gray-600">Requires immediate attention</p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader class="pb-3">
              <CardDescription>Total Transactions</CardDescription>
              <CardTitle class="text-3xl">{{ stats.total_transactions }}</CardTitle>
            </CardHeader>
            <CardContent>
              <p class="text-sm text-gray-600">All time</p>
            </CardContent>
          </Card>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Transactions -->
          <Card>
            <CardHeader>
              <CardTitle>Recent Transactions</CardTitle>
              <CardDescription>Latest inventory movements</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div
                  v-for="transaction in recentTransactions"
                  :key="transaction.id"
                  class="flex items-center justify-between py-3 border-b last:border-0 cursor-pointer hover:bg-gray-50 px-2 -mx-2"
                  @click="emit('navigate', 'items-history', transaction.item_id)"
                >
                  <div class="flex-1">
                    <p class="font-medium text-sm">{{ getItemName(transaction.item_id) }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(transaction.transaction_date) }}</p>
                  </div>
                  <Badge :variant="transaction.transaction_type === 'add' ? 'default' : 'destructive'">
                    {{ transaction.transaction_type === 'add' ? '+' : '-' }}{{ transaction.quantity }}
                  </Badge>
                </div>
                <div v-if="recentTransactions.length === 0" class="text-center py-8 text-gray-500">
                  No transactions yet
                </div>
              </div>
            </CardContent>
          </Card>

          <!-- Low Stock Items -->
          <Card>
            <CardHeader>
              <CardTitle>Low Stock Alert</CardTitle>
              <CardDescription>Items that need restocking</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div
                  v-for="item in lowStockItems"
                  :key="item.id"
                  class="flex items-center justify-between py-3 border-b last:border-0 cursor-pointer hover:bg-gray-50 px-2 -mx-2"
                  @click="emit('navigate', 'items-history', item.id)"
                >
                  <div class="flex-1">
                    <p class="font-medium text-sm">{{ item.name }}</p>
                    <p class="text-xs text-gray-500">
                      Current: {{ item.quantity }} {{ item.unit_type }}
                      • Min: {{ item.reorder_level }} {{ item.unit_type }}
                    </p>
                  </div>
                  <StockBadge :quantity="item.quantity" :reorder-level="item.reorder_level" />
                </div>
                <div v-if="lowStockItems.length === 0" class="text-center py-8 text-green-600">
                  ✓ All items are well stocked!
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
    </div>
  </div>
</template>
