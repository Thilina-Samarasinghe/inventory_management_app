import { ref, computed } from 'vue';
import type { Item, Transaction, BulkAddItem, DeductionForm } from '@/types/inventory';

// Mock data for demonstration
const mockItems = ref<Item[]>([
  {
    id: 1,
    name: 'Rice',
    description: 'Premium white rice',
    quantity: 50,
    unit_type: 'Kg',
    reorder_level: 10,
    created_at: '2026-01-20T10:00:00Z',
    updated_at: '2026-01-29T10:00:00Z',
  },
  {
    id: 2,
    name: 'Wheat Flour',
    description: 'All-purpose flour',
    quantity: 3,
    unit_type: 'Kg',
    reorder_level: 5,
    created_at: '2026-01-22T10:00:00Z',
    updated_at: '2026-01-29T10:00:00Z',
  },
  {
    id: 3,
    name: 'Cloth Roll',
    description: 'Cotton cloth',
    quantity: 0,
    unit_type: 'm',
    reorder_level: 10,
    created_at: '2026-01-25T10:00:00Z',
    updated_at: '2026-01-29T10:00:00Z',
  },
]);

const mockTransactions = ref<Transaction[]>([
  {
    id: 1,
    item_id: 1,
    transaction_type: 'add',
    quantity: 50,
    reference: 'PO-001',
    notes: 'Initial stock',
    transaction_date: '2026-01-20T10:00:00Z',
    created_at: '2026-01-20T10:00:00Z',
    updated_at: '2026-01-20T10:00:00Z',
  },
  {
    id: 2,
    item_id: 1,
    transaction_type: 'deduct',
    quantity: 5,
    reference: 'SO-001',
    notes: 'Sold to customer',
    transaction_date: '2026-01-25T10:00:00Z',
    created_at: '2026-01-25T10:00:00Z',
    updated_at: '2026-01-25T10:00:00Z',
  },
]);

export function useInventory() {
  const items = ref<Item[]>(mockItems.value);
  const transactions = ref<Transaction[]>(mockTransactions.value);
  const searchQuery = ref('');
  const selectedUnit = ref<string>('');

  // Computed properties for stats
  const stats = computed(() => {
    const totalItems = items.value.length;
    const lowStockItems = items.value.filter(
      (item) => item.quantity <= item.reorder_level && item.quantity > 0
    ).length;
    const outOfStockItems = items.value.filter((item) => item.quantity === 0).length;
    const totalTransactions = transactions.value.length;

    return {
      total_items: totalItems,
      low_stock_items: lowStockItems,
      out_of_stock_items: outOfStockItems,
      total_transactions: totalTransactions,
    };
  });

  // Filtered items based on search and unit
  const filteredItems = computed(() => {
    return items.value.filter((item) => {
      const matchesSearch = item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
      const matchesUnit = !selectedUnit.value || item.unit_type === selectedUnit.value;
      return matchesSearch && matchesUnit;
    });
  });

  // Get item history
  const getItemHistory = (itemId: number) => {
    return transactions.value
      .filter((t) => t.item_id === itemId)
      .sort(
        (a, b) =>
          new Date(b.transaction_date).getTime() - new Date(a.transaction_date).getTime()
      );
  };

  // Get recent transactions
  const recentTransactions = computed(() => {
    return transactions.value
      .sort(
        (a, b) =>
          new Date(b.transaction_date).getTime() - new Date(a.transaction_date).getTime()
      )
      .slice(0, 5)
      .map((t) => ({
        ...t,
        item: items.value.find((i) => i.id === t.item_id),
      }));
  });

  // Get low stock items
  const lowStockItems = computed(() => {
    return items.value
      .filter((item) => item.quantity <= item.reorder_level && item.quantity > 0)
      .slice(0, 5);
  });

  // Add single item
  const addItem = (item: BulkAddItem) => {
    const newItem: Item = {
      id: Math.max(...items.value.map((i) => i.id), 0) + 1,
      name: item.name,
      description: item.description || null,
      quantity: item.quantity,
      unit_type: item.unit_type,
      reorder_level: item.reorder_level || 10,
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    };

    items.value.push(newItem);

    // Add transaction
    const transaction: Transaction = {
      id: Math.max(...transactions.value.map((t) => t.id), 0) + 1,
      item_id: newItem.id,
      transaction_type: 'add',
      quantity: item.quantity,
      reference: undefined,
      notes: 'Initial stock added',
      transaction_date: new Date().toISOString(),
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    };

    transactions.value.push(transaction);
    return newItem;
  };

  // Add multiple items
  const addMultipleItems = (newItems: BulkAddItem[]) => {
    const addedItems: Item[] = [];
    newItems.forEach((item) => {
      addedItems.push(addItem(item));
    });
    return addedItems;
  };

  // Deduct from item
  const deductItem = (deduction: DeductionForm) => {
    const item = items.value.find((i) => i.id === deduction.item_id);
    if (!item) throw new Error('Item not found');

    if (item.quantity < deduction.quantity) {
      throw new Error('Insufficient stock');
    }

    item.quantity -= deduction.quantity;
    item.updated_at = new Date().toISOString();

    // Add transaction
    const transaction: Transaction = {
      id: Math.max(...transactions.value.map((t) => t.id), 0) + 1,
      item_id: item.id,
      transaction_type: 'deduct',
      quantity: deduction.quantity,
      reference: deduction.reference,
      notes: deduction.notes,
      transaction_date: new Date().toISOString(),
      created_at: new Date().toISOString(),
      updated_at: new Date().toISOString(),
    };

    transactions.value.push(transaction);
    return item;
  };

  // Deduct from multiple items
  const deductMultipleItems = (deductions: DeductionForm[]) => {
    const deductedItems: Item[] = [];
    deductions.forEach((deduction) => {
      deductedItems.push(deductItem(deduction));
    });
    return deductedItems;
  };

  // Search items
  const searchItems = (query: string) => {
    searchQuery.value = query;
  };

  // Filter by unit
  const filterByUnit = (unit: string) => {
    selectedUnit.value = unit;
  };

  // Clear filters
  const clearFilters = () => {
    searchQuery.value = '';
    selectedUnit.value = '';
  };

  return {
    items,
    transactions,
    stats,
    filteredItems,
    recentTransactions,
    lowStockItems,
    searchQuery,
    selectedUnit,
    getItemHistory,
    addItem,
    addMultipleItems,
    deductItem,
    deductMultipleItems,
    searchItems,
    filterByUnit,
    clearFilters,
  };
}
