@extends('layouts.app')

@section('title', 'Purchases report')

@section('content')
<x-page-header title="Purchases (GRN)" :subtitle="$from->format('d M Y').' – '.$to->format('d M Y')">
    <x-slot:breadcrumb><a href="{{ route('reports.index') }}">Reports</a> / Purchases</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Purchases '.$from->format('d M Y').' – '.$to->format('d M Y')])

<x-card :flush="true">
    <form method="GET" class="filters card-toolbar">
        <label class="label" for="from">From</label>
        <input id="from" type="date" name="from" value="{{ $from->toDateString() }}" class="input">
        <label class="label" for="to">To</label>
        <input id="to" type="date" name="to" value="{{ $to->toDateString() }}" class="input">
        <select name="supplier_id" class="select" aria-label="Supplier">
            <option value="">All suppliers</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected((int) $supplierId === $supplier->id)>{{ $supplier->displayName() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Run report</button>
    </form>
</x-card>

<div class="grid grid-3 mt-3">
    <x-stat label="Bill value" :value="money($totals['total'])" icon="truck" tone="amber" :hint="$totals['count'].' GRNs'" />
    <x-stat label="Units received" :value="number_format($totals['quantity'])" icon="cube" tone="blue" />
    <x-stat label="Discounts received" :value="money($totals['discount'])" icon="trending" tone="green" />
</div>

<div class="grid grid-main-side mt-3">
    <x-card title="GRNs" :flush="true">
        @if ($grns->isEmpty())
            <x-empty-state title="No goods received in this period" icon="arrow-down">Try a different date range.</x-empty-state>
        @else
            <div class="table-wrap">
                <table class="table table-sm">
                    <thead><tr><th>Date</th><th>GRN</th><th>Supplier</th><th>Bill no.</th><th class="num">Units</th><th class="num">Value</th></tr></thead>
                    <tbody>
                    @foreach ($grns as $grn)
                        <tr>
                            <td class="nowrap">{{ $grn->grn_date->format('d M Y') }}</td>
                            <td><a href="{{ route('grns.show', $grn) }}">{{ $grn->grn_no }}</a></td>
                            <td>{{ $grn->supplier->displayName() }}</td>
                            <td>{{ $grn->supplier_ref ?: '—' }}</td>
                            <td class="num">{{ number_format((int) $grn->total_quantity) }}</td>
                            <td class="num">{{ money($grn->total, false) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr><td colspan="4">Total</td><td class="num">{{ number_format($totals['quantity']) }}</td><td class="num">{{ money($totals['total'], false) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </x-card>

    <x-card title="By supplier" :flush="true">
        @if ($bySupplier->isEmpty())
            <x-empty-state title="No suppliers" icon="truck">Nothing received in this period.</x-empty-state>
        @else
            <table class="table table-sm">
                <thead><tr><th>Supplier</th><th class="num">GRNs</th><th class="num">Value</th></tr></thead>
                <tbody>
                @foreach ($bySupplier as $row)
                    <tr>
                        <td><a href="{{ route('contacts.show', $row->supplier) }}">{{ $row->supplier->displayName() }}</a></td>
                        <td class="num">{{ $row->count }}</td>
                        <td class="num">{{ money($row->total, false) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
</div>
@endsection
