<script setup lang="ts">
import { ref } from 'vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import AppSidebar from '@/components/AppSidebar.vue';
import AppHeader from '@/components/AppHeader.vue';
import Dashboard from '@/pages/Dashboard.vue';
import ItemsIndex from '@/pages/Items/Index.vue';
import ItemsCreate from '@/pages/Items/Create.vue';
import ItemsDeduct from '@/pages/Items/Deduct.vue';
import ItemsHistory from '@/pages/Items/History.vue';
import { useInventory } from '@/composables/useInventory';

const currentPage = ref('dashboard');
const sidebarOpen = ref(true);
const selectedItemId = ref<number | null>(null);

const { stats, recentTransactions, lowStockItems } = useInventory();

const dashboardProps = {
  stats,
  recentTransactions,
  lowStockItems,
};

const navigateTo = (page: string, itemId?: number) => {
  currentPage.value = page;
  if (itemId) {
    selectedItemId.value = itemId;
  }
};
</script>

<template>
  <SidebarProvider :default-open="sidebarOpen">
    <AppSidebar @navigate="navigateTo" />
    <div class="flex min-h-screen w-full flex-col">
      <AppHeader />
      <main class="flex-1 overflow-y-auto">
        <Dashboard v-if="currentPage === 'dashboard'" v-bind="dashboardProps" />
        <ItemsIndex v-else-if="currentPage === 'items'" @navigate="navigateTo" />
        <ItemsCreate v-else-if="currentPage === 'items-create'" @navigate="navigateTo" />
        <ItemsDeduct v-else-if="currentPage === 'items-deduct'" @navigate="navigateTo" />
        <ItemsHistory
          v-else-if="currentPage === 'items-history' && selectedItemId"
          :item-id="selectedItemId"
          @navigate="navigateTo"
        />
      </main>
    </div>
  </SidebarProvider>
</template>
