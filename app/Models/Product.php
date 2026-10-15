<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'color',
        'size',
        'cost',
        'price',
        'reorder_level',
        'is_active',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock' => 'integer',
        'reorder_level' => 'integer',
    ];

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function grnItems(): HasMany
    {
        return $this->hasMany(GrnItem::class);
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->stock > 0 && $this->reorder_level > 0 && $this->stock <= $this->reorder_level;
    }

    public function stockValue(): float
    {
        return max(0, $this->stock) * (float) $this->cost;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if ($term === null || $term === '') {
            return $query;
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('code', 'like', $like)
                ->orWhere('name', 'like', $like)
                ->orWhere('color', 'like', $like)
                ->orWhere('size', 'like', $like);
        });
    }

    /** Short variant label such as "Black / 42". */
    public function variant(): string
    {
        return implode(' / ', array_filter([$this->color, $this->size]));
    }
}
