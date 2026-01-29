<script setup lang="ts">
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";

const emit = defineEmits<{
  navigate: [page: string];
}>();

const menuItems = [
  { title: "Dashboard", page: "dashboard", icon: "📊" },
  { title: "All Items", page: "items", icon: "📦" },
  { title: "Add Items", page: "items-create", icon: "➕" },
  { title: "Deduct Items", page: "items-deduct", icon: "➖" },
];

const handleNavigate = (page: string) => {
  emit('navigate', page);
};
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <div class="flex items-center gap-2 p-4">
        <span class="text-lg font-bold">Inventory</span>
      </div>
    </SidebarHeader>

    <SidebarContent>
      <SidebarGroup>
        <SidebarGroupLabel>Menu</SidebarGroupLabel>
        <SidebarGroupContent>
          <SidebarMenu>
            <SidebarMenuItem v-for="item in menuItems" :key="item.title">
              <SidebarMenuButton as-child>
                <button
                  @click="handleNavigate(item.page)"
                  class="w-full text-left flex items-center gap-2"
                >
                  <span>{{ item.icon }}</span>
                  <span>{{ item.title }}</span>
                </button>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>
  </Sidebar>
</template>
