<?php

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_converts_to_and_from_cents_without_float_drift(): void
    {
        $this->assertSame(1999, Money::toCents('19.99'));
        $this->assertSame(30, Money::toCents(0.1 + 0.2));
        $this->assertSame('0.30', Money::fromCents(30));
        $this->assertSame('1234567.89', Money::fromCents(123456789));
    }

    public function test_formats_with_symbol(): void
    {
        $this->assertSame('Rs. 1,250.50', Money::format('1250.5', 'Rs.'));
        $this->assertSame('1,250.50', Money::format(1250.5));
    }
}
