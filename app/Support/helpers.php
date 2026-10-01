<?php

use App\Models\BusinessSetting;
use App\Support\Money;

if (! function_exists('money')) {
    /**
     * Format an amount with the business currency symbol.
     */
    function money(string|int|float|null $amount, bool $withSymbol = true): string
    {
        return Money::format($amount, $withSymbol ? BusinessSetting::current()->currency_symbol : null);
    }
}
