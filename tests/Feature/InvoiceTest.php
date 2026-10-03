<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    private Contact $customer;

    private Contact $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = Contact::factory()->customer()->create();
        $this->supplier = Contact::factory()->supplier()->create();
    }

    private function payload(array $items, array $overrides = []): array
    {
        return array_merge([
            'customer_id' => $this->customer->id,
            'supplier_id' => $this->supplier->id,
            'invoice_date' => today()->toDateString(),
            'due_date' => today()->addDays(30)->toDateString(),
            'discount' => '0',
            'items' => $items,
        ], $overrides);
    }

    public function test_invoice_totals_are_calculated_on_the_server(): void
    {
        $this->actingAs($this->staff());
        $a = Product::factory()->create(['cost' => '1000.00', 'price' => '1500.00']);
        $b = Product::factory()->create(['cost' => '200.10', 'price' => '300.20']);

        $this->post('/invoices', $this->payload([
            ['product_id' => $a->id, 'quantity' => 10, 'unit_price' => '1450.00'], // custom price
            ['product_id' => $b->id, 'quantity' => 3, 'unit_price' => '300.20', 'unit_cost' => '210.00'],
            // Anything the client sends for totals is ignored.
        ], ['discount' => '100.60', 'total' => '1', 'amount_paid' => '999999']))->assertRedirect();

        $invoice = Invoice::with('items')->firstOrFail();
        $this->assertSame('15400.60', $invoice->subtotal);  // 14500 + 900.60
        $this->assertSame('100.60', $invoice->discount);
        $this->assertSame('15300.00', $invoice->total);
        $this->assertSame('10630.00', $invoice->total_cost); // 10000 + 630
        $this->assertSame('0.00', $invoice->amount_paid);
        $this->assertSame(InvoiceStatus::Unpaid, $invoice->status);
        $this->assertSame('INV-00001', $invoice->invoice_no);
        $this->assertSame($a->code, $invoice->items[0]->product_code);
        $this->assertSame('1450.00', $invoice->items[0]->unit_price);
    }

    public function test_line_discount_amount_is_applied(): void
    {
        $this->actingAs($this->staff());
        $p = Product::factory()->create(['cost' => '1000', 'price' => '1450']);

        $this->post('/invoices', $this->payload([
            ['product_id' => $p->id, 'quantity' => 6, 'unit_price' => '1450', 'discount' => '870'],
        ]))->assertSessionHasNoErrors();

        $invoice = Invoice::with('items')->firstOrFail();
        $this->assertSame('870.00', $invoice->items[0]->discount);
        $this->assertSame('7830.00', $invoice->items[0]->line_total);
        $this->assertSame('7830.00', $invoice->total);

        $this->post('/invoices', $this->payload([
            ['product_id' => $p->id, 'quantity' => 2, 'unit_price' => '10', 'discount' => '20.01'],
        ]))->assertSessionHasErrors('items.0.discount');

        $this->post('/invoices', $this->payload([
            ['product_id' => $p->id, 'quantity' => 1, 'unit_price' => '10', 'discount' => '-1'],
        ]))->assertSessionHasErrors('items.0.discount');
    }

    public function test_invoice_numbers_are_sequential(): void
    {
        $this->actingAs($this->staff());
        $p = Product::factory()->create();

        $this->post('/invoices', $this->payload([['product_id' => $p->id, 'quantity' => 1, 'unit_price' => '10']]));
        $this->post('/invoices', $this->payload([['product_id' => $p->id, 'quantity' => 1, 'unit_price' => '10']]));

        $this->assertSame(['INV-00001', 'INV-00002'], Invoice::orderBy('id')->pluck('invoice_no')->all());
    }

    public function test_rejects_wrong_contact_types_and_bad_items(): void
    {
        $this->actingAs($this->staff());
        $p = Product::factory()->create();

        $this->post('/invoices', $this->payload(
            [['product_id' => 999, 'quantity' => 0, 'unit_price' => '-1']],
            ['customer_id' => $this->supplier->id, 'supplier_id' => $this->customer->id]
        ))->assertSessionHasErrors(['customer_id', 'supplier_id', 'items.0.product_id', 'items.0.quantity', 'items.0.unit_price']);

        $this->post('/invoices', $this->payload([]))->assertSessionHasErrors('items');

        $this->post('/invoices', $this->payload([['product_id' => $p->id, 'quantity' => 1, 'unit_price' => '10']], ['discount' => '50']))
            ->assertSessionHasErrors('discount');

        $this->assertSame(0, Invoice::count());
    }

    public function test_invoice_can_be_edited_until_paid(): void
    {
        $this->actingAs($this->admin());
        $p = Product::factory()->create();
        $this->post('/invoices', $this->payload([['product_id' => $p->id, 'quantity' => 2, 'unit_price' => '100']]));
        $invoice = Invoice::firstOrFail();

        $this->put("/invoices/{$invoice->id}", $this->payload([['product_id' => $p->id, 'quantity' => 5, 'unit_price' => '100']]))
            ->assertRedirect("/invoices/{$invoice->id}");
        $this->assertSame('500.00', $invoice->fresh()->total);
        $this->assertSame(1, $invoice->items()->count());

        $this->post("/invoices/{$invoice->id}/payments", ['payment_date' => today()->toDateString(), 'amount' => '100', 'method' => 'cash']);

        $this->get("/invoices/{$invoice->id}/edit")->assertRedirect("/invoices/{$invoice->id}");
        $this->put("/invoices/{$invoice->id}", $this->payload([['product_id' => $p->id, 'quantity' => 1, 'unit_price' => '1']]))
            ->assertSessionHasErrors('invoice');
        $this->delete("/invoices/{$invoice->id}")->assertSessionHasErrors('invoice');
        $this->assertSame('500.00', $invoice->fresh()->total);
    }

    public function test_print_view_hides_cost_information(): void
    {
        $this->actingAs($this->staff());
        $p = Product::factory()->create(['cost' => '777.00', 'price' => '999.00']);
        $this->post('/invoices', $this->payload([['product_id' => $p->id, 'quantity' => 1, 'unit_price' => '999']]));
        $invoice = Invoice::firstOrFail();

        $this->get("/invoices/{$invoice->id}/print")
            ->assertOk()
            ->assertSee('SALES INVOICE')
            ->assertSee('Net Invoice Value')
            ->assertSee('Total Package')
            ->assertDontSee('Roll')
            ->assertDontSee('Sales Rep')
            ->assertSee('999.00')
            ->assertDontSee('777.00')
            ->assertDontSee($this->supplier->name);
    }
}
