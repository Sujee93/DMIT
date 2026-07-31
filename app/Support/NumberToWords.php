<?php

namespace App\Support;

class NumberToWords
{
    private const ONES = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen',
    ];

    private const TENS = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety',
    ];

    /**
     * Render a Rupees/Cents amount in words, e.g. "Rupees Ninety Four Thousand Two Hundred
     * And Seventy Two And Cents Eighteen Only".
     */
    public static function rupees(float $amount): string
    {
        $rupees = (int) floor($amount);
        $cents = (int) round(($amount - $rupees) * 100);

        $words = 'Rupees '.($rupees > 0 ? self::convert($rupees) : 'Zero');

        if ($cents > 0) {
            $words .= ' And Cents '.self::convert($cents);
        }

        return $words.' Only';
    }

    public static function convert(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $parts = [];

        foreach ([
            1_000_000_000 => 'Billion',
            1_000_000 => 'Million',
            1_000 => 'Thousand',
        ] as $divisor => $label) {
            if ($number >= $divisor) {
                $parts[] = self::convert(intdiv($number, $divisor)).' '.$label;
                $number %= $divisor;
            }
        }

        if ($number >= 100) {
            $parts[] = self::ONES[intdiv($number, 100)].' Hundred';
            $number %= 100;
        }

        if ($number > 0) {
            if (count($parts) > 0) {
                $parts[] = 'And';
            }

            if ($number < 20) {
                $parts[] = self::ONES[$number];
            } else {
                $tens = self::TENS[intdiv($number, 10)];
                $ones = $number % 10;
                $parts[] = $ones > 0 ? $tens.'-'.self::ONES[$ones] : $tens;
            }
        }

        return implode(' ', $parts);
    }
}
