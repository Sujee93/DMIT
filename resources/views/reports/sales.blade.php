@extends('layouts.app')

@section('title', 'Sales report')

@section('content')
<x-page-header title="Sales report" :subtitle="$from->format('d M Y').' – '.$to->format('d M Y')">
    <x-slot:breadcrumb><a href="{{ route('reports.index') }}">Reports</a> / Sales</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Sales report '.$from->format('d M Y').' – '.$to->format('d M Y')])

<x-card :flush="true">
    <form method="GET" class="filters card-toolbar">
        <label class="label" for="from">From</label>
        <input id="from" type="date" name="from" value="{{ $from->toDateString() }}" class="input">
        <label class="label" for="to">To</label>
        <input id="to" type="date" name="to" value="{{ $to->toDateString() }}" class="input">
        <select name="customer_id" class="select" aria-label="Customer">
            <option value="">All customers</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected((int) $customerId === $customer->id)>{{ $customer->displayName() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Run report</button>
    </form>
</x-card>

<div class="grid grid-4 mt-3">
    <x-stat label="Net sales" :value="money($totals['total'])" icon="trending" tone="blue" :hint="$totals['count'].' invoices'" />
    <x-stat label="Cost of goods" :value="money($totals['cost'])" icon="cube" tone="amber" />
    <x-stat label="Gross profit" :value="money($totals['profit'])" icon="chart" tone="green"
            :hint="$totals['total'] > 0 ? number_format($totals['profit'] / $totals['total'] * 100, 1).'% margin' : null" />
    <x-stat label="Collected" :value="money($totals['received'])" icon="cash" tone="sky" :hint="'Outstanding '.money($totals['total'] - $totals['received'])" />
</div>

<div class="grid grid-main-side mt-3">
    <x-card title="Invoices" :flush="true">
        @if ($invoices->isEmpty())
            <x-empty-state title="No sales in this period" icon="document">Try a different date range.</x-empty-state>
        @else
            <div class="table-wrap">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>Invoice</th><th>Customer</th><th class="num">Total</th><th class="num">Cost</th><th class="num">Profit</th><th class="num">Paid</th></tr></thead>
                    <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="nowrap">{{ $invoice->invoice_date->format('d M Y') }}</td>
                            <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_no }}</a></td>
                            <td>{{ $invoice->customer->displayName() }}</td>
                            <td class="num">{{ money($invoice->total, false) }}</td>
                            <td class="num">{{ money($invoice->total_cost, false) }}</td>
                            <td class="num">{{ money($invoice->profit(), false) }}</td>
                            <td class="num">{{ money($invoice->amount_paid, false) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3">Total</td>
                        <td class="num">{{ money($totals['total'], false) }}</td>
                        <td class="num">{{ money($totals['cost'], false) }}</td>
                        <td class="num">{{ money($totals['profit'], false) }}</td>
                        <td class="num">{{ money($totals['received'], false) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-card>

    <x-card title="Top products" :flush="true">
        @if ($topProducts->isEmpty())
            <x-empty-state title="No products sold" icon="cube">Nothing sold in this period.</x-empty-state>
        @else
            <table class="table table-sm">
                <thead><tr><th>Product</th><th class="num">Qty</th><th class="num">Revenue</th></tr></thead>
                <tbody>
                @foreach ($topProducts as $row)
                    <tr>
                        <td><div class="cell-title mono">{{ $row->product_code }}</div><div class="cell-sub">{{ $row->product_name }}</div></td>
                        <td class="num">{{ number_format((int) $row->quantity) }}</td>
                        <td class="num">{{ money($row->revenue, false) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
</div>
@endsection
