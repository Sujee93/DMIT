@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<x-page-header title="Invoices" subtitle="Wholesale invoices and their payment status.">
    <x-slot:actions>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary"><x-icon name="plus" /> New invoice</a>
    </x-slot:actions>
</x-page-header>

<x-card :flush="true">
    <form method="GET" class="filters card-toolbar">
        <div class="search-input">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Invoice no. or customer" maxlength="100">
        </div>
        <select name="status" class="select" aria-label="Status">
            <option value="">All statuses</option>
            @foreach (\App\Enums\InvoiceStatus::cases() as $case)
                <option value="{{ $case->value }}" @selected($status === $case->value)>{{ $case->label() }}</option>
            @endforeach
            <option value="overdue" @selected($status === 'overdue')>Overdue</option>
        </select>
        <input type="date" name="from" value="{{ $from }}" class="input" aria-label="From date">
        <input type="date" name="to" value="{{ $to }}" class="input" aria-label="To date">
        <button class="btn btn-secondary" type="submit">Filter</button>
        @if ($search !== '' || $status || $from || $to)
            <a href="{{ route('invoices.index') }}" class="btn btn-ghost">Clear</a>
        @endif
    </form>

    @if ($invoices->isEmpty())
        <x-empty-state title="No invoices found" icon="document">Try changing the filters or create a new invoice.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr><th>Invoice</th><th>Customer</th><th>Supplier</th><th>Date</th><th>Due</th><th class="num">Total</th><th class="num">Balance</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td><a href="{{ route('invoices.show', $invoice) }}" class="cell-title nowrap">{{ $invoice->invoice_no }}</a></td>
                        <td>{{ $invoice->customer->displayName() }}</td>
                        <td class="text-muted">{{ $invoice->supplier?->name ?? '—' }}</td>
                        <td class="nowrap">{{ $invoice->invoice_date->format('d M Y') }}</td>
                        <td class="nowrap">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="num">{{ money($invoice->total) }}</td>
                        <td class="num fw-bold">{{ money($invoice->balance()) }}</td>
                        <td><x-invoice-status :invoice="$invoice" /></td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-ghost btn-sm btn-icon" title="View"><x-icon name="eye" /><span class="sr-only">View</span></a>
                                <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-ghost btn-sm btn-icon" title="Print" target="_blank" rel="noopener"><x-icon name="printer" /><span class="sr-only">Print</span></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $invoices->links() }}
    @endif
</x-card>
@endsection
