<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import ItemTable from '@/Components/Inventory/ItemTable.vue';
import type { Item } from '@/Types/inventory';

interface Props {
  items: {
    data: Item[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search?: string;
    unit_type?: string;
  };
}

const props = defineProps<Props>();

const search = ref(props.filters.search || '');
const unitType = ref(props.filters.unit_type || '');

const performSearch = () => {
  router.get(route('items.index'), {
    search: search.value,
    unit_type: unitType.value,
  }, {
    preserveState: true,
    preserveScroll: true,
  });
};

const clearFilters = () => {
  search.value = '';
  unitType.value = '';
  performSearch();
};

const hasFilters = computed(() => search.value || unitType.value);
</script>

<template>
  <AppLayout>
    <Head title="Inventory Items" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h2 class="text-3xl font-bold text-gray-900">Inventory Items</h2>
            <p class="mt-1 text-sm text-gray-600">Manage your inventory items</p>
          </div>
          <div class="flex gap-3">
            <Link :href="route('items.create')">
              <Button>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Items
              </Button>
            </Link>
            <Link href="/items/deduct">
              <Button variant="outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                Deduct Items
              </Button>
            </Link>
          </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
              <Input
                v-model="search"
                type="text"
                placeholder="Search items by name..."
                @keyup.enter="performSearch"
              />
            </div>
            <div>
              <Select v-model="unitType" @update:model-value="performSearch">
                <SelectTrigger>
                  <SelectValue placeholder="Filter by unit type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="">All Units</SelectItem>
                  <SelectItem value="kg">Kilograms (kg)</SelectItem>
                  <SelectItem value="m">Meters (m)</SelectItem>
                  <SelectItem value="cm">Centimeters (cm)</SelectItem>
                  <SelectItem value="units">Units</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          <div v-if="hasFilters" class="mt-3 flex items-center gap-2">
            <span class="text-sm text-gray-600">Active filters:</span>
            <Button size="sm" variant="outline" @click="clearFilters">Clear all</Button>
          </div>
        </div>

        <!-- Items Table -->
        <ItemTable :items="items.data" />

        <!-- Pagination -->
        <div v-if="items.last_page > 1" class="mt-6 flex justify-center">
          <div class="flex gap-2">
            <Button
              v-for="page in items.last_page"
              :key="page"
              :variant="page === items.current_page ? 'default' : 'outline'"
              size="sm"
              @click="router.get(route('items.index', { page, search: search, unit_type: unitType }))"
            >
              {{ page }}
            </Button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="items.data.length === 0" class="bg-white rounded-lg shadow p-12 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
          </svg>
          <h3 class="mt-2 text-lg font-medium text-gray-900">No items found</h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ hasFilters ? 'Try adjusting your filters' : 'Get started by adding your first item' }}
          </p>
          <div class="mt-6">
            <Link :href="route('items.create')">
              <Button>Add Items</Button>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>