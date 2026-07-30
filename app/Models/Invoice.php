<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'invoice_type', 'invoice_number', 'reference_number',
    'customer_id', 'customer_name', 'customer_address', 'customer_tin', 'customer_telephone',
    'date_of_invoice', 'date_of_supply', 'place_of_supply',
    'vehicle_no', 'sup_no',
    'subtotal', 'vat_rate', 'vat_amount', 'total_amount', 'advance', 'balance',
    'amount_in_words', 'mode_of_payment',
    'prepared_by', 'checked_by', 'status',
])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return [
            'date_of_invoice' => 'date',
            'date_of_supply' => 'date',
            'subtotal' => 'decimal:2',
            'vat_rate' => 'decimal:2',
            'vat_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'advance' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function isTaxInvoice(): bool
    {
        return $this->invoice_type === 'tax';
    }
}
