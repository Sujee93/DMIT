<?php

namespace App\Models;

use App\Enums\ContactType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A customer or a supplier.
 */
class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'company',
        'phone',
        'email',
        'address',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'type' => ContactType::class,
        'is_active' => 'boolean',
    ];

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'supplier_id');
    }

    public function customerPayments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class, 'customer_id');
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }

    public function scopeCustomers(Builder $query): Builder
    {
        return $query->where('type', ContactType::Customer);
    }

    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->where('type', ContactType::Supplier);
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
            $q->where('name', 'like', $like)
                ->orWhere('company', 'like', $like)
                ->orWhere('phone', 'like', $like)
                ->orWhere('email', 'like', $like);
        });
    }

    public function isCustomer(): bool
    {
        return $this->type === ContactType::Customer;
    }

    public function isSupplier(): bool
    {
        return $this->type === ContactType::Supplier;
    }

    public function displayName(): string
    {
        return $this->company && strcasecmp($this->company, $this->name) !== 0
            ? "{$this->name} ({$this->company})"
            : $this->name;
    }

    /**
     * Amount still owed: by a customer to us, or by us to a supplier.
     */
    public function balance(): string
    {
        if ($this->isCustomer()) {
            $invoiced = (float) $this->salesInvoices()->sum('total');
            $paid = (float) $this->salesInvoices()->sum('amount_paid');
        } else {
            $invoiced = (float) $this->purchaseInvoices()->sum('total_cost');
            $paid = (float) $this->supplierPayments()->sum('amount');
        }

        return number_format($invoiced - $paid, 2, '.', '');
    }
}
