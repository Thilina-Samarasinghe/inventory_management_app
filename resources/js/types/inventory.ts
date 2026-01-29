export type UnitType = 'Kg' | 'm' | 'cm' | 'No. of Units';
export type TransactionType = 'add' | 'deduct';

export interface Item {
  id: number;
  name: string;
  description: string | null;
  quantity: number;
  unit_type: UnitType;
  reorder_level: number;
  stock_status?: 'in_stock' | 'low_stock' | 'out_of_stock';
  created_at: string;
  updated_at: string;
  deleted_at?: string | null;
}

export interface Transaction {
  id: number;
  item_id: number;
  transaction_type: TransactionType;
  quantity: number;
  reference?: string | null;
  notes?: string | null;
  transaction_date: string;
  created_at: string;
  updated_at: string;
  item?: Item;
}

export interface ItemForm {
  name: string;
  description?: string;
  quantity: number;
  unit_type: UnitType;
  reorder_level?: number;
}

export interface BulkAddItem {
  name: string;
  description?: string;
  quantity: number;
  unit_type: UnitType;
  reorder_level?: number;
}

export interface DeductionForm {
  item_id: number;
  quantity: number;
  reference?: string;
  notes?: string;
}

export interface BulkDeductItem {
  item_id: number;
  quantity: number;
  reference?: string;
  notes?: string;
}
