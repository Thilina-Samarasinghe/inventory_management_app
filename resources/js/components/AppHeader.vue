<script setup lang="ts">
import { LayoutGrid } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { getInitials } from '@/composables/useInitials';

const mainNavItems = [
    {
        title: 'Dashboard',
        href: '#',
        icon: LayoutGrid,
    },
];

const userName = 'User';
</script>

<template>
    <div class="border-b border-sidebar-border/80">
        <div class="mx-auto flex h-16 items-center justify-between px-4">
            <div class="flex items-center gap-2">
                <AppLogo />
            </div>
            <div class="flex items-center gap-4">
                <DropdownMenu>
                    <DropdownMenuTrigger :as-child="true">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="relative size-10 w-auto rounded-full p-1"
                        >
                            <Avatar class="size-8 overflow-hidden rounded-full">
                                <AvatarFallback class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                    {{ getInitials(userName) }}
                                </AvatarFallback>
                            </Avatar>
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-48">
                        <div class="px-2 py-1.5">
                            <p class="text-sm font-medium">{{ userName }}</p>
                            <p class="text-xs text-gray-500">user@example.com</p>
                        </div>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </div>
</template>
