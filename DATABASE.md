# Inventory Management System - Database Reference

## Database Overview

The application uses a relational database with two main tables: `items` and `inventory_transactions`.

---

## Table: `items`

Stores all inventory items in the system.

### Schema

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT | No | AUTO_INCREMENT | Primary key, unique identifier |
| `name` | VARCHAR(255) | No | - | Item name (required) |
| `description` | TEXT | Yes | NULL | Item description |
| `quantity` | DECIMAL(10,2) | No | - | Current stock quantity |
| `unit_type` | ENUM | No | - | Measurement unit |
| `reorder_level` | DECIMAL(10,2) | Yes | 10 | Stock alert threshold |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Last update time |
| `deleted_at` | TIMESTAMP | Yes | NULL | Soft delete timestamp |

### Unit Types

Valid `unit_type` values:
- `Kg` - Kilograms
- `m` - Meters
- `cm` - Centimeters
- `No. of Units` - Individual units/pieces

### Example Records

```sql
INSERT INTO items (name, description, quantity, unit_type, reorder_level)
VALUES
  ('Rice', 'Premium basmati rice', 500, 'Kg', 100),
  ('Steel Rod', 'High tensile steel rod', 50, 'm', 20),
  ('Bolts', 'M8 stainless steel bolts', 1000, 'No. of Units', 200),
  ('Copper Wire', '2mm copper wire coil', 100, 'm', 30);
```

### Stock Status Logic

```
- IN_STOCK: quantity >= reorder_level
- LOW_STOCK: 0 < quantity < reorder_level
- OUT_OF_STOCK: quantity = 0
```

---

## Table: `inventory_transactions`

Records all inventory changes (additions and deductions).

### Schema

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT | No | AUTO_INCREMENT | Primary key |
| `item_id` | BIGINT | No | - | Foreign key to items table |
| `transaction_type` | ENUM | No | - | 'add' or 'deduct' |
| `quantity` | DECIMAL(10,2) | No | - | Amount changed |
| `reference` | VARCHAR(255) | Yes | NULL | Reference number (SO, Invoice) |
| `notes` | TEXT | Yes | NULL | Transaction reason/notes |
| `transaction_date` | TIMESTAMP | No | - | When transaction occurred |
| `created_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Record creation time |
| `updated_at` | TIMESTAMP | No | CURRENT_TIMESTAMP | Last update time |

### Foreign Key

```sql
FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
```

When an item is deleted, all its transactions are deleted as well.

### Transaction Types

- `add` - Stock addition/receipt
- `deduct` - Stock removal/usage

### Example Records

```sql
INSERT INTO inventory_transactions 
  (item_id, transaction_type, quantity, reference, notes, transaction_date)
VALUES
  (1, 'add', 250, 'PO-001', 'Purchase order received', NOW()),
  (1, 'deduct', 50, 'SO-001', 'Sales order', NOW()),
  (2, 'add', 25, 'PO-002', 'Supplier shipment', NOW()),
  (3, 'deduct', 100, 'USAGE-001', 'Production assembly', NOW());
```

---

## Relationships

### Item → Transactions (One-to-Many)

One item can have many transactions.

```
items
  ├── id: 1
  ├── name: "Rice"
  └── transactions:
      ├── id: 1 (add 500 Kg)
      ├── id: 2 (deduct 50 Kg)
      └── id: 3 (deduct 75 Kg)
```

---

## Database Queries

### Get Item with Stock Status

```sql
SELECT 
  id,
  name,
  quantity,
  reorder_level,
  unit_type,
  CASE 
    WHEN quantity >= reorder_level THEN 'in_stock'
    WHEN quantity > 0 THEN 'low_stock'
    ELSE 'out_of_stock'
  END as stock_status
FROM items
WHERE deleted_at IS NULL;
```

### Get Low Stock Items

```sql
SELECT id, name, quantity, reorder_level, unit_type
FROM items
WHERE deleted_at IS NULL
  AND quantity < reorder_level
  AND quantity > 0
ORDER BY quantity ASC;
```

### Get Out of Stock Items

```sql
SELECT id, name, unit_type
FROM items
WHERE deleted_at IS NULL
  AND quantity = 0;
```

### Get Item Transaction History

```sql
SELECT 
  id,
  item_id,
  transaction_type,
  quantity,
  reference,
  notes,
  transaction_date,
  created_at
FROM inventory_transactions
WHERE item_id = ? 
ORDER BY transaction_date DESC;
```

### Calculate Stock Changes

```sql
SELECT 
  item_id,
  SUM(CASE WHEN transaction_type = 'add' THEN quantity ELSE 0 END) as total_added,
  SUM(CASE WHEN transaction_type = 'deduct' THEN quantity ELSE 0 END) as total_deducted,
  SUM(CASE WHEN transaction_type = 'add' THEN quantity ELSE -quantity END) as net_change
