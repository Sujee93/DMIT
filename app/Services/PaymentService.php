<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\SupplierPayment;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly InvoiceService $invoices) {}

    /**
     * Record money received from the customer against an invoice.
     *
     * @param  array<string, mixed>  $data  validated payment data
     */
    public function receive(Invoice $invoice, array $data, User $user): CustomerPayment
    {
        return DB::transaction(function () use ($invoice, $data, $user) {
            // Lock the invoice so two simultaneous payments cannot overpay it.
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);

            $amount = Money::toCents($data['amount']);
            $balance = Money::toCents($invoice->balance());

            if ($amount <= 0 || $amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'The amount must be between 0.01 and the outstanding balance of '.money($invoice->balance()).'.',
                ]);
            }

            $payment = new CustomerPayment($this->paymentAttributes($data));
            $payment->amount = Money::fromCents($amount);
            $payment->invoice_id = $invoice->id;
            $payment->customer_id = $invoice->customer_id;
            $payment->created_by = $user->id;
            $payment->save();

            $this->invoices->refreshPaymentStatus($invoice);

            return $payment;
        });
    }

    public function removeCustomerPayment(CustomerPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id);
            $payment->delete();
            $this->invoices->refreshPaymentStatus($invoice);
        });
    }

    /**
     * Record money paid out to a supplier.
     *
     * @param  array<string, mixed>  $data  validated payment data
     */
    public function paySupplier(Contact $supplier, array $data, User $user): SupplierPayment
    {
        if (! $supplier->isSupplier()) {
            throw ValidationException::withMessages(['supplier_id' => 'Please select a valid supplier.']);
        }

        $payment = new SupplierPayment($this->paymentAttributes($data));
        $payment->supplier_id = $supplier->id;
        $payment->amount = Money::fromCents(Money::toCents($data['amount']));
        $payment->created_by = $user->id;
        $payment->save();

        return $payment;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function paymentAttributes(array $data): array
    {
        return [
            'payment_date' => $data['payment_date'],
            'method' => $data['method'],
            'reference' => $data['reference'] ?? null,
            'bank' => $data['bank'] ?? null,
            'cheque_date' => $data['cheque_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }
}
