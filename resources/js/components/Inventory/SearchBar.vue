<script setup lang="ts">
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { useDebounceFn } from '@vueuse/core';

interface Props {
  modelValue: string;
  placeholder?: string;
}

interface Emits {
  (e: 'update:modelValue', value: string): void;
  (e: 'search', value: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const localValue = ref(props.modelValue);

const debouncedSearch = useDebounceFn((value: string) => {
  emit('update:modelValue', value);
  emit('search', value);
}, 500);

watch(localValue, (newValue) => {
  debouncedSearch(newValue);
});
</script>

<template>
  <div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
    </div>
    <Input
      v-model="localValue"
      type="text"
      :placeholder="placeholder || 'Search...'"
      class="pl-10"
    />
  </div>
</template>