FROM inventory_transactions
WHERE item_id = ?
GROUP BY item_id;
```

### Get Recent Transactions (Last 10)

```sql
SELECT 
  t.id,
  t.item_id,
  i.name as item_name,
  t.transaction_type,
  t.quantity,
  t.reference,
  t.transaction_date
FROM inventory_transactions t
JOIN items i ON t.item_id = i.id
ORDER BY t.transaction_date DESC
LIMIT 10;
```

### Get Daily Stock Summary

```sql
SELECT 
  DATE(transaction_date) as date,
  SUM(CASE WHEN transaction_type = 'add' THEN quantity ELSE 0 END) as added,
  SUM(CASE WHEN transaction_type = 'deduct' THEN quantity ELSE 0 END) as deducted
FROM inventory_transactions
GROUP BY DATE(transaction_date)
ORDER BY date DESC;
```

---

## Indexes

For optimal performance, create these indexes:

```sql
-- Index on item_id in transactions
CREATE INDEX idx_transactions_item_id ON inventory_transactions(item_id);

-- Index on transaction_date for faster queries
CREATE INDEX idx_transactions_date ON inventory_transactions(transaction_date);

-- Index on transaction_type for filtering
CREATE INDEX idx_transactions_type ON inventory_transactions(transaction_type);

-- Index on deleted_at for soft deletes
CREATE INDEX idx_items_deleted_at ON items(deleted_at);
```

---

## Data Integrity Rules

### Quantity Rules

1. **Item Quantity** must always be >= 0
2. **Reorder Level** should be < average quantity
3. **Transaction Quantity** must be > 0

### Validation Rules

1. **Name** is required and unique
2. **Unit Type** must be one of: Kg, m, cm, No. of Units
3. **Transaction Type** must be: add or deduct
4. **Item ID** must exist in items table

### Cascade Rules

- **Delete Item** → All transactions deleted
- **Soft Delete Item** → Item hidden but transactions remain
- **Delete Transaction** → Update quantity accordingly (requires manual calculation)

---

## Backup & Recovery

### Backup SQLite Database

```bash
# Copy database file
cp database/database.sqlite database/database.sqlite.backup

# Or using sqlite3
sqlite3 database/database.sqlite ".dump" > database/backup.sql
```

### Backup MySQL Database

```bash
# Using mysqldump
mysqldump -u root -p inventory_management > backup.sql

# Restore from backup
mysql -u root -p inventory_management < backup.sql
```

### Export Data to CSV

```sql
-- MySQL
SELECT * INTO OUTFILE '/tmp/items.csv'
FIELDS TERMINATED BY ','
FROM items;

-- SQLite (from command line)
sqlite3 database/database.sqlite ".mode csv" ".output items.csv" "SELECT * FROM items;"
```

---

## Migration Files

### Create Items Table Migration

File: `database/migrations/2026_01_29_093140_create_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->enum('unit_type', ['Kg', 'm', 'cm', 'No. of Units']);
            $table->decimal('reorder_level', 10, 2)->default(10);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('items');
    }
};
```

### Create Transactions Table Migration

File: `database/migrations/2026_01_29_093206_create_inventory_transactions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->enum('transaction_type', ['add', 'deduct']);
            $table->decimal('quantity', 10, 2);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory_transactions');
    }
};
```

---

## Troubleshooting Database Issues

### Issue: Foreign Key Constraint Failed

**Cause:** Trying to insert item_id that doesn't exist

**Solution:**
```sql
-- Check if item exists
SELECT * FROM items WHERE id = ?;

-- Enable foreign key constraints (SQLite)
PRAGMA foreign_keys = ON;
```

### Issue: Duplicate Entry for Key 'name'

**Cause:** Item with same name already exists

**Solution:**
```sql
-- Check existing items
SELECT * FROM items WHERE name = 'Rice';

-- Use UPDATE instead of INSERT
UPDATE items SET quantity = 100 WHERE name = 'Rice';
```

### Issue: Decimal Precision Loss

**Cause:** Using DECIMAL(10,2) but quantity is too large

**Solution:** Increase precision to DECIMAL(12,2) or DECIMAL(15,2)

---

## Performance Tips

1. **Use pagination** for large result sets (>1000 records)
2. **Index frequently searched columns** (item_id, transaction_date)
3. **Archive old transactions** yearly
4. **Use soft deletes** for audit trail
5. **Avoid N+1 queries** - use eager loading
6. **Denormalize if needed** - cache stock_status in items table

---

## Data Export

### Export Items to CSV

```bash
php artisan tinker

# In tinker:
$items = App\Models\Item::all();
$items->each(fn($item) => echo "{$item->name},{$item->quantity},{$item->unit_type}\n");
```

### Export Transactions to JSON

```bash
php artisan tinker

# In tinker:
$transactions = App\Models\Transaction::with('item')->get();
echo json_encode($transactions);
```

