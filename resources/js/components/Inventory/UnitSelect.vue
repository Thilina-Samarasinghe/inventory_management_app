<script setup lang="ts">
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
  { value: 'Kg', label: 'Kilograms (Kg)' },
  { value: 'm', label: 'Meters (m)' },
  { value: 'cm', label: 'Centimeters (cm)' },
  { value: 'No. of Units', label: 'Units' },
];

const handleChange = (event: Event) => {
  const value = (event.target as HTMLSelectElement).value;
  emit('update:modelValue', value);
};
</script>

<template>
  <select
    :id="id"
    :value="modelValue"
    @change="handleChange"
    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary bg-white"
  >
    <option value="">Select unit type</option>
    <option v-for="unit in units" :key="unit.value" :value="unit.value">
      {{ unit.label }}
    </option>
  </select>
</template>
