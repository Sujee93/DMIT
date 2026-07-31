<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    private const FILTERS = ['today', 'yesterday', '7days', 'month', 'custom'];

    public function sales(Request $request): View
    {
        [$from, $to, $filter] = $this->resolveRange($request);

        $invoices = Invoice::with('customer')
            ->whereDate('date_of_invoice', '>=', $from->toDateString())
            ->whereDate('date_of_invoice', '<=', $to->toDateString())
            ->orderBy('date_of_invoice')
            ->orderBy('id')
            ->get();

        return view('reports.sales', [
            'invoices' => $invoices,
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
            'totalSales' => $invoices->sum('total_amount'),
            'totalSubtotal' => $invoices->sum('subtotal'),
            'totalVat' => $invoices->sum('vat_amount'),
            'taxCount' => $invoices->where('invoice_type', 'tax')->count(),
            'generalCount' => $invoices->where('invoice_type', 'general')->count(),
        ]);
    }

    public function productsSold(Request $request): View
    {
        [$from, $to, $filter] = $this->resolveRange($request);

        $rows = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereDate('invoices.date_of_invoice', '>=', $from->toDateString())
            ->whereDate('invoices.date_of_invoice', '<=', $to->toDateString())
            ->select([
                'invoice_items.product_id',
                DB::raw('COALESCE(products.name, invoice_items.description) as product_name'),
                DB::raw('SUM(invoice_items.quantity) as total_quantity'),
                DB::raw('SUM(invoice_items.amount) as total_amount'),
                DB::raw('COUNT(DISTINCT invoice_items.invoice_id) as invoice_count'),
            ])
            ->groupBy('invoice_items.product_id', 'products.name', 'invoice_items.description')
            ->orderByDesc('total_amount')
            ->get();

        return view('reports.products-sold', [
            'rows' => $rows,
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
            'totalQuantity' => $rows->sum('total_quantity'),
            'totalAmount' => $rows->sum('total_amount'),
        ]);
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolveRange(Request $request): array
    {
        $filter = $request->string('filter')->toString();
        $filter = in_array($filter, self::FILTERS, true) ? $filter : 'today';

        $today = Carbon::today();

        [$from, $to] = match ($filter) {
            'yesterday' => [$today->copy()->subDay(), $today->copy()->subDay()],
            '7days' => [$today->copy()->subDays(6), $today->copy()],
            'month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'custom' => [
                $request->date('from') ?? $today->copy()->subDays(6),
                $request->date('to') ?? $today->copy(),
            ],
            default => [$today->copy(), $today->copy()],
        };

        if ($from->greaterThan($to)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to, $filter];
    }
}
