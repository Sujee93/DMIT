<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_type', 'invoice_number', 'reference_number',
        'customer_id', 'customer_name', 'customer_address', 'customer_tin', 'customer_telephone',
        'date_of_invoice', 'date_of_supply', 'place_of_supply',
        'vehicle_no', 'sup_no',
        'subtotal', 'vat_rate', 'vat_amount', 'total_amount', 'advance', 'balance',
        'amount_in_words', 'mode_of_payment',
        'prepared_by', 'checked_by', 'status',
    ];

    protected $casts = [
        'date_of_invoice' => 'date',
        'date_of_supply' => 'date',
        'subtotal' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

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

    /**
     * Next zero-padded sequential number, e.g. "001", "002". Tax invoices are numbered
     * per customer (Singer and Arpico each have their own 1, 2, 3… sequence); general
     * invoices share one sequence (customerId is null).
     */
    public static function nextInvoiceNumber(string $invoiceType, ?int $customerId = null): string
    {
        $max = static::where('invoice_type', $invoiceType)
            ->where('customer_id', $customerId)
            ->pluck('invoice_number')
            ->map(fn ($number) => (int) $number)
            ->max() ?? 0;

        return str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Reference number printed as "Tax Invoice No." e.g. 2026_08_KATMO_SIN_001 / 2026_08_KATMO_ARP_008.
     */
    public static function referenceNumberFor(string $customerKey, string $invoiceNumber, $invoiceDate = null): ?string
    {
        $codes = ['singer' => 'SIN', 'arpico' => 'ARP', 'ramadia' => 'RAM'];

        if (! isset($codes[$customerKey])) {
            return null;
        }

        $date = $invoiceDate instanceof \Carbon\Carbon
            ? $invoiceDate
            : \Carbon\Carbon::parse($invoiceDate ?? now());

        return $date->format('Y').'_'.$date->format('m').'_KATMO_'.$codes[$customerKey].'_'.$invoiceNumber;
    }
}
