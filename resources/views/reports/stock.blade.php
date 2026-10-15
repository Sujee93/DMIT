@extends('layouts.app')

@section('title', 'Stock report')

@section('content')
<x-page-header title="Stock report" :subtitle="'As at '.today()->format('d M Y').' · value at cost price'">
    <x-slot:breadcrumb><a href="{{ route('reports.index') }}">Reports</a> / Stock</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Stock report as at '.today()->format('d M Y')])

<div class="grid grid-4">
    <x-stat label="Units in stock" :value="number_format($totals['units'])" icon="cube" tone="blue" />
    <x-stat label="Stock value" :value="money($totals['value'])" icon="cash" tone="green" />
    <x-stat label="Low stock" :value="$totals['low']" icon="alert" tone="amber" />
    <x-stat label="Out of stock" :value="$totals['out']" icon="x" tone="red" />
</div>

<x-card :flush="true" class="mt-3">
    @if ($products->isEmpty())
        <x-empty-state title="No products" icon="cube">Add products and receive stock with a GRN.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table table-sm">
                <thead><tr><th>Code</th><th>Product</th><th class="num">In stock</th><th class="num">Reorder at</th><th class="num">Cost</th><th class="num">Value</th><th>Status</th></tr></thead>
                <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td class="mono fw-bold">{{ $product->code }}</td>
                        <td>{{ $product->name }}@if ($product->variant()) <span class="cell-sub">· {{ $product->variant() }}</span>@endif</td>
                        <td class="num fw-bold">{{ number_format($product->stock) }}</td>
                        <td class="num text-muted">{{ $product->reorder_level ?: '—' }}</td>
                        <td class="num">{{ money($product->cost, false) }}</td>
                        <td class="num">{{ money($product->stockValue(), false) }}</td>
                        <td>@include('stock._badge', ['product' => $product])</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr><td colspan="2">Total</td><td class="num">{{ number_format($totals['units']) }}</td><td colspan="2"></td><td class="num">{{ money($totals['value'], false) }}</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    @endif
</x-card>
@endsection
