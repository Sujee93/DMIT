@extends('layouts.app')

@section('title', 'Dues')

@section('content')
<x-page-header title="Dues & overdue invoices" :subtitle="'Total overdue: '.money($total)">
    <x-slot:breadcrumb><a href="{{ route('reports.index') }}">Reports</a> / Dues</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Overdue invoices as at '.today()->format('d M Y')])

<div class="aging mb-2">
    @foreach ($buckets as $label => $amount)
        <div class="aging__item">
            <span class="text-muted">{{ $label }} days overdue</span>
            <strong>{{ money($amount) }}</strong>
        </div>
    @endforeach
</div>

<x-card :flush="true" class="mt-3">
    @if ($invoices->isEmpty())
        <x-empty-state title="No overdue invoices" icon="check">Great - every invoice is within its due date.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Invoice</th><th>Customer</th><th>Phone</th><th>Due date</th><th class="num">Days overdue</th><th class="num">Total</th><th class="num">Balance</th></tr></thead>
                <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td><a href="{{ route('invoices.show', $invoice) }}" class="cell-title nowrap">{{ $invoice->invoice_no }}</a></td>
                        <td>{{ $invoice->customer->displayName() }}</td>
                        <td class="nowrap">{{ $invoice->customer->phone ?: '—' }}</td>
                        <td class="nowrap">{{ $invoice->due_date->format('d M Y') }}</td>
                        <td class="num"><x-badge :tone="$invoice->days_overdue > 60 ? 'danger' : 'warning'">{{ $invoice->days_overdue }}</x-badge></td>
                        <td class="num">{{ money($invoice->total, false) }}</td>
                        <td class="num fw-bold text-danger">{{ money($invoice->balance(), false) }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr><td colspan="6">Total overdue</td><td class="num">{{ money($total, false) }}</td></tr>
                </tfoot>
            </table>
        </div>
    @endif
</x-card>
@endsection
