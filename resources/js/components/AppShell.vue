<script setup lang="ts">
import { ref } from 'vue';
import { SidebarProvider } from '@/components/ui/sidebar';
import AppSidebar from '@/components/AppSidebar.vue';
import AppHeader from '@/components/AppHeader.vue';
import Dashboard from '@/pages/Dashboard.vue';

const currentPage = ref('dashboard');
const sidebarOpen = ref(true);

// Mock data for now
const dashboardProps = {
  stats: {
    total_items: 45,
    low_stock_items: 8,
    out_of_stock_items: 2,
    total_transactions: 156,
  },
  recentTransactions: [],
  lowStockItems: [],
};
</script>

<template>
  <SidebarProvider :default-open="sidebarOpen">
    <AppSidebar />
    <div class="flex min-h-screen w-full flex-col">
      <AppHeader />
      <main class="flex-1 overflow-y-auto">
        <Dashboard v-if="currentPage === 'dashboard'" v-bind="dashboardProps" />
      </main>
    </div>
  </SidebarProvider>
</template>
