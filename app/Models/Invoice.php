<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * Totals and status are calculated by InvoiceService and are therefore
     * never mass assigned from a request.
     */
    protected $fillable = [
        'customer_id',
        'supplier_id',
        'invoice_date',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'status' => InvoiceStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class)->latest('payment_date')->latest('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeOutstanding(Builder $query): Builder
    {
        return $query->where('status', '!=', InvoiceStatus::Paid->value);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->outstanding()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString());
    }

    public function balance(): string
    {
        return number_format((float) $this->total - (float) $this->amount_paid, 2, '.', '');
    }

    public function profit(): string
    {
        return number_format((float) $this->total - (float) $this->total_cost, 2, '.', '');
    }

    public function isOverdue(): bool
    {
        return $this->status !== InvoiceStatus::Paid
            && $this->due_date !== null
            && $this->due_date->lt(today());
    }

    public function hasPayments(): bool
    {
        return (float) $this->amount_paid > 0 || $this->payments()->exists();
    }
}
