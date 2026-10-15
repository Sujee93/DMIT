<?php

namespace App\Services;

use App\Models\Cheque;
use App\Models\Contact;
use App\Models\Grn;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\SupplierPayment;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Read-only aggregate queries used by the dashboard and the reports pages.
 */
class ReportService
{
    /**
     * @return array<string, mixed>
     */
    public function sales(CarbonInterface $from, CarbonInterface $to, ?int $customerId = null): array
    {
        $invoices = Invoice::query()
            ->with(['customer:id,name,company', 'supplier:id,name,company'])
            ->whereDate('invoice_date', '>=', $from->toDateString())
            ->whereDate('invoice_date', '<=', $to->toDateString())
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();

        $topProducts = InvoiceItem::query()
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereDate('invoices.invoice_date', '>=', $from->toDateString())
            ->whereDate('invoices.invoice_date', '<=', $to->toDateString())
            ->when($customerId, fn ($q) => $q->where('invoices.customer_id', $customerId))
            ->groupBy('invoice_items.product_code', 'invoice_items.product_name')
            ->select([
                'invoice_items.product_code',
                'invoice_items.product_name',
                DB::raw('SUM(invoice_items.quantity) as quantity'),
                DB::raw('SUM(invoice_items.line_total) as revenue'),
            ])
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return [
            'invoices' => $invoices,
            'topProducts' => $topProducts,
            'totals' => [
                'count' => $invoices->count(),
                'subtotal' => $invoices->sum(fn ($i) => (float) $i->subtotal),
                'discount' => $invoices->sum(fn ($i) => (float) $i->discount),
                'total' => $invoices->sum(fn ($i) => (float) $i->total),
                'cost' => $invoices->sum(fn ($i) => (float) $i->total_cost),
                'received' => $invoices->sum(fn ($i) => (float) $i->amount_paid),
                'profit' => $invoices->sum(fn ($i) => (float) $i->total - (float) $i->total_cost),
            ],
        ];
    }

    /**
     * Customers that still owe money.
     *
     * @return Collection<int, object>
     */
    public function receivables(): Collection
    {
        $balances = Invoice::query()
            ->groupBy('customer_id')
            ->select([
                'customer_id',
                DB::raw('COUNT(*) as invoice_count'),
                DB::raw('SUM(total) as invoiced'),
                DB::raw('SUM(amount_paid) as paid'),
                DB::raw('SUM(total - amount_paid) as balance'),
                DB::raw('MIN(CASE WHEN total > amount_paid THEN due_date END) as oldest_due'),
            ])
            ->havingRaw('SUM(total - amount_paid) > 0')
            ->get()
            ->keyBy('customer_id');

        $contacts = Contact::query()->whereIn('id', $balances->keys())->get()->keyBy('id');

        return $balances
            ->map(fn ($row) => (object) [
                'contact' => $contacts->get($row->customer_id),
                'invoice_count' => (int) $row->invoice_count,
                'invoiced' => (float) $row->invoiced,
                'paid' => (float) $row->paid,
                'balance' => (float) $row->balance,
                'oldest_due' => $row->oldest_due,
            ])
            ->sortByDesc('balance')
            ->values();
    }

    /**
     * What we owe each supplier: cost of goods invoiced through them minus payments made.
     *
     * @return Collection<int, object>
     */
    public function payables(bool $onlyOutstanding = true): Collection
    {
        // What we owe = supplier bills entered as GRNs - payments made.
        $purchases = Grn::query()
            ->groupBy('supplier_id')
            ->select(['supplier_id', DB::raw('SUM(total) as purchased'), DB::raw('COUNT(*) as grn_count')])
            ->get()
            ->keyBy('supplier_id');

        $payments = SupplierPayment::query()
            ->groupBy('supplier_id')
            ->select(['supplier_id', DB::raw('SUM(amount) as paid'), DB::raw('MAX(payment_date) as last_paid')])
            ->get()
            ->keyBy('supplier_id');

        $ids = $purchases->keys()->merge($payments->keys())->unique();
        $contacts = Contact::query()->whereIn('id', $ids)->get()->keyBy('id');

        return $ids
            ->map(function ($id) use ($purchases, $payments, $contacts) {
                $purchased = (float) ($purchases->get($id)->purchased ?? 0);
                $paid = (float) ($payments->get($id)->paid ?? 0);

                return (object) [
                    'contact' => $contacts->get($id),
                    'grn_count' => (int) ($purchases->get($id)->grn_count ?? 0),
                    'purchased' => $purchased,
                    'paid' => $paid,
                    'balance' => round($purchased - $paid, 2),
                    'last_paid' => $payments->get($id)->last_paid ?? null,
                ];
            })
            ->filter(fn ($row) => $row->contact !== null && (! $onlyOutstanding || $row->balance != 0))
            ->sortByDesc('balance')
            ->values();
    }

