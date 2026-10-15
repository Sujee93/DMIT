<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Goods Received Note: stock received from a supplier against their bill.
 */
class Grn extends Model
{
    /**
     * Totals are calculated by GrnService and never mass assigned.
     */
    protected $fillable = [
        'supplier_id',
        'grn_date',
        'supplier_ref',
        'notes',
    ];

    protected $casts = [
        'grn_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GrnItem::class)->orderBy('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
