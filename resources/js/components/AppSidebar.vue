<script setup lang="ts">
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";
import { usePage } from "@inertiajs/vue3";
import NavUser from "@/components/NavUser.vue";

const page = usePage();

const menuItems = [
  { title: "Dashboard", url: "/dashboard", icon: "📊" },
  { title: "All Items", url: "/items", icon: "📦" },
  { title: "Add Items", url: "/items/create", icon: "➕" },
  { title: "Deduct Items", url: "/items/deduct", icon: "➖" },
];
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
                <a :href="item.url">
                  <span>{{ item.icon }}</span>
                  <span>{{ item.title }}</span>
                </a>
              </SidebarMenuButton>
            </SidebarMenuItem>
          </SidebarMenu>
        </SidebarGroupContent>
      </SidebarGroup>
    </SidebarContent>

    <SidebarFooter>
      <!-- ✅ Pass the authenticated user -->
      <NavUser :user="page.props.auth.user" />
    </SidebarFooter>
  </Sidebar>
</template>