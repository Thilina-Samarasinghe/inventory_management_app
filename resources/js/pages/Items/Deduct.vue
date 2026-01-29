<script setup lang="ts">
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import QuantityInput from '@/components/Inventory/QuantityInput.vue';
import StockBadge from '@/components/Inventory/StockBadge.vue';
import { useInventory } from '@/composables/useInventory';
import type { DeductionForm } from '@/types/inventory';

const emit = defineEmits<{
  navigate: [page: string];
}>();

const { items, deductMultipleItems } = useInventory();
const deductions = ref<DeductionForm[]>([
  {
    item_id: 0,
    quantity: 0,
    reference: '',
    notes: '',
  },
]);

const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const addDeductionForm = () => {
  deductions.value.push({
    item_id: 0,
    quantity: 0,
    reference: '',
    notes: '',
  });
};

const removeDeductionForm = (index: number) => {
  if (deductions.value.length > 1) {
    deductions.value.splice(index, 1);
  }
};

const getSelectedItem = (itemId: number) => {
  return items.value.find((item) => item.id === itemId);
};

const validateDeductions = (): boolean => {
  for (const deduction of deductions.value) {
    if (!deduction.item_id) {
      errorMessage.value = 'All deductions must have an item selected';
      return false;
    }
    if (deduction.quantity <= 0) {
      errorMessage.value = 'Quantity must be greater than 0';
      return false;
    }
    const item = getSelectedItem(deduction.item_id);
    if (!item || deduction.quantity > item.quantity) {
      errorMessage.value = 'Insufficient stock for one or more items';
      return false;
    }
  }
  return true;
};

const submit = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  if (!validateDeductions()) {
    return;
  }

  isSubmitting.value = true;
  try {
    const deductedItems = deductMultipleItems(deductions.value);
    successMessage.value = `Successfully deducted from ${deductedItems.length} item(s)`;
    
    // Reset form
    deductions.value = [
      {
        item_id: 0,
        quantity: 0,
        reference: '',
        notes: '',
      },
    ];
    
    // Redirect after 2 seconds
    setTimeout(() => {
      emit('navigate', 'items');
    }, 2000);
  } catch (error) {
    errorMessage.value = error instanceof Error ? error.message : 'An error occurred';
  } finally {
    isSubmitting.value = false;
  }
};

const isValidDeduction = computed(() => {
  return deductions.value.every((d) => {
    if (!d.item_id) return false;
    const item = getSelectedItem(d.item_id);
    return item && d.quantity > 0 && d.quantity <= item.quantity;
  });
});
</script>

<template>
  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Deduct Inventory Items</h2>
        <p class="mt-1 text-sm text-gray-600">Remove quantities from your inventory</p>
      </div>

      <!-- Success Message -->
      <div v-if="successMessage" class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
        {{ successMessage }}
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="submit">
        <!-- Deductions -->
        <div class="space-y-4">
          <Card v-for="(deduction, index) in deductions" :key="index">
            <CardHeader>
              <div class="flex justify-between items-center">
                <CardTitle>Deduction {{ index + 1 }}</CardTitle>
                <Button
                  v-if="deductions.length > 1"
                  type="button"
                  variant="ghost"
                  size="sm"
                  @click="removeDeductionForm(index)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </Button>
              </div>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Item Selection -->
              <div>
                <Label :for="`item-${index}`">Select Item *</Label>
                <select
                  :id="`item-${index}`"
                  v-model.number="deduction.item_id"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                  required
                >
                  <option :value="0">Choose an item</option>
                  <option v-for="item in items" :key="item.id" :value="item.id">
                    {{ item.name }} ({{ item.quantity }} {{ item.unit_type }} available)
                  </option>
                </select>
              </div>

              <!-- Item Info (if selected) -->
              <div v-if="deduction.item_id && getSelectedItem(deduction.item_id)" class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                  <div>
                    <p class="font-medium">{{ getSelectedItem(deduction.item_id)?.name }}</p>
                    <p class="text-sm text-gray-600">
                      Current Stock: {{ getSelectedItem(deduction.item_id)?.quantity }}
                      {{ getSelectedItem(deduction.item_id)?.unit_type }}
                    </p>
                  </div>
                  <StockBadge
                    :quantity="getSelectedItem(deduction.item_id)?.quantity || 0"
                    :reorder-level="getSelectedItem(deduction.item_id)?.reorder_level || 0"
                  />
                </div>
              </div>

              <!-- Quantity to Deduct -->
              <div>
                <Label :for="`quantity-${index}`">Quantity to Deduct *</Label>
                <QuantityInput
                  :id="`quantity-${index}`"
                  v-model.number="deduction.quantity"
                  :unit="getSelectedItem(deduction.item_id)?.unit_type || 'No. of Units'"
                  :max="getSelectedItem(deduction.item_id)?.quantity || 0"
                  required
                />
              </div>

              <!-- Reference -->
              <div>
                <Label :for="`reference-${index}`">Reference (Optional)</Label>
                <input
                  :id="`reference-${index}`"
                  v-model="deduction.reference"
                  type="text"
                  placeholder="e.g., SO-001, Invoice #"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                />
              </div>

              <!-- Notes -->
              <div>
                <Label :for="`notes-${index}`">Notes</Label>
                <textarea
                  :id="`notes-${index}`"
                  v-model="deduction.notes"
                  placeholder="Reason for deduction (optional)"
                  rows="2"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                />
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Add More Button -->
        <div class="mt-4">
          <Button type="button" variant="outline" @click="addDeductionForm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Another Deduction
          </Button>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-3">
          <Button type="button" variant="outline" @click="emit('navigate', 'items')">
            Cancel
          </Button>
          <Button
            type="submit"
            :disabled="isSubmitting || !isValidDeduction"
            variant="destructive"
          >
            {{ isSubmitting ? 'Deducting...' : `Deduct ${deductions.length} Item${deductions.length > 1 ? 's' : ''}` }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>
