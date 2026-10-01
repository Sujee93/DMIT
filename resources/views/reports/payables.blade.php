@extends('layouts.app')

@section('title', 'Payables')

@section('content')
<x-page-header title="Payables" :subtitle="'Total owed to suppliers: '.money($total)">
    <x-slot:breadcrumb><a href="{{ route('reports.index') }}">Reports</a> / Payables</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Payables as at '.today()->format('d M Y')])

<x-card :flush="true">
    @if ($rows->isEmpty())
        <x-empty-state title="Nothing owed" icon="check">You have no outstanding supplier balances.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Supplier</th><th class="num">Invoices</th><th class="num">Goods (cost)</th><th class="num">Paid</th><th class="num">Balance</th><th>Last payment</th><th class="no-print"></th></tr></thead>
                <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td><a href="{{ route('contacts.show', $row->contact) }}" class="cell-title">{{ $row->contact->displayName() }}</a></td>
                        <td class="num">{{ $row->invoice_count }}</td>
                        <td class="num">{{ money($row->purchased, false) }}</td>
                        <td class="num">{{ money($row->paid, false) }}</td>
                        <td @class(['num fw-bold', 'text-success' => $row->balance < 0])>
                            {{ money($row->balance, false) }}@if ($row->balance < 0) <span class="cell-sub">(advance)</span>@endif
                        </td>
                        <td class="nowrap">{{ $row->last_paid ? \Carbon\Carbon::parse($row->last_paid)->format('d M Y') : '—' }}</td>
                        <td class="no-print">
                            @if ($row->balance > 0)
                                <a href="{{ route('supplier-payments.create', ['supplier_id' => $row->contact->id]) }}" class="btn btn-soft btn-sm"><x-icon name="cash" /> Pay</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr><td colspan="2">Total</td><td class="num">{{ money($rows->sum('purchased'), false) }}</td><td class="num">{{ money($rows->sum('paid'), false) }}</td><td class="num">{{ money($total, false) }}</td><td colspan="2"></td></tr>
                </tfoot>
            </table>
        </div>
    @endif
</x-card>
@endsection
