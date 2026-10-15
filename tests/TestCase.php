<?php

namespace Tests;

use App\Models\Contact;
use App\Models\Grn;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    protected function staff(): User
    {
        return User::factory()->create();
    }

    /**
     * Receive stock from a supplier through the real GRN screen.
     *
     * @param  array<int, array{0: Product, 1: int, 2: string|int|float}>  $lines  [product, qty, unit cost]
     */
    protected function receive(Contact $supplier, array $lines, array $overrides = []): Grn
    {
        $this->post('/grns', array_merge([
            'supplier_id' => $supplier->id,
            'grn_date' => today()->toDateString(),
            'supplier_ref' => 'BILL-'.random_int(100, 999),
            'items' => array_map(fn ($line) => [
                'product_id' => $line[0]->id, 'quantity' => $line[1], 'unit_cost' => (string) $line[2],
            ], $lines),
        ], $overrides))->assertSessionHasNoErrors();

        return Grn::query()->latest('id')->firstOrFail();
    }
}
