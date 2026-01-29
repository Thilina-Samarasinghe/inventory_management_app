<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'unit_type',
        'current_quantity',
        'minimum_quantity',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
    ];

    protected $appends = ['stock_status'];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        if (!empty($search)) {
            return $query->where('name', 'like', "%{$search}%");
        }
        return $query;
    }

    public function scopeByUnitType($query, $unitType)
    {
        if (!empty($unitType)) {
            return $query->where('unit_type', $unitType);
        }
        return $query;
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_quantity', '<=', 'minimum_quantity')
                    ->whereNotNull('minimum_quantity');
    }

    // Accessors
    public function getStockStatusAttribute(): string
    {
        if ($this->current_quantity <= 0) {
            return 'out_of_stock';
        }
        if ($this->minimum_quantity && $this->current_quantity <= $this->minimum_quantity) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    // Helper methods
    public function isLowStock(): bool
    {
        return $this->stock_status === 'low_stock';
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_status === 'out_of_stock';
    }
}