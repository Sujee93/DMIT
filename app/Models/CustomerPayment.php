<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerPayment extends Model
{
    protected $fillable = [
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

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

    public function cheque(): HasOne
    {
        return $this->hasOne(Cheque::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
