<?php

namespace App\Models;

use App\Enums\ChequeStatus;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A cheque received from a customer.
 */
class Cheque extends Model
{
    /**
     * Status and links are changed only by PaymentService / ChequeService.
     */
    protected $fillable = [
        'cheque_no',
        'bank',
        'cheque_date',
        'amount',
        'notes',
    ];

    protected $casts = [
        'cheque_date' => 'date',
        'deposited_on' => 'date',
        'returned_on' => 'date',
        'amount' => 'decimal:2',
        'status' => ChequeStatus::class,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customerPayment(): BelongsTo
    {
        return $this->belongsTo(CustomerPayment::class);
    }

    public function supplierPayment(): BelongsTo
    {
        return $this->belongsTo(SupplierPayment::class);
    }

    public static function validMonths(): int
    {
        return max(1, (int) config('distribution.cheque_valid_months', 6));
    }

    /**
     * Last day the cheque can be presented (cheque date + validity period).
     */
    public function expiresOn(): CarbonInterface
    {
        return $this->cheque_date->copy()->addMonthsNoOverflow(self::validMonths());
    }

    public function isExpired(): bool
    {
        return $this->expiresOn()->lt(today());
    }

    public function isExpiringSoon(): bool
    {
        return ! $this->isExpired()
            && $this->expiresOn()->lte(today()->addDays((int) config('distribution.cheque_expiry_warning_days', 14)));
    }

    /** Cheques still with us and still valid: these can be passed to a supplier. */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', ChequeStatus::InHand)
            ->whereDate('cheque_date', '>=', today()->subMonthsNoOverflow(self::validMonths())->toDateString());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', ChequeStatus::InHand)
            ->whereDate('cheque_date', '<', today()->subMonthsNoOverflow(self::validMonths())->toDateString());
    }

    public function statusLabel(): string
    {
        return $this->status === ChequeStatus::InHand && $this->isExpired() ? 'Expired' : $this->status->label();
    }

    public function statusTone(): string
    {
        return $this->status === ChequeStatus::InHand && $this->isExpired() ? 'danger' : $this->status->tone();
    }
}
