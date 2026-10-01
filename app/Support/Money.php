<?php

namespace App\Support;

/**
 * Money maths is done in integer cents to avoid floating point drift,
 * then converted back to 2-decimal strings for storage.
 */
final class Money
{
    public static function toCents(string|int|float|null $amount): int
    {
        return (int) round(((float) ($amount ?? 0)) * 100);
    }

    public static function fromCents(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    public static function format(string|int|float|null $amount, ?string $symbol = null): string
    {
        $formatted = number_format((float) ($amount ?? 0), 2);

        return $symbol === null || $symbol === '' ? $formatted : $symbol.' '.$formatted;
    }
}
