<script setup lang="ts">
import {
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar";
import UserInfo from "@/components/UserInfo.vue";
import { logout } from "@/routes";
import { usePage } from "@inertiajs/vue3";

// Make user prop optional
const props = defineProps<{
  user?: {
    name: string;
    email: string;
    avatar?: string;
  };
}>();

const page = usePage();

// Use prop or get from page
const currentUser = props.user || page.props.auth?.user;

const handleLogout = () => {
  if (confirm('Are you sure you want to logout?')) {
    window.location.href = logout;
  }
};
</script>

<template>
  <SidebarMenu v-if="currentUser">
    <SidebarMenuItem>
      <div class="flex items-center gap-2 p-2">
        <UserInfo :user="currentUser" />
        <SidebarMenuButton @click="handleLogout" class="ml-auto">
          <span>Logout</span>
        </SidebarMenuButton>
      </div>
    </SidebarMenuItem>
  </SidebarMenu>
</template>