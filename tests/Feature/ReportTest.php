<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_include_todays_and_overdue_invoices(): void
    {
        $this->actingAs($this->staff());
        $customer = Contact::factory()->customer()->create(['name' => 'Galle Shoes']);
        $supplier = Contact::factory()->supplier()->create(['name' => 'Lanka Mfg']);
        $product = Product::factory()->create(['code' => 'SN-77', 'cost' => '600', 'price' => '1000']);

        $invoice = fn (string $date, ?string $due) => $this->post('/invoices', [
            'customer_id' => $customer->id,
            'supplier_id' => $supplier->id,
            'invoice_date' => $date,
            'due_date' => $due,
            'items' => [['product_id' => $product->id, 'quantity' => 2, 'unit_price' => '1000']],
        ])->assertSessionHasNoErrors();

        $invoice(today()->toDateString(), today()->addDays(30)->toDateString());
        $invoice(today()->subDays(45)->toDateString(), today()->subDays(15)->toDateString());

        $sales = $this->get('/reports/sales?from='.today()->toDateString().'&to='.today()->toDateString());
        $sales->assertOk();
        $this->assertSame(2000.0, $sales->viewData('totals')['total']);
        $this->assertSame(800.0, $sales->viewData('totals')['profit']);
        $this->assertSame('SN-77', $sales->viewData('topProducts')->first()->product_code);

        $receivables = $this->get('/reports/receivables')->assertSee('Galle Shoes');
        $this->assertSame(4000.0, $receivables->viewData('total'));

        $payables = $this->get('/reports/payables')->assertSee('Lanka Mfg');
        $this->assertSame(2400.0, $payables->viewData('total'));

        $dues = $this->get('/reports/dues');
        $this->assertCount(1, $dues->viewData('invoices'));
        $this->assertSame(2000.0, $dues->viewData('buckets')['1-30']);

        $this->get('/reports/sales?from=not-a-date')->assertSessionHasErrors('from');
    }
}
