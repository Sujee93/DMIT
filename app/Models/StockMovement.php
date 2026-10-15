<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One change to a product's stock. Positive quantity = stock in, negative = stock out.
 */
class StockMovement extends Model
{
    public const SOURCE_GRN = 'grn';

    public const SOURCE_INVOICE = 'invoice';

    public const SOURCE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'product_id',
        'source',
        'source_id',
        'reference',
        'movement_date',
        'quantity',
        'note',
        'created_by',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sourceLabel(): string
    {
        return match ($this->source) {
            self::SOURCE_GRN => 'GRN',
            self::SOURCE_INVOICE => 'Invoice',
            default => 'Adjustment',
        };
    }

    /**
     * Link to the document that caused the movement, if any.
     */
    public function sourceUrl(): ?string
    {
        return match (true) {
            $this->source === self::SOURCE_GRN && $this->source_id !== null => route('grns.show', $this->source_id),
            $this->source === self::SOURCE_INVOICE && $this->source_id !== null => route('invoices.show', $this->source_id),
            default => null,
        };
    }
}