    /**
     * Overdue customer invoices with ageing buckets.
     *
     * @return array<string, mixed>
     */
    public function dues(): array
    {
        $today = today();

        $invoices = Invoice::query()
            ->with('customer:id,name,company,phone')
            ->overdue()
            ->orderBy('due_date')
            ->get()
            ->each(function (Invoice $invoice) use ($today) {
                $invoice->setAttribute('days_overdue', (int) $invoice->due_date->diffInDays($today));
            });

        $buckets = ['1-30' => 0.0, '31-60' => 0.0, '61-90' => 0.0, '90+' => 0.0];
        foreach ($invoices as $invoice) {
            $days = $invoice->days_overdue;
            $key = match (true) {
                $days <= 30 => '1-30',
                $days <= 60 => '31-60',
                $days <= 90 => '61-90',
                default => '90+',
            };
            $buckets[$key] += (float) $invoice->balance();
        }

        return [
            'invoices' => $invoices,
            'buckets' => $buckets,
            'total' => array_sum($buckets),
        ];
    }

    /**
     * Headline numbers for the dashboard.
     *
     * @return array<string, float|int>
     */
    public function summary(): array
    {
        $monthStart = today()->startOfMonth()->toDateString();

        $payables = $this->payables();

        return [
            'sales_month' => (float) Invoice::query()->whereDate('invoice_date', '>=', $monthStart)->sum('total'),
            'profit_month' => (float) Invoice::query()->whereDate('invoice_date', '>=', $monthStart)->sum(DB::raw('total - total_cost')),
            'invoices_month' => Invoice::query()->whereDate('invoice_date', '>=', $monthStart)->count(),
            'receivable' => (float) Invoice::query()->outstanding()->sum(DB::raw('total - amount_paid')),
            'payable' => (float) $payables->where('balance', '>', 0)->sum('balance'),
            'overdue' => (float) Invoice::query()->overdue()->sum(DB::raw('total - amount_paid')),
            'overdue_count' => Invoice::query()->overdue()->count(),
            'purchases_month' => (float) Grn::query()->whereDate('grn_date', '>=', $monthStart)->sum('total'),
            'stock_value' => (float) Product::query()->where('stock', '>', 0)->sum(DB::raw('stock * cost')),
            'low_stock_count' => Product::query()->active()->where(fn ($q) => $q
                ->where('stock', '<=', 0)
                ->orWhere(fn ($q) => $q->where('reorder_level', '>', 0)->whereColumn('stock', '<=', 'reorder_level')))->count(),
            'cheques_in_hand' => (float) Cheque::query()->available()->sum('amount'),
            'cheques_in_hand_count' => Cheque::query()->available()->count(),
        ];
    }

    /**
     * Goods received (supplier bills) in a period.
     *
     * @return array<string, mixed>
     */
    public function purchases(CarbonInterface $from, CarbonInterface $to, ?int $supplierId = null): array
    {
        $grns = Grn::query()
            ->with('supplier:id,name,company')
            ->withSum('items as total_quantity', 'quantity')
            ->whereDate('grn_date', '>=', $from->toDateString())
            ->whereDate('grn_date', '<=', $to->toDateString())
            ->when($supplierId, fn ($q) => $q->where('supplier_id', $supplierId))
            ->orderBy('grn_date')
            ->orderBy('id')
            ->get();

        $bySupplier = $grns->groupBy('supplier_id')->map(fn ($rows) => (object) [
            'supplier' => $rows->first()->supplier,
            'count' => $rows->count(),
            'quantity' => (int) $rows->sum('total_quantity'),
            'total' => $rows->sum(fn ($g) => (float) $g->total),
        ])->sortByDesc('total')->values();

        return [
            'grns' => $grns,
            'bySupplier' => $bySupplier,
            'totals' => [
                'count' => $grns->count(),
                'quantity' => (int) $grns->sum('total_quantity'),
                'subtotal' => $grns->sum(fn ($g) => (float) $g->subtotal),
                'discount' => $grns->sum(fn ($g) => (float) $g->discount),
                'total' => $grns->sum(fn ($g) => (float) $g->total),
            ],
        ];
    }

    /**
     * Stock on hand per product with value at cost.
     *
     * @return array<string, mixed>
     */
    public function stock(): array
    {
        $products = Product::query()->orderBy('code')->get();

        return [
            'products' => $products,
            'totals' => [
                'units' => (int) $products->sum(fn (Product $p) => max(0, $p->stock)),
                'value' => $products->sum(fn (Product $p) => $p->stockValue()),
                'out' => $products->filter(fn (Product $p) => $p->isOutOfStock())->count(),
                'low' => $products->filter(fn (Product $p) => $p->isLowStock())->count(),
            ],
        ];
    }
}
