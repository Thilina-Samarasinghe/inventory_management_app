<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'transaction_type',
        'quantity',
        'previous_quantity',
        'new_quantity',
        'notes',
        'batch_id',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'previous_quantity' => 'decimal:2',
        'new_quantity' => 'decimal:2',
    ];

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeAdditions($query)
    {
        return $query->where('transaction_type', 'addition');
    }

    public function scopeDeductions($query)
    {
        return $query->where('transaction_type', 'deduction');
    }

    public function scopeByBatch($query, $batchId)
    {
        return $query->where('batch_id', $batchId);
    }

    // Helper methods
    public function isAddition(): bool
    {
        return $this->transaction_type === 'addition';
    }

    public function isDeduction(): bool
    {
        return $this->transaction_type === 'deduction';
    }
}