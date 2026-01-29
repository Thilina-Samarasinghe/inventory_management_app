<script setup lang="ts">
const props = defineProps<{
  user: {
    name: string;
    email: string;
    avatar?: string;
  };
}>();

// Get first letter safely
const initial = props.user?.name?.charAt(0)?.toUpperCase() || '?';
</script>

<template>
  <div class="flex items-center gap-2">
    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground">
      {{ initial }}
    </div>
    <div class="flex flex-col">
      <span class="text-sm font-medium">{{ user.name }}</span>
      <span class="text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
  </div>
</template>