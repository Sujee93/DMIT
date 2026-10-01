<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Single-row table holding the business profile shown on invoices and in the UI.
 */
class BusinessSetting extends Model
{
    private const CACHE_KEY = 'business_settings';

    protected $fillable = [
        'name',
        'tagline',
        'address',
        'phone',
        'mobile',
        'email',
        'website',
        'registration_no',
        'tax_no',
        'currency_symbol',
        'invoice_prefix',
        'default_due_days',
        'bank_details',
        'invoice_terms',
        'invoice_footer',
    ];

    protected $casts = [
        'default_due_days' => 'integer',
        'invoice_next_number' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget(self::CACHE_KEY);
            app()->forgetInstance(self::class.'@current');
        });
    }

    /**
     * The business profile, resolved once per request and cached between requests.
     */
    public static function current(): self
    {
        return app(self::class.'@current');
    }

    /**
     * Load the settings row (creating it on first use). Bound as a singleton in AppServiceProvider.
     */
    public static function resolve(): self
    {
        if (! Schema::hasTable('business_settings')) {
            return new self(self::defaults());
        }

        return Cache::rememberForever(
            self::CACHE_KEY,
            fn () => self::query()->first() ?? self::query()->create(self::defaults())
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'name' => config('app.name'),
            'currency_symbol' => 'Rs.',
            'invoice_prefix' => 'INV-',
            'default_due_days' => 30,
        ];
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }
}
