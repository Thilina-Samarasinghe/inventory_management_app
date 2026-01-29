export interface Item {
  id: number;
  name: string;
  description: string | null;
  unit_type: 'kg' | 'm' | 'cm' | 'units';
  current_quantity: string | number;
  minimum_quantity: string | number | null;
  stock_status: 'in_stock' | 'low_stock' | 'out_of_stock';
  created_at: string;
  updated_at: string;
}

export interface Transaction {
  id: number;
  item_id: number;
  transaction_type: 'addition' | 'deduction';
  quantity: string | number;
  previous_quantity: string | number;
  new_quantity: string | number;
  notes: string | null;
  batch_id: string | null;
  user_id: number;
  created_at: string;
  item?: Item;
  user?: {
    id: number;
    name: string;
    email: string;
  };
}

export interface ItemForm {
  name: string;
  description: string;
  unit_type: 'kg' | 'm' | 'cm' | 'units';
  quantity: number;
  minimum_quantity: number | null;
}

export interface DeductionForm {
  id: number;
  quantity: number;
  notes: string;
}