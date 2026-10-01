@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<x-page-header title="Welcome back, {{ auth()->user()->name }}" subtitle="Here's how the business is doing this month.">
    <x-slot:actions>
        <a href="{{ route('products.create') }}" class="btn btn-secondary"><x-icon name="cube" /> Add product</a>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary"><x-icon name="plus" /> New invoice</a>
    </x-slot:actions>
</x-page-header>

<div class="grid grid-4">
    <x-stat label="Sales this month" :value="money($summary['sales_month'])" icon="trending" tone="blue"
            :hint="$summary['invoices_month'].' invoices'" :href="route('reports.sales')" />
    <x-stat label="Gross profit (month)" :value="money($summary['profit_month'])" icon="chart" tone="green" :href="route('reports.sales')" />
    <x-stat label="Receivables" :value="money($summary['receivable'])" icon="arrow-down" tone="sky" hint="Owed by customers" :href="route('reports.receivables')" />
    <x-stat label="Payables" :value="money($summary['payable'])" icon="arrow-up" tone="amber" hint="Owed to suppliers" :href="route('reports.payables')" />
</div>

<div class="grid grid-main-side mt-3">
    <x-card title="Recent invoices" :flush="true">
        <x-slot:actions>
            <a href="{{ route('invoices.index') }}" class="btn btn-soft btn-sm">View all</a>
        </x-slot:actions>
        @if ($recentInvoices->isEmpty())
            <x-empty-state title="No invoices yet" icon="document">Create your first wholesale invoice to get started.
                <x-slot:action><a href="{{ route('invoices.create') }}" class="btn btn-primary"><x-icon name="plus" /> New invoice</a></x-slot:action>
            </x-empty-state>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Invoice</th><th>Customer</th><th>Date</th><th class="num">Total</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach ($recentInvoices as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}" class="cell-title nowrap">{{ $invoice->invoice_no }}</a></td>
                            <td>{{ $invoice->customer->displayName() }}</td>
                            <td class="nowrap">{{ $invoice->invoice_date->format('d M Y') }}</td>
                            <td class="num">{{ money($invoice->total) }}</td>
                            <td><x-invoice-status :invoice="$invoice" /></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <div>
        <x-card title="Overdue" :subtitle="$summary['overdue_count'].' invoices · '.money($summary['overdue'])" :flush="true">
            <x-slot:actions>
                <a href="{{ route('reports.dues') }}" class="btn btn-soft btn-sm">Dues report</a>
            </x-slot:actions>
            @forelse ($overdueInvoices as $invoice)
                <a href="{{ route('invoices.show', $invoice) }}" class="list-link">
                    <div class="stat__icon tone-red"><x-icon name="clock" /></div>
                    <div class="list-link__body">
                        <div class="cell-title">{{ $invoice->customer->displayName() }}</div>
                        <div class="cell-sub">{{ $invoice->invoice_no }} · due {{ $invoice->due_date->format('d M Y') }}</div>
                    </div>
                    <strong class="text-danger nowrap">{{ money($invoice->balance()) }}</strong>
                </a>
            @empty
                <x-empty-state title="All caught up" icon="check">No overdue invoices right now.</x-empty-state>
            @endforelse
        </x-card>
    </div>
</div>
@endsection
