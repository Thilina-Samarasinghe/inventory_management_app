<script setup lang="ts">
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';

interface Props {
  modelValue: string;
  id?: string;
}

interface Emits {
  (e: 'update:modelValue', value: string): void;
}

defineProps<Props>();
const emit = defineEmits<Emits>();

const units = [
  { value: 'kg', label: 'Kilograms (kg)' },
  { value: 'm', label: 'Meters (m)' },
  { value: 'cm', label: 'Centimeters (cm)' },
  { value: 'units', label: 'Units' },
];
</script>

<template>
  <Select :model-value="modelValue" @update:model-value="emit('update:modelValue', $event)">
    <SelectTrigger :id="id">
      <SelectValue placeholder="Select unit type" />
    </SelectTrigger>
    <SelectContent>
      <SelectItem v-for="unit in units" :key="unit.value" :value="unit.value">
        {{ unit.label }}
      </SelectItem>
    </SelectContent>
  </Select>
</template>