<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import UnitSelect from '@/components/Inventory/UnitSelect.vue';
import QuantityInput from '@/components/Inventory/QuantityInput.vue';
import { useInventory } from '@/composables/useInventory';
import type { BulkAddItem } from '@/types/inventory';

const emit = defineEmits<{
  navigate: [page: string];
}>();

const { addMultipleItems } = useInventory();
const items = ref<BulkAddItem[]>([
  {
    name: '',
    description: '',
    unit_type: 'Kg',
    quantity: 0,
    reorder_level: 10,
  },
]);
const isSubmitting = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const addItemForm = () => {
  items.value.push({
    name: '',
    description: '',
    unit_type: 'Kg',
    quantity: 0,
    reorder_level: 10,
  });
};

const removeItemForm = (index: number) => {
  if (items.value.length > 1) {
    items.value.splice(index, 1);
  }
};

const validateItems = (): boolean => {
  for (const item of items.value) {
    if (!item.name.trim()) {
      errorMessage.value = 'All items must have a name';
      return false;
    }
    if (item.quantity <= 0) {
      errorMessage.value = 'Quantity must be greater than 0';
      return false;
    }
  }
  return true;
};

const submit = async () => {
  errorMessage.value = '';
  successMessage.value = '';

  if (!validateItems()) {
    return;
  }

  isSubmitting.value = true;
  try {
    const addedItems = addMultipleItems(items.value);
    successMessage.value = `Successfully added ${addedItems.length} item(s)`;
    
    // Reset form
    items.value = [
      {
        name: '',
        description: '',
        unit_type: 'Kg',
        quantity: 0,
        reorder_level: 10,
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
</script>

<template>
  <div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Add Inventory Items</h2>
        <p class="mt-1 text-sm text-gray-600">Add one or multiple items to your inventory</p>
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
        <!-- Items -->
        <div class="space-y-4">
          <Card v-for="(item, index) in items" :key="index">
            <CardHeader>
              <div class="flex justify-between items-center">
                <CardTitle>Item {{ index + 1 }}</CardTitle>
                <Button
                  v-if="items.length > 1"
                  type="button"
                  variant="ghost"
                  size="sm"
                  @click="removeItemForm(index)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </Button>
              </div>
            </CardHeader>
            <CardContent class="space-y-4">
              <!-- Name -->
              <div>
                <Label :for="`name-${index}`">Item Name *</Label>
                <Input
                  :id="`name-${index}`"
                  v-model="item.name"
                  type="text"
                  required
                  placeholder="e.g., Rice, Steel Rod, Bolts"
                />
              </div>

              <!-- Description -->
              <div>
                <Label :for="`description-${index}`">Description</Label>
                <Input
                  :id="`description-${index}`"
                  v-model="item.description"
                  type="text"
                  placeholder="Optional description"
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Unit Type -->
                <div>
                  <Label :for="`unit-${index}`">Unit Type *</Label>
                  <UnitSelect
                    :id="`unit-${index}`"
                    v-model="item.unit_type"
                  />
                </div>

                <!-- Quantity -->
                <div>
                  <Label :for="`quantity-${index}`">Quantity *</Label>
                  <QuantityInput
                    :id="`quantity-${index}`"
                    v-model.number="item.quantity"
                    :unit="item.unit_type"
                    required
                  />
                </div>

                <!-- Reorder Level -->
                <div>
                  <Label :for="`reorder-${index}`">Reorder Level</Label>
                  <QuantityInput
                    :id="`reorder-${index}`"
                    v-model.number="item.reorder_level"
                    :unit="item.unit_type"
                    placeholder="10"
                  />
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Add More Button -->
        <div class="mt-4">
          <Button type="button" variant="outline" @click="addItemForm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Another Item
          </Button>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex justify-end gap-3">
          <Button type="button" variant="outline" @click="emit('navigate', 'items')">
            Cancel
          </Button>
          <Button type="submit" :disabled="isSubmitting">
            {{ isSubmitting ? 'Adding...' : `Add ${items.length} Item${items.length > 1 ? 's' : ''}` }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>
