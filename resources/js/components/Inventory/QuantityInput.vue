<script setup lang="ts">
import { Input } from '@/Components/ui/input';

interface Props {
  modelValue: number | null;
  unit: string;
  id?: string;
  placeholder?: string;
  required?: boolean;
  min?: number;
  max?: number;
}

interface Emits {
  (e: 'update:modelValue', value: number | null): void;
}

defineProps<Props>();
const emit = defineEmits<Emits>();

const handleInput = (event: Event) => {
  const value = (event.target as HTMLInputElement).value;
  emit('update:modelValue', value ? parseFloat(value) : null);
};
</script>

<template>
  <div class="relative">
    <Input
      :id="id"
      type="number"
      step="0.01"
      :value="modelValue"
      :placeholder="placeholder"
      :required="required"
      :min="min ?? 0"
      :max="max"
      @input="handleInput"
      class="pr-16"
    />
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
      <span class="text-gray-500 text-sm">{{ unit }}</span>
    </div>
  </div>
</template>