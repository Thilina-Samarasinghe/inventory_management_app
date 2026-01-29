<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import UnitSelect from '@/Components/Inventory/UnitSelect.vue';
import QuantityInput from '@/Components/Inventory/QuantityInput.vue';
import type { ItemForm } from '@/Types/inventory';

const items = ref<ItemForm[]>([
  {
    name: '',
    description: '',
    unit_type: 'units',
    quantity: 0,
    minimum_quantity: null,
  },
]);

const addItem = () => {
  items.value.push({
    name: '',
    description: '',
    unit_type: 'units',
    quantity: 0,
    minimum_quantity: null,
  });
};

const removeItem = (index: number) => {
  if (items.value.length > 1) {
    items.value.splice(index, 1);
  }
};

const form = useForm({
  items: items,
});

const submit = () => {
  form.post(route('items.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppLayout>
    <Head title="Add Items" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <h2 class="text-3xl font-bold text-gray-900">Add Inventory Items</h2>
          <p class="mt-1 text-sm text-gray-600">Add one or multiple items to your inventory</p>
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
                    @click="removeItem(index)"
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
                    :class="{ 'border-red-500': form.errors[`items.${index}.name`] }"
                  />
                  <p v-if="form.errors[`items.${index}.name`]" class="text-sm text-red-500 mt-1">
                    {{ form.errors[`items.${index}.name`] }}
                  </p>
                </div>

                <!-- Description -->
                <div>
                  <Label :for="`description-${index}`">Description</Label>
                  <Textarea
                    :id="`description-${index}`"
                    v-model="item.description"
                    placeholder="Optional description"
                    rows="2"
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
                      v-model="item.quantity"
                      :unit="item.unit_type"
                      required
                    />
                    <p v-if="form.errors[`items.${index}.quantity`]" class="text-sm text-red-500 mt-1">
                      {{ form.errors[`items.${index}.quantity`] }}
                    </p>
                  </div>

                  <!-- Minimum Quantity -->
                  <div>
                    <Label :for="`min-quantity-${index}`">Min. Quantity</Label>
                    <QuantityInput
                      :id="`min-quantity-${index}`"
                      v-model="item.minimum_quantity"
                      :unit="item.unit_type"
                      placeholder="Alert threshold"
                    />
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Add More Button -->
          <div class="mt-4">
            <Button type="button" variant="outline" @click="addItem">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Add Another Item
            </Button>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end gap-3">
            <Button type="button" variant="outline" @click="$inertia.visit(route('items.index'))">
              Cancel
            </Button>
            <Button type="submit" :disabled="form.processing">
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              {{ form.processing ? 'Adding...' : `Add ${items.length} Item${items.length > 1 ? 's' : ''}` }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>