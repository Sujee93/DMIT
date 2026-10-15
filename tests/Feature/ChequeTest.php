<?php

namespace Tests\Feature;

use App\Enums\ChequeStatus;
use App\Enums\InvoiceStatus;
use App\Models\Cheque;
use App\Models\Contact;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\SupplierPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChequeTest extends TestCase
{
    use RefreshDatabase;

    private Contact $customer;

    private Contact $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->admin());
        $this->customer = Contact::factory()->customer()->create();
        $this->supplier = Contact::factory()->supplier()->create();
    }

    private function invoiceFor(string $amount): Invoice
    {
        $this->post('/invoices', [
            'customer_id' => $this->customer->id,
            'invoice_date' => today()->toDateString(),
            'items' => [['product_id' => Product::factory()->create()->id, 'quantity' => 1, 'unit_price' => $amount]],
        ])->assertSessionHasNoErrors();

        return Invoice::latest('id')->firstOrFail();
    }

    private function payByCheque(Invoice $invoice, string $amount, string $no, ?string $date = null)
    {
        return $this->post("/invoices/{$invoice->id}/payments", [
            'payment_date' => today()->toDateString(), 'amount' => $amount, 'method' => 'cheque',
            'reference' => $no, 'bank' => 'BOC', 'cheque_date' => $date ?? today()->toDateString(),
        ]);
    }

    private function payWithCheques(array $ids)
    {
        return $this->post('/supplier-payments', [
            'supplier_id' => $this->supplier->id, 'payment_date' => today()->toDateString(),
            'method' => 'customer_cheque', 'cheque_ids' => $ids,
        ]);
    }

    public function test_customer_cheque_payment_is_added_to_register(): void
    {
        $invoice = $this->invoiceFor('5000');
        $this->payByCheque($invoice, '5000', 'CHQ-100')->assertSessionHasNoErrors();

        $cheque = Cheque::firstOrFail();
        $this->assertSame('CHQ-100', $cheque->cheque_no);
        $this->assertSame(ChequeStatus::InHand, $cheque->status);
        $this->assertSame($this->customer->id, $cheque->customer_id);
        $this->assertSame(InvoiceStatus::Paid, $invoice->fresh()->status);

        $this->get('/cheques')->assertOk()->assertSee('CHQ-100');
    }

    public function test_cheque_payment_needs_number_and_date(): void
    {
        $invoice = $this->invoiceFor('100');
        $this->post("/invoices/{$invoice->id}/payments", ['payment_date' => today()->toDateString(), 'amount' => '100', 'method' => 'cheque'])
            ->assertSessionHasErrors(['reference', 'cheque_date']);

        // Customers cannot "pay" with the supplier-only method.
        $this->post("/invoices/{$invoice->id}/payments", ['payment_date' => today()->toDateString(), 'amount' => '100', 'method' => 'customer_cheque'])
            ->assertSessionHasErrors('method');
    }

    public function test_supplier_can_be_paid_with_customer_cheques(): void
    {
        $this->receive($this->supplier, [[Product::factory()->create(), 10, '1000']]); // owe 10,000
        $this->payByCheque($this->invoiceFor('4000'), '4000', 'CHQ-1');
        $this->payByCheque($this->invoiceFor('2500'), '2500', 'CHQ-2');
        $ids = Cheque::pluck('id')->all();

        $this->payWithCheques($ids)->assertRedirect("/contacts/{$this->supplier->id}");

        $payment = SupplierPayment::firstOrFail();
        $this->assertSame('6500.00', $payment->amount);
        $this->assertSame('3500.00', $this->supplier->balance());
        $this->assertSame(2, Cheque::where('status', 'endorsed')->where('supplier_payment_id', $payment->id)->count());

        // A cheque cannot be given away twice.
        $this->payWithCheques([$ids[0]])->assertSessionHasErrors('cheque_ids');
        $this->assertSame(1, SupplierPayment::count());
    }

    public function test_expired_cheques_cannot_be_used(): void
    {
        $this->payByCheque($this->invoiceFor('1000'), '1000', 'OLD-1', today()->subMonths(7)->toDateString());
        $cheque = Cheque::firstOrFail();
        $this->assertTrue($cheque->isExpired());

        $this->payWithCheques([$cheque->id])->assertSessionHasErrors('cheque_ids');
        $this->assertSame(0, SupplierPayment::count());
        $this->get('/cheques?status=expired')->assertSee('OLD-1');
    }

    public function test_deleting_supplier_payment_puts_cheques_back_in_hand(): void
    {
        $this->payByCheque($this->invoiceFor('1000'), '1000', 'CHQ-5');
        $cheque = Cheque::firstOrFail();
        $this->payWithCheques([$cheque->id]);
        $payment = SupplierPayment::firstOrFail();

        $this->delete("/supplier-payments/{$payment->id}")->assertRedirect();

        $cheque->refresh();
        $this->assertSame(ChequeStatus::InHand, $cheque->status);
        $this->assertNull($cheque->supplier_payment_id);
    }

    public function test_customer_payment_with_endorsed_cheque_cannot_be_removed(): void
    {
        $invoice = $this->invoiceFor('1000');
        $this->payByCheque($invoice, '1000', 'CHQ-7');
        $this->payWithCheques([Cheque::firstOrFail()->id]);
        $payment = CustomerPayment::firstOrFail();

        $this->delete("/invoices/{$invoice->id}/payments/{$payment->id}")->assertSessionHasErrors('payment');
        $this->assertModelExists($payment);
    }

    public function test_returned_cheque_reverses_the_customer_payment(): void
    {
        $invoice = $this->invoiceFor('3000');
        $this->payByCheque($invoice, '3000', 'BOUNCE-1');
        $cheque = Cheque::firstOrFail();

        $this->post("/cheques/{$cheque->id}/return", ['returned_on' => today()->toDateString(), 'notes' => 'Insufficient funds'])
            ->assertSessionHasNoErrors();

        $cheque->refresh();
        $this->assertSame(ChequeStatus::Returned, $cheque->status);
        $this->assertSame('Insufficient funds', $cheque->notes);
        $this->assertSame(0, CustomerPayment::count());
        $this->assertSame(InvoiceStatus::Unpaid, $invoice->fresh()->status);
        $this->assertSame('3000.00', $invoice->fresh()->balance());
    }

    public function test_endorsed_cheque_cannot_be_marked_returned_until_payment_removed(): void
    {
        $this->payByCheque($this->invoiceFor('500'), '500', 'CHQ-8');
        $cheque = Cheque::firstOrFail();
        $this->payWithCheques([$cheque->id]);

        $this->post("/cheques/{$cheque->id}/return", ['returned_on' => today()->toDateString()])->assertSessionHasErrors('cheque');
        $this->assertSame(ChequeStatus::Endorsed, $cheque->fresh()->status);
    }

    public function test_deposit_and_undo(): void
    {
        $this->payByCheque($this->invoiceFor('500'), '500', 'CHQ-9');
        $cheque = Cheque::firstOrFail();

        $this->post("/cheques/{$cheque->id}/deposit", ['deposited_on' => today()->toDateString()])->assertSessionHasNoErrors();
        $this->assertSame(ChequeStatus::Deposited, $cheque->fresh()->status);

        // Deposited cheques are no longer available for suppliers.
        $this->payWithCheques([$cheque->id])->assertSessionHasErrors('cheque_ids');

        $this->post("/cheques/{$cheque->id}/undo-deposit")->assertSessionHasNoErrors();
        $this->assertSame(ChequeStatus::InHand, $cheque->fresh()->status);
    }

    public function test_staff_cannot_mark_cheques_returned(): void
    {
        $this->payByCheque($this->invoiceFor('500'), '500', 'CHQ-10');
        $cheque = Cheque::firstOrFail();

        $this->actingAs($this->staff())
            ->post("/cheques/{$cheque->id}/return", ['returned_on' => today()->toDateString()])
            ->assertForbidden();
    }
}
