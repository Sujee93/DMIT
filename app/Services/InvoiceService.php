<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Creates and updates invoices. All totals are calculated here on the server;
 * amounts posted by the browser other than the per-line price/cost are never trusted.
 */
class InvoiceService
{
    public function __construct(private readonly InvoiceNumberGenerator $numbers) {}

    /**
     * @param  array<string, mixed>  $data  validated InvoiceRequest data
     */
    public function create(array $data, User $user): Invoice
    {
        return DB::transaction(function () use ($data, $user) {
            $invoice = new Invoice($this->headerAttributes($data));
            $invoice->invoice_no = $this->numbers->next();
            $invoice->created_by = $user->id;
            $invoice->amount_paid = '0.00';

            $this->fillItemsAndTotals($invoice, $data);

            return $invoice;
        });
    }

    /**
     * @param  array<string, mixed>  $data  validated InvoiceRequest data
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);

            if ($invoice->hasPayments()) {
                throw ValidationException::withMessages([
                    'invoice' => 'This invoice already has payments recorded and can no longer be edited.',
                ]);
            }

            $invoice->fill($this->headerAttributes($data));
            $invoice->items()->delete();
            $this->fillItemsAndTotals($invoice, $data);

            return $invoice;
        });
    }

    public function delete(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);

            if ($invoice->hasPayments()) {
                throw ValidationException::withMessages([
                    'invoice' => 'Invoices with payments cannot be deleted. Remove the payments first.',
                ]);
            }

            $invoice->items()->delete();
            $invoice->delete();
        });
    }

    /**
     * Recalculate amount paid and status from the payments table.
     */
    public function refreshPaymentStatus(Invoice $invoice): void
    {
        $paidCents = Money::toCents((string) $invoice->payments()->sum('amount'));

        $invoice->amount_paid = Money::fromCents($paidCents);
        $invoice->status = $this->statusFor(Money::toCents($invoice->total), $paidCents);
        $invoice->save();
    }

    public function statusFor(int $totalCents, int $paidCents): InvoiceStatus
    {
        if ($paidCents >= $totalCents) {
            return InvoiceStatus::Paid;
        }

        return $paidCents > 0 ? InvoiceStatus::Partial : InvoiceStatus::Unpaid;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function headerAttributes(array $data): array
    {
        return [
            'customer_id' => $data['customer_id'],
            'supplier_id' => $data['supplier_id'] ?? null,
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function fillItemsAndTotals(Invoice $invoice, array $data): void
    {
        $lines = $data['items'];
        $products = Product::query()
            ->whereIn('id', array_column($lines, 'product_id'))
            ->get()
            ->keyBy('id');

        $subtotal = 0;
        $totalCost = 0;
        $rows = [];

        foreach (array_values($lines) as $index => $line) {
            /** @var Product $product */
            $product = $products->get((int) $line['product_id']);
            $quantity = (int) $line['quantity'];
            $unitPrice = Money::toCents($line['unit_price']);
            $unitCost = Money::toCents($line['unit_cost'] ?? $product->cost);
            $discountPercent = round((float) ($line['discount_percent'] ?? 0), 2);

            $lineTotal = (int) round($quantity * $unitPrice * (100 - $discountPercent) / 100);
            $lineCost = $quantity * $unitCost;
            $subtotal += $lineTotal;
            $totalCost += $lineCost;

            $rows[] = [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'description' => $line['description'] ?? $this->defaultDescription($product),
                'quantity' => $quantity,
                'unit_cost' => Money::fromCents($unitCost),
                'unit_price' => Money::fromCents($unitPrice),
                'discount_percent' => number_format($discountPercent, 2, '.', ''),
                'line_cost' => Money::fromCents($lineCost),
                'line_total' => Money::fromCents($lineTotal),
                'sort_order' => $index,
            ];
        }

        $discount = Money::toCents($data['discount'] ?? 0);
        if ($discount > $subtotal) {
            throw ValidationException::withMessages(['discount' => 'The discount cannot be more than the invoice subtotal.']);
        }

        $total = $subtotal - $discount;
        $invoice->subtotal = Money::fromCents($subtotal);
        $invoice->discount = Money::fromCents($discount);
        $invoice->total = Money::fromCents($total);
        $invoice->total_cost = Money::fromCents($totalCost);
        $invoice->status = $this->statusFor($total, Money::toCents($invoice->amount_paid));
        $invoice->save();

        $invoice->items()->createMany($rows);
    }

    private function defaultDescription(Product $product): ?string
    {
        $text = trim(implode(' - ', array_filter([$product->description, $product->variant()])));

        return $text === '' ? null : mb_substr($text, 0, 500);
    }
}
