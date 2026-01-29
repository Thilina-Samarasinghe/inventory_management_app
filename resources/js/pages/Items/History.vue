<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import StockBadge from '@/Components/Inventory/StockBadge.vue';
import type { Item, Transaction } from '@/Types/inventory';

interface Props {
  item: Item;
  transactions: Transaction[];
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
  return new Date(date).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const getBatchTransactions = (batchId: string | null) => {
  if (!batchId) return [];
  return props.transactions.filter(t => t.batch_id === batchId);
};

const groupedTransactions = props.transactions.reduce((acc, transaction) => {
  if (transaction.batch_id) {
    if (!acc.batches[transaction.batch_id]) {
      acc.batches[transaction.batch_id] = [];
    }
    acc.batches[transaction.batch_id].push(transaction);
  } else {
    acc.singles.push(transaction);
  }
  return acc;
}, { batches: {} as Record<string, Transaction[]>, singles: [] as Transaction[] });
</script>

<template>
  <AppLayout>
    <Head :title="`History - ${item.name}`" />

    <div class="py-12">
      <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <Link :href="route('items.index')" class="text-sm text-gray-600 hover:text-gray-900 mb-2 inline-block">
              ← Back to Items
            </Link>
            <h2 class="text-3xl font-bold text-gray-900">Transaction History</h2>
            <p class="mt-1 text-sm text-gray-600">{{ item.name }}</p>
          </div>
        </div>

        <!-- Item Summary Card -->
        <Card class="mb-6">
          <CardHeader>
            <div class="flex justify-between items-start">
              <div>
                <CardTitle>{{ item.name }}</CardTitle>
                <CardDescription v-if="item.description">{{ item.description }}</CardDescription>
              </div>
              <StockBadge :status="item.stock_status" />
            </div>
          </CardHeader>
          <CardContent>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <p class="text-sm text-gray-600">Current Stock</p>
                <p class="text-2xl font-bold">{{ item.current_quantity }} {{ item.unit_type }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-600">Minimum Stock</p>
                <p class="text-2xl font-bold">
                  {{ item.minimum_quantity || 'Not set' }}
                  <span v-if="item.minimum_quantity" class="text-base">{{ item.unit_type }}</span>
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
              <!-- Render Singles -->
              <div v-for="transaction in groupedTransactions.singles" :key="transaction.id" class="border-l-2 border-gray-200 pl-6 pb-6 relative">
                <!-- Timeline Dot -->
                <div class="absolute left-0 top-0 -translate-x-1/2">
                  <div 
                    :class="[
                      'w-4 h-4 rounded-full border-2 border-white',
                      transaction.transaction_type === 'addition' ? 'bg-green-500' : 'bg-red-500'
                    ]"
                  />
                </div>

                <!-- Transaction Content -->
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex justify-between items-start mb-2">
                    <div>
                      <Badge :variant="transaction.transaction_type === 'addition' ? 'default' : 'destructive'">
                        {{ transaction.transaction_type === 'addition' ? 'Addition' : 'Deduction' }}
                      </Badge>
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ formatDate(transaction.created_at) }}
                    </div>
                  </div>

                  <div class="grid grid-cols-3 gap-4 mt-3">
                    <div>
                      <p class="text-xs text-gray-600">Quantity Changed</p>
                      <p class="text-lg font-semibold">
                        {{ transaction.transaction_type === 'addition' ? '+' : '-' }}{{ transaction.quantity }} {{ item.unit_type }}
                      </p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600">Previous Stock</p>
                      <p class="text-lg font-semibold">{{ transaction.previous_quantity }} {{ item.unit_type }}</p>
                    </div>
                    <div>
                      <p class="text-xs text-gray-600">New Stock</p>
                      <p class="text-lg font-semibold">{{ transaction.new_quantity }} {{ item.unit_type }}</p>
                    </div>
                  </div>

                  <div v-if="transaction.notes" class="mt-3 pt-3 border-t">
                    <p class="text-sm text-gray-600">
                      <strong>Notes:</strong> {{ transaction.notes }}
                    </p>
                  </div>

                  <div class="mt-2 text-xs text-gray-500">
                    By {{ transaction.user?.name }}
                  </div>
                </div>
              </div>

              <!-- Render Batches -->
              <div 
                v-for="(batchTransactions, batchId) in groupedTransactions.batches" 
                :key="batchId"
                class="border-l-2 border-blue-300 pl-6 pb-6 relative"
              >
                <!-- Timeline Dot -->
                <div class="absolute left-0 top-0 -translate-x-1/2">
                  <div class="w-4 h-4 rounded-full border-2 border-white bg-blue-500" />
                </div>

                <!-- Batch Header -->
                <div class="bg-blue-50 rounded-lg p-4 mb-2">
                  <div class="flex justify-between items-start">
                    <div>
                      <Badge variant="outline" class="mb-2">Batch Operation</Badge>
                      <p class="text-sm font-medium">
                        {{ batchTransactions[0].transaction_type === 'addition' ? 'Bulk Addition' : 'Bulk Deduction' }}
                        - {{ batchTransactions.length }} items
                      </p>
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ formatDate(batchTransactions[0].created_at) }}
                    </div>
                  </div>
                </div>

                <!-- Batch Items -->
                <div class="space-y-2">
                  <div 
                    v-for="transaction in batchTransactions" 
                    :key="transaction.id"
                    class="bg-gray-50 rounded-lg p-3 ml-4"
                  >
                    <div class="grid grid-cols-3 gap-4">
                      <div>
                        <p class="text-xs text-gray-600">Quantity</p>
                        <p class="font-semibold">
                          {{ transaction.transaction_type === 'addition' ? '+' : '-' }}{{ transaction.quantity }} {{ item.unit_type }}
                        </p>
                      </div>
                      <div>
                        <p class="text-xs text-gray-600">Before</p>
                        <p class="font-semibold">{{ transaction.previous_quantity }} {{ item.unit_type }}</p>
                      </div>
                      <div>
                        <p class="text-xs text-gray-600">After</p>
                        <p class="font-semibold">{{ transaction.new_quantity }} {{ item.unit_type }}</p>
                      </div>
                    </div>
                    <div v-if="transaction.notes" class="mt-2 text-sm text-gray-600">
                      {{ transaction.notes }}
                    </div>
                  </div>
                </div>

                <div class="mt-2 text-xs text-gray-500 ml-4">
                  By {{ batchTransactions[0].user?.name }}
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
  </AppLayout>
</template>