<?php

namespace App\Services;

use App\Enums\ChequeStatus;
use App\Enums\PaymentMethod;
use App\Models\Cheque;
use App\Models\Contact;
use App\Models\CustomerPayment;
use App\Models\Invoice;
use App\Models\SupplierPayment;
use App\Models\User;
use App\Support\Money;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly InvoiceService $invoices) {}

    /**
     * Record money received from the customer against an invoice.
     * A cheque payment is also added to the cheque register.
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

            if ($payment->method === PaymentMethod::Cheque) {
                $cheque = new Cheque([
                    'cheque_no' => $payment->reference,
                    'bank' => $payment->bank,
                    'cheque_date' => $payment->cheque_date,
                    'amount' => $payment->amount,
                ]);
                $cheque->customer_payment_id = $payment->id;
                $cheque->customer_id = $invoice->customer_id;
                $cheque->invoice_id = $invoice->id;
                $cheque->status = ChequeStatus::InHand;
                $cheque->save();
            }

            $this->invoices->refreshPaymentStatus($invoice);

            return $payment;
        });
    }

    public function removeCustomerPayment(CustomerPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id);

            $cheque = Cheque::query()->where('customer_payment_id', $payment->id)->lockForUpdate()->first();
            if ($cheque && $cheque->status === ChequeStatus::Endorsed) {
                throw ValidationException::withMessages([
                    'payment' => "Cheque {$cheque->cheque_no} has been given to a supplier. Remove that supplier payment first.",
                ]);
            }
            $cheque?->delete();

            $payment->delete();
            $this->invoices->refreshPaymentStatus($invoice);
        });
    }

    /**
     * Record money paid out to a supplier. With the "customer cheque" method the
     * selected cheques are handed over and the amount is their total.
     *
     * @param  array<string, mixed>  $data  validated payment data
     */
    public function paySupplier(Contact $supplier, array $data, User $user): SupplierPayment
    {
        if (! $supplier->isSupplier()) {
            throw ValidationException::withMessages(['supplier_id' => 'Please select a valid supplier.']);
        }

        return DB::transaction(function () use ($supplier, $data, $user) {
            $payment = new SupplierPayment($this->paymentAttributes($data));
            $payment->supplier_id = $supplier->id;
            $payment->created_by = $user->id;

            $cheques = collect();
            if ($payment->method === PaymentMethod::CustomerCheque) {
                $cheques = $this->lockAvailableCheques($data['cheque_ids'] ?? []);
                $payment->amount = Money::fromCents($cheques->sum(fn (Cheque $c) => Money::toCents($c->amount)));
                $payment->reference = Str::limit('Cheques '.$cheques->pluck('cheque_no')->implode(', '), 100, '');
                $payment->bank = null;
                $payment->cheque_date = null;
            } else {
                $payment->amount = Money::fromCents(Money::toCents($data['amount']));
            }

            $payment->save();

            foreach ($cheques as $cheque) {
                $cheque->status = ChequeStatus::Endorsed;
                $cheque->supplier_payment_id = $payment->id;
                $cheque->save();
            }

            return $payment;
        });
    }

    /**
     * Delete a supplier payment. Customer cheques it used go back to "in hand".
     */
    public function removeSupplierPayment(SupplierPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            Cheque::query()
                ->where('supplier_payment_id', $payment->id)
                ->lockForUpdate()
                ->get()
                ->each(function (Cheque $cheque) {
                    $cheque->status = ChequeStatus::InHand;
                    $cheque->supplier_payment_id = null;
                    $cheque->save();
                });

            $payment->delete();
        });
    }

    public function depositCheque(Cheque $cheque, CarbonInterface|string $date): void
    {
        DB::transaction(function () use ($cheque, $date) {
            $cheque = Cheque::query()->lockForUpdate()->findOrFail($cheque->id);
            if ($cheque->status !== ChequeStatus::InHand) {
                throw ValidationException::withMessages(['cheque' => "Cheque {$cheque->cheque_no} is not in hand, so it cannot be deposited."]);
            }

            $cheque->status = ChequeStatus::Deposited;
            $cheque->deposited_on = $date;
            $cheque->save();
        });
    }

    public function undoDeposit(Cheque $cheque): void
    {
        DB::transaction(function () use ($cheque) {
            $cheque = Cheque::query()->lockForUpdate()->findOrFail($cheque->id);
            if ($cheque->status !== ChequeStatus::Deposited) {
                throw ValidationException::withMessages(['cheque' => 'Only deposited cheques can be moved back to in hand.']);
            }

            $cheque->status = ChequeStatus::InHand;
            $cheque->deposited_on = null;
            $cheque->save();
        });
    }

    /**
     * The bank returned (bounced) the cheque: the customer's payment is reversed,
     * so the invoice is owed again. The cheque stays in the register as "returned".
     */
    public function returnCheque(Cheque $cheque, CarbonInterface|string $date, ?string $note): void
    {
        DB::transaction(function () use ($cheque, $date, $note) {
            $cheque = Cheque::query()->lockForUpdate()->findOrFail($cheque->id);

            if ($cheque->status === ChequeStatus::Endorsed) {
                throw ValidationException::withMessages([
                    'cheque' => "Cheque {$cheque->cheque_no} was given to a supplier. Delete that supplier payment first, then mark the cheque returned.",
                ]);
            }
            if ($cheque->status === ChequeStatus::Returned) {
                throw ValidationException::withMessages(['cheque' => 'This cheque is already marked as returned.']);
            }

            if ($cheque->customer_payment_id) {
                $payment = CustomerPayment::query()->find($cheque->customer_payment_id);
                if ($payment) {
                    $invoice = Invoice::query()->lockForUpdate()->findOrFail($payment->invoice_id);
                    $cheque->customer_payment_id = null;
                    $cheque->save();
                    $payment->delete();
                    $this->invoices->refreshPaymentStatus($invoice);
                }
            }

            $cheque->status = ChequeStatus::Returned;
            $cheque->returned_on = $date;
            $cheque->notes = $note;
            $cheque->save();
        });
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return \Illuminate\Support\Collection<int, Cheque>
     */
    private function lockAvailableCheques(array $ids)
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $cheques = Cheque::query()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();

        $problems = [];
        foreach ($ids as $id) {
            $cheque = $cheques->firstWhere('id', $id);
            if (! $cheque) {
                $problems[] = 'a selected cheque no longer exists';
            } elseif ($cheque->status !== ChequeStatus::InHand) {
                $problems[] = "cheque {$cheque->cheque_no} is already ".mb_strtolower($cheque->status->label());
            } elseif ($cheque->isExpired()) {
                $problems[] = "cheque {$cheque->cheque_no} has expired";
            }
        }

        if ($cheques->isEmpty() || $problems !== []) {
            throw ValidationException::withMessages([
                'cheque_ids' => $problems === [] ? 'Select at least one cheque.' : 'Cannot use these cheques: '.implode('; ', $problems).'.',
            ]);
        }

        return $cheques;
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
