<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import QuantityInput from '@/Components/Inventory/QuantityInput.vue';
import StockBadge from '@/Components/Inventory/StockBadge.vue';
import type { Item, DeductionForm } from '@/Types/inventory';

interface Props {
  items: Item[];
}

const props = defineProps<Props>();

const deductions = ref<DeductionForm[]>([
  {
    id: 0,
    quantity: 0,
    notes: '',
  },
]);

const addDeduction = () => {
  deductions.value.push({
    id: 0,
    quantity: 0,
    notes: '',
  });
};

const removeDeduction = (index: number) => {
  if (deductions.value.length > 1) {
    deductions.value.splice(index, 1);
  }
};

const getSelectedItem = (id: number): Item | undefined => {
  return props.items.find(item => item.id === id);
};

const form = useForm({
  deductions: deductions,
});

const submit = () => {
  form.post(route('items.deduct'), {
    preserveScroll: true,
  });
};

const isValidDeduction = computed(() => {
  return deductions.value.every(d => {
    if (!d.id) return false;
    const item = getSelectedItem(d.id);
    return item && d.quantity > 0 && d.quantity <= Number(item.current_quantity);
  });
});
</script>

<template>
  <AppLayout>
    <Head title="Deduct Items" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
          <h2 class="text-3xl font-bold text-gray-900">Deduct Inventory Items</h2>
          <p class="mt-1 text-sm text-gray-600">Remove quantities from your inventory</p>
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
                    @click="removeDeduction(index)"
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
                  <Select v-model="deduction.id">
                    <SelectTrigger :id="`item-${index}`">
                      <SelectValue placeholder="Choose an item" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem
                        v-for="item in items"
                        :key="item.id"
                        :value="item.id.toString()"
                      >
                        {{ item.name }} ({{ item.current_quantity }} {{ item.unit_type }} available)
                      </SelectItem>
                    </SelectContent>
                  </Select>
                  <p v-if="form.errors[`deductions.${index}.id`]" class="text-sm text-red-500 mt-1">
                    {{ form.errors[`deductions.${index}.id`] }}
                  </p>
                </div>

                <!-- Item Info (if selected) -->
                <div v-if="deduction.id && getSelectedItem(deduction.id)" class="bg-gray-50 p-4 rounded-lg">
                  <div class="flex justify-between items-center">
                    <div>
                      <p class="font-medium">{{ getSelectedItem(deduction.id)?.name }}</p>
                      <p class="text-sm text-gray-600">
                        Current Stock: {{ getSelectedItem(deduction.id)?.current_quantity }} 
                        {{ getSelectedItem(deduction.id)?.unit_type }}
                      </p>
                    </div>
                    <StockBadge :status="getSelectedItem(deduction.id)?.stock_status || 'in_stock'" />
                  </div>
                </div>

                <!-- Quantity to Deduct -->
                <div>
                  <Label :for="`quantity-${index}`">Quantity to Deduct *</Label>
                  <QuantityInput
                    :id="`quantity-${index}`"
                    v-model="deduction.quantity"
                    :unit="getSelectedItem(deduction.id)?.unit_type || 'units'"
                    :max="Number(getSelectedItem(deduction.id)?.current_quantity || 0)"
                    required
                  />
                  <p v-if="form.errors[`deductions.${index}.quantity`]" class="text-sm text-red-500 mt-1">
                    {{ form.errors[`deductions.${index}.quantity`] }}
                  </p>
                </div>

                <!-- Notes -->
                <div>
                  <Label :for="`notes-${index}`">Notes</Label>
                  <Textarea
                    :id="`notes-${index}`"
                    v-model="deduction.notes"
                    placeholder="Reason for deduction (optional)"
                    rows="2"
                  />
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Add More Button -->
          <div class="mt-4">
            <Button type="button" variant="outline" @click="addDeduction">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
              Add Another Deduction
            </Button>
          </div>

          <!-- Actions -->
          <div class="mt-6 flex justify-end gap-3">
            <Button type="button" variant="outline" @click="$inertia.visit(route('items.index'))">
              Cancel
            </Button>
            <Button 
              type="submit" 
              :disabled="form.processing || !isValidDeduction"
              variant="destructive"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
              </svg>
              {{ form.processing ? 'Deducting...' : `Deduct ${deductions.length} Item${deductions.length > 1 ? 's' : ''}` }}
            </Button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>