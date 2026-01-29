<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import StockBadge from '@/components/Inventory/StockBadge.vue';
import type { Item, Transaction } from '@/types/inventory';

interface Props {
  stats: {
    total_items: number;
    low_stock_items: number;
    out_of_stock_items: number;
    total_transactions: number;
  };
  recentTransactions: Transaction[];
  lowStockItems: Item[];
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
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
          <Button>
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
              <p class="text-sm text-primary hover:underline cursor-pointer">
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
                  class="flex items-center justify-between py-3 border-b last:border-0"
                >
                  <div class="flex-1">
                    <p class="font-medium text-sm">{{ transaction.item?.name }}</p>
                    <p class="text-xs text-gray-500">{{ transaction.user?.name }} • {{ formatDate(transaction.created_at) }}</p>
                  </div>
                  <Badge :variant="transaction.transaction_type === 'addition' ? 'default' : 'destructive'">
                    {{ transaction.transaction_type === 'addition' ? '+' : '-' }}{{ transaction.quantity }}
                    {{ transaction.item?.unit_type }}
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
                  class="flex items-center justify-between py-3 border-b last:border-0"
                >
                  <div class="flex-1">
                    <p class="font-medium text-sm">{{ item.name }}</p>
                    <p class="text-xs text-gray-500">
                      Current: {{ item.current_quantity }} {{ item.unit_type }} 
                      • Min: {{ item.minimum_quantity }} {{ item.unit_type }}
                    </p>
                  </div>
                  <StockBadge :status="item.stock_status" />
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
