<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierPayment extends Model
{
    protected $fillable = [
        'supplier_id',
        'payment_date',
        'amount',
        'method',
        'reference',
        'bank',
        'cheque_date',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
        'method' => PaymentMethod::class,
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    /** Customer cheques handed to the supplier as this payment. */
    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
