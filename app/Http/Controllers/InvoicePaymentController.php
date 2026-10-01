<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;

/**
 * Payments received from customers against an invoice.
 */
class InvoicePaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function store(PaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        $payment = $this->payments->receive($invoice, $request->validated(), $request->user());

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Payment of '.money($payment->amount).' recorded.');
    }

    public function destroy(Invoice $invoice, CustomerPayment $payment): RedirectResponse
    {
        $this->authorize('admin');

        abort_unless((int) $payment->invoice_id === (int) $invoice->id, 404);

        $this->payments->removeCustomerPayment($payment);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment removed.');
    }
}
