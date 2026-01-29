<script setup lang="ts">
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import StockBadge from '@/components/Inventory/StockBadge.vue';
import { useInventory } from '@/composables/useInventory';
import type { Item } from '@/types/inventory';

const props = defineProps<{
  itemId: number;
}>();

const emit = defineEmits<{
  navigate: [page: string];
}>();

const { items, getItemHistory } = useInventory();

const currentItem = computed(() => items.value.find((i) => i.id === props.itemId));
const transactions = computed(() => getItemHistory(props.itemId));

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('en-US', {
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
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6 flex justify-between items-center">
        <div>
          <button @click="emit('navigate', 'items')" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
            ← Back to Items
          </button>
          <h2 v-if="currentItem" class="text-3xl font-bold text-gray-900">Transaction History</h2>
          <p v-if="currentItem" class="mt-1 text-sm text-gray-600">{{ currentItem.name }}</p>
        </div>
      </div>

      <!-- Item Summary Card -->
      <Card v-if="currentItem" class="mb-6">
        <CardHeader>
          <div class="flex justify-between items-start">
            <div>
              <CardTitle>{{ currentItem.name }}</CardTitle>
              <CardDescription v-if="currentItem.description">{{ currentItem.description }}</CardDescription>
            </div>
            <StockBadge :quantity="currentItem.quantity" :reorder-level="currentItem.reorder_level" />
          </div>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <p class="text-sm text-gray-600">Current Stock</p>
              <p class="text-2xl font-bold">{{ currentItem.quantity }} {{ currentItem.unit_type }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Reorder Level</p>
              <p class="text-2xl font-bold">
                {{ currentItem.reorder_level }}
                <span class="text-base">{{ currentItem.unit_type }}</span>
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Total Transactions</p>
              <p class="text-2xl font-bold">{{ transactions.length }}</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Transaction Timeline -->
      <Card>
        <CardHeader>
          <CardTitle>Transaction Timeline</CardTitle>
          <CardDescription>Complete history of all changes</CardDescription>
        </CardHeader>
        <CardContent>
          <div v-if="transactions.length > 0" class="space-y-6">
            <div v-for="transaction in transactions" :key="transaction.id" class="border-l-2 border-gray-200 pl-6 pb-6 relative">
              <!-- Timeline Dot -->
              <div class="absolute left-0 top-0 -translate-x-1/2">
                <div
                  :class="[
                    'w-4 h-4 rounded-full border-2 border-white',
                    transaction.transaction_type === 'add' ? 'bg-green-500' : 'bg-red-500',
                  ]"
                />
              </div>

              <!-- Transaction Content -->
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <Badge :variant="transaction.transaction_type === 'add' ? 'default' : 'destructive'">
                      {{ transaction.transaction_type === 'add' ? 'Addition' : 'Deduction' }}
                    </Badge>
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ formatDate(transaction.transaction_date) }}
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-3">
                  <div>
                    <p class="text-xs text-gray-600">Quantity Changed</p>
                    <p class="text-lg font-semibold">
                      {{ transaction.transaction_type === 'add' ? '+' : '-' }}{{ transaction.quantity }}
                      {{ currentItem?.unit_type }}
                    </p>
                  </div>
                  <div v-if="transaction.reference">
                    <p class="text-xs text-gray-600">Reference</p>
                    <p class="text-sm font-semibold">{{ transaction.reference }}</p>
                  </div>
                </div>

                <div v-if="transaction.notes" class="mt-3 pt-3 border-t">
                  <p class="text-sm text-gray-600">
                    <strong>Notes:</strong> {{ transaction.notes }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions yet</h3>
            <p class="mt-1 text-sm text-gray-500">This item hasn't had any additions or deductions.</p>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
