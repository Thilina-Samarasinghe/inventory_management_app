# Inventory Management System - Local Setup Guide

## Prerequisites

Before starting, ensure you have the following installed on your system:

- **PHP** (8.1 or higher) - [Download](https://www.php.net/downloads)
- **Composer** (2.0 or higher) - [Download](https://getcomposer.org/download/)
- **Node.js** (16.0 or higher) - [Download](https://nodejs.org/)
- **npm** (8.0 or higher) - Usually comes with Node.js
- **SQLite** or **MySQL** (optional, SQLite comes with PHP)
- **Git** - [Download](https://git-scm.com/)

### Verify Installation

```bash
php --version
composer --version
node --version
npm --version
```

---

## Step 1: Clone the Repository

```bash
# Clone the project
git clone https://github.com/Thilina-Samarasinghe/inventory_management_app.git

# Navigate to project directory
cd inventory_management_app
```

---

## Step 2: Install PHP Dependencies

```bash
# Install Laravel dependencies using Composer
composer install
```

This will install all PHP packages including Laravel, database drivers, and other dependencies.

---

## Step 3: Install Node.js Dependencies

```bash
# Install JavaScript dependencies
npm install
```

This installs Vue 3, Tailwind CSS, Vite, and other frontend dependencies.

---

## Step 4: Environment Configuration

### Create .env File

```bash
# Copy the example environment file
cp .env.example .env
```

### Edit .env File

Open `.env` and configure the following:

```env
APP_NAME="Inventory Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=sqlite
# OR use MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=inventory_management
# DB_USERNAME=root
# DB_PASSWORD=

LOG_CHANNEL=stack
LOG_LEVEL=debug

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Generate Application Key

```bash
php artisan key:generate
```

---

## Step 5: Database Setup

### Option A: Using SQLite (Recommended for Development)

SQLite doesn't require additional installation. The database file will be created automatically.

```bash
# Create the SQLite database file
touch database/database.sqlite
```

### Option B: Using MySQL

1. **Create Database**:
   ```bash
   # Open MySQL command line
   mysql -u root -p
   
   # Create database
   CREATE DATABASE inventory_management;
   EXIT;
   ```

2. **Update .env**:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=inventory_management
   DB_USERNAME=root
   DB_PASSWORD=yourpassword
   ```

---

## Step 6: Run Database Migrations

Create the database tables:

```bash
php artisan migrate
```

This will create two main tables:

### Table 1: `items`
```sql
CREATE TABLE items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    quantity DECIMAL(10, 2) NOT NULL,
    unit_type ENUM('Kg', 'm', 'cm', 'No. of Units') NOT NULL,
    reorder_level DECIMAL(10, 2) DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

**Fields:**
- `id` - Unique item identifier
- `name` - Item name (required)
- `description` - Item description (optional)
- `quantity` - Current stock quantity
- `unit_type` - Measurement unit
- `reorder_level` - Stock alert threshold
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

### Table 2: `inventory_transactions`
```sql
CREATE TABLE inventory_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    item_id BIGINT NOT NULL,
    transaction_type ENUM('add', 'deduct') NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    reference VARCHAR(255),
    notes TEXT,
    transaction_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);
```

**Fields:**
- `id` - Unique transaction identifier
- `item_id` - Reference to items table
- `transaction_type` - 'add' or 'deduct'
- `quantity` - Amount of transaction
- `reference` - Reference number (e.g., invoice, SO)
- `notes` - Transaction notes/reason
- `transaction_date` - When transaction occurred
- `created_at` - Record creation time
- `updated_at` - Record update time

---

## Step 7: Seed Sample Data (Optional)

To populate the database with sample data:

```bash
php artisan db:seed
```

Or create a seeder manually. Add sample items to `database/seeders/ItemSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('items')->insert([
            [
                'name' => 'Rice',
                'description' => 'Premium quality basmati rice',
                'quantity' => 500,
                'unit_type' => 'Kg',
                'reorder_level' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Steel Rod',
                'description' => 'High tensile steel rod',
                'quantity' => 50,
                'unit_type' => 'm',
                'reorder_level' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bolts',
                'description' => 'M8 stainless steel bolts',
                'quantity' => 1000,
                'unit_type' => 'No. of Units',
                'reorder_level' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Copper Wire',
                'description' => '2mm copper wire coil',
                'quantity' => 100,
                'unit_type' => 'm',
                'reorder_level' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=ItemSeeder
```

---

## Step 8: Build Frontend Assets

Compile Vue 3 and Tailwind CSS:

```bash
# Development build
npm run dev

# Production build
npm run build
```

---

## Step 9: Start Development Servers

### Terminal 1: Laravel Development Server

```bash
php artisan serve
```

This starts the Laravel server at `http://localhost:8000`

### Terminal 2: Vite Development Server (For Hot Module Replacement)

```bash
npm run dev
```

This starts the Vite server with hot reload capabilities

---

## Step 10: Access the Application

Open your browser and navigate to:

```
http://localhost:8000
```

---

## Project Structure

```
inventory_management_app/
├── app/                          # Laravel application code
│   ├── Http/Controllers/        # Request handlers
│   ├── Models/                  # Database models
│   └── ...
├── database/
│   ├── migrations/              # Database schema
│   ├── seeders/                 # Sample data seeders
│   └── database.sqlite          # SQLite database file
├── resources/
│   ├── js/
│   │   ├── app.ts              # Vue app entry point
│   │   ├── pages/              # Page components
│   │   │   ├── Dashboard.vue
│   │   │   ├── Items/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Create.vue
│   │   │   │   ├── Deduct.vue
│   │   │   │   └── History.vue
│   │   ├── components/         # Reusable components
│   │   ├── composables/        # Vue composables
│   │   │   └── useInventory.ts # Inventory state management
│   │   ├── types/              # TypeScript types
│   │   └── css/                # Stylesheets
│   └── views/
│       └── app.blade.php       # Main HTML template
├── routes/
│   ├── web.php                 # Web routes
│   └── api.php                 # API routes
├── public/                      # Public assets
├── .env                        # Environment configuration
├── composer.json               # PHP dependencies
├── package.json                # Node dependencies
├── vite.config.ts              # Vite configuration
└── tsconfig.json               # TypeScript configuration
```

---

## Core Features

### 1. Dashboard
- View inventory summary
- See low stock items
- View recent transactions

### 2. Add Items
- Add single or multiple items
- Specify unit type (Kg, m, cm, No. of Units)
- Set reorder level

### 3. Deduct Items
- Remove stock from items
- Add reference number and notes
- Bulk deduct from multiple items

### 4. Items List
- View all inventory items
- Search by item name
- Filter by unit type
- View item details

### 5. Item History
- Complete transaction history
- View all additions and deductions
- Track changes over time

---

## API Endpoints (If Using API)

```
GET    /api/items              - List all items
POST   /api/items              - Create new item
GET    /api/items/{id}         - Get item details
PUT    /api/items/{id}         - Update item
DELETE /api/items/{id}         - Delete item

GET    /api/items/{id}/history - Get item transactions
POST   /api/transactions       - Create transaction
GET    /api/transactions       - List transactions
```

---

## Troubleshooting

### Issue: Database connection error
**Solution:**
- For SQLite: Ensure `database/database.sqlite` exists
- For MySQL: Verify database credentials in `.env`
- Run migrations: `php artisan migrate`

### Issue: Node modules not installed
**Solution:**
```bash
rm -rf node_modules package-lock.json
npm install
```

### Issue: Permission denied on database file
**Solution:**
```bash
chmod 644 database/database.sqlite
chmod 755 database/
```

### Issue: Port 8000 already in use
**Solution:**
```bash
php artisan serve --port=8001
```

### Issue: Vite hot reload not working
**Solution:**
- Ensure `npm run dev` is running in separate terminal
- Check that port 5173 is not blocked
- Clear browser cache (Ctrl+Shift+Delete)

---

## Development Commands

```bash
# Run tests
php artisan test

# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Run queue jobs
php artisan queue:work

# Create new model with migration
php artisan make:model Item -m

# Create new controller
php artisan make:controller ItemController

# Format code
npm run format

# Lint code
npm run lint
```

---

## Production Deployment

For production deployment:

1. Set `APP_DEBUG=false` in `.env`
2. Set `APP_ENV=production` in `.env`
3. Run migrations: `php artisan migrate --force`
4. Build frontend: `npm run build`
5. Serve with production server (Nginx, Apache)
6. Set proper file permissions
7. Configure SSL certificate
8. Use environment variables for sensitive data

---

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review Laravel documentation: https://laravel.com/docs
3. Review Vue 3 documentation: https://vuejs.org
4. Check GitHub issues: https://github.com/Thilina-Samarasinghe/inventory_management_app/issues

---

## License

This project is open source and available under the MIT License.

