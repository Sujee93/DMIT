@extends('layouts.app')

@section('title', 'Receivables')

@section('content')
<x-page-header title="Receivables" :subtitle="'Total outstanding from customers: '.money($total)">
    <x-slot:breadcrumb><a href="{{ route('reports.index') }}">Reports</a> / Receivables</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Receivables as at '.today()->format('d M Y')])

<x-card :flush="true">
    @if ($rows->isEmpty())
        <x-empty-state title="Nothing outstanding" icon="check">Every customer invoice is fully paid.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Customer</th><th>Phone</th><th class="num">Invoices</th><th class="num">Invoiced</th><th class="num">Paid</th><th class="num">Balance</th><th>Oldest due</th></tr></thead>
                <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td><a href="{{ route('contacts.show', $row->contact) }}" class="cell-title">{{ $row->contact->displayName() }}</a></td>
                        <td class="nowrap">{{ $row->contact->phone ?: '—' }}</td>
                        <td class="num">{{ $row->invoice_count }}</td>
                        <td class="num">{{ money($row->invoiced, false) }}</td>
                        <td class="num">{{ money($row->paid, false) }}</td>
                        <td class="num fw-bold">{{ money($row->balance, false) }}</td>
                        <td class="nowrap">
                            @if ($row->oldest_due)
                                @php $due = \Carbon\Carbon::parse($row->oldest_due); @endphp
                                <span @class(['text-danger fw-bold' => $due->lt(today())])>{{ $due->format('d M Y') }}</span>
                            @else — @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr><td colspan="3">Total</td><td class="num">{{ money($rows->sum('invoiced'), false) }}</td><td class="num">{{ money($rows->sum('paid'), false) }}</td><td class="num">{{ money($total, false) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    @endif
</x-card>
@endsection
