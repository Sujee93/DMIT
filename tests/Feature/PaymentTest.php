<?php

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Models\Contact;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\SupplierPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private Invoice $invoice;

    private Contact $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->admin());

        $customer = Contact::factory()->customer()->create();
        $this->supplier = Contact::factory()->supplier()->create();
        $product = Product::factory()->create(['cost' => '600', 'price' => '1000']);

        $this->post('/invoices', [
            'customer_id' => $customer->id,
            'supplier_id' => $this->supplier->id,
            'invoice_date' => today()->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => 1, 'unit_price' => '1000']],
        ]);
        $this->invoice = Invoice::firstOrFail();
    }

    private function pay(array $data)
    {
        return $this->post("/invoices/{$this->invoice->id}/payments", array_merge([
            'payment_date' => today()->toDateString(), 'method' => 'cash',
        ], $data));
    }

    public function test_partial_then_full_payment_updates_status(): void
    {
        $this->pay(['amount' => '400'])->assertRedirect("/invoices/{$this->invoice->id}");
        $this->invoice->refresh();
        $this->assertSame(InvoiceStatus::Partial, $this->invoice->status);
        $this->assertSame('600.00', $this->invoice->balance());

        $this->pay(['amount' => '600', 'method' => 'cheque', 'reference' => 'CHQ-001', 'bank' => 'BOC', 'cheque_date' => today()->toDateString()])
            ->assertSessionHasNoErrors();
        $this->invoice->refresh();
        $this->assertSame(InvoiceStatus::Paid, $this->invoice->status);
        $this->assertSame('0.00', $this->invoice->balance());
        $this->assertDatabaseHas('customer_payments', ['reference' => 'CHQ-001', 'method' => 'cheque']);
    }

    public function test_overpayment_and_invalid_payments_are_rejected(): void
    {
        $this->pay(['amount' => '1000.01'])->assertSessionHasErrors('amount');
        $this->pay(['amount' => '0'])->assertSessionHasErrors('amount');
        $this->pay(['amount' => '10', 'method' => 'bitcoin'])->assertSessionHasErrors('method');
        $this->pay(['amount' => '10', 'method' => 'cheque'])->assertSessionHasErrors('reference');

        $this->assertSame(0, CustomerPayment::count());
    }

    public function test_removing_a_payment_restores_the_balance(): void
    {
        $this->pay(['amount' => '1000']);
        $payment = CustomerPayment::firstOrFail();

        $this->delete("/invoices/{$this->invoice->id}/payments/{$payment->id}")->assertRedirect();

        $this->invoice->refresh();
        $this->assertSame(InvoiceStatus::Unpaid, $this->invoice->status);
        $this->assertSame('0.00', $this->invoice->amount_paid);
    }

    public function test_payment_cannot_be_removed_through_another_invoice(): void
    {
        $this->pay(['amount' => '100']);
        $payment = CustomerPayment::firstOrFail();
        $otherInvoice = $this->invoice->replicate(['invoice_no']);
        $otherInvoice->invoice_no = 'X-1';
        $otherInvoice->save();

        $this->delete("/invoices/{$otherInvoice->id}/payments/{$payment->id}")->assertNotFound();
        $this->assertModelExists($payment);
    }

    public function test_staff_cannot_remove_payments(): void
    {
        $this->pay(['amount' => '100']);
        $payment = CustomerPayment::firstOrFail();

        $this->actingAs($this->staff())
            ->delete("/invoices/{$this->invoice->id}/payments/{$payment->id}")
            ->assertForbidden();
    }

    public function test_supplier_payment_reduces_payable(): void
    {
        $this->assertSame('600.00', $this->supplier->balance());

        $this->post('/supplier-payments', [
            'supplier_id' => $this->supplier->id,
            'payment_date' => today()->toDateString(),
            'amount' => '250',
            'method' => 'bank_transfer',
            'reference' => 'TRX-9',
        ])->assertRedirect("/contacts/{$this->supplier->id}");

        $this->assertSame('350.00', $this->supplier->balance());
        $this->assertSame(1, SupplierPayment::count());

        $this->get('/reports/payables')->assertSee('350.00');
    }

    public function test_supplier_payment_requires_a_supplier(): void
    {
        $customer = Contact::factory()->customer()->create();

        $this->post('/supplier-payments', [
            'supplier_id' => $customer->id, 'payment_date' => today()->toDateString(), 'amount' => '10', 'method' => 'cash',
        ])->assertSessionHasErrors('supplier_id');
    }
}
