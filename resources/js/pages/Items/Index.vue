<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { useInventory } from '@/composables/useInventory';
import StockBadge from '@/components/Inventory/StockBadge.vue';

const emit = defineEmits<{
  navigate: [page: string, itemId?: number];
}>();

const { filteredItems, searchItems, filterByUnit, clearFilters, items } = useInventory();
const search = ref('');
const unitType = ref('');
const units = ['Kg', 'm', 'cm', 'No. of Units'] as const;

const handleSearch = (query: string) => {
  search.value = query;
  searchItems(query);
};

const handleUnitFilter = (unit: string) => {
  unitType.value = unit;
  filterByUnit(unit);
};

const handleClearFilters = () => {
};
</script>

<template>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6 flex justify-between items-center">
        <div>
          <h2 class="text-3xl font-bold text-gray-900">Inventory Items</h2>
          <p class="mt-1 text-sm text-gray-600">Manage your inventory items</p>
        </div>
        <div class="flex gap-3">
          <Button @click="emit('navigate', 'items-create')">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Items
          </Button>
          <Button variant="outline" @click="emit('navigate', 'items-deduct')">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
            Deduct Items
          </Button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-2">
            <Input
              :value="search"
              type="text"
              placeholder="Search items by name..."
              @input="handleSearch(($event.target as HTMLInputElement).value)"
              @keyup.enter="handleSearch(search)"
            />
          </div>
          <div>
            <select
              :value="unitType"
              @change="handleUnitFilter(($event.target as HTMLSelectElement).value)"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
            >
              <option value="">All Units</option>
              <option value="Kg">Kilograms (Kg)</option>
              <option value="m">Meters (m)</option>
              <option value="cm">Centimeters (cm)</option>
              <option value="No. of Units">Units</option>
            </select>
          </div>
        </div>
        <div v-if="search || unitType" class="mt-3 flex items-center gap-2">
          <span class="text-sm text-gray-600">Active filters:</span>
          <Button size="sm" variant="outline" @click="handleClearFilters">Clear all</Button>
        </div>
      </div>

      <!-- Items Grid -->
      <div v-if="filteredItems.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card v-for="item in filteredItems" :key="item.id">
          <CardHeader>
            <div class="flex justify-between items-start">
              <CardTitle class="text-lg">{{ item.name }}</CardTitle>
              <StockBadge :quantity="item.quantity" :reorder-level="item.reorder_level" />
            </div>
            <p v-if="item.description" class="text-sm text-gray-600 mt-2">{{ item.description }}</p>
          </CardHeader>
          <CardContent class="space-y-4">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Current Stock:</span>
              <span class="font-semibold">{{ item.quantity }} {{ item.unit_type }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-600">Reorder Level:</span>
              <span class="text-sm">{{ item.reorder_level }} {{ item.unit_type }}</span>
            </div>
            <div class="flex gap-2">
              <Button
                size="sm"
                variant="outline"
                @click="emit('navigate', 'items-history', item.id)"
                class="flex-1"
              >
                History
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Empty State -->
      <div v-else class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">No items found</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ (search || unitType) ? 'Try adjusting your filters' : 'Get started by adding your first item' }}
        </p>
        <div class="mt-6">
          <Button @click="emit('navigate', 'items-create')">Add Items</Button>
        </div>
      </div>
    </div>
  </div>
</template>
