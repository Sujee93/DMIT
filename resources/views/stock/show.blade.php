@extends('layouts.app')

@section('title', 'Stock · '.$product->code)

@section('content')
<x-page-header :title="$product->code.' — '.$product->name" subtitle="Stock movement history">
    <x-slot:breadcrumb><a href="{{ route('stock.index') }}">Stock</a> / {{ $product->code }}</x-slot:breadcrumb>
    <x-slot:actions>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary"><x-icon name="pencil" /> Edit product</a>
        @can('admin')
            <a href="{{ route('stock.adjust', ['product_id' => $product->id]) }}" class="btn btn-primary"><x-icon name="plus" /> Adjust stock</a>
        @endcan
    </x-slot:actions>
</x-page-header>

<div class="grid grid-main-side">
    <x-card title="Movements" subtitle="+ stock in · − stock out" :flush="true">
        @if ($movements->isEmpty())
            <x-empty-state title="No movements yet" icon="cube">Stock changes from GRNs, invoices and adjustments will appear here.</x-empty-state>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Date</th><th>Type</th><th>Reference</th><th>By</th><th class="num">Qty</th></tr></thead>
                    <tbody>
                    @foreach ($movements as $movement)
                        <tr>
                            <td class="nowrap">{{ $movement->movement_date->format('d M Y') }}</td>
                            <td><x-badge :tone="$movement->quantity > 0 ? 'success' : 'info'">{{ $movement->sourceLabel() }}</x-badge></td>
                            <td>
                                @if ($movement->sourceUrl())
                                    <a href="{{ $movement->sourceUrl() }}">{{ $movement->reference }}</a>
                                @else
                                    {{ $movement->reference ?: '' }}
                                @endif
                                @if ($movement->note)<div class="cell-sub">{{ $movement->note }}</div>@endif
                            </td>
                            <td class="text-muted">{{ $movement->creator?->name ?? '—' }}</td>
                            <td @class(['num fw-bold', 'text-success' => $movement->quantity > 0, 'text-danger' => $movement->quantity < 0])>{{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $movements->links() }}
        @endif
    </x-card>

    <x-card>
        <div @class(['balance-box', 'balance-box--paid' => ! $product->isOutOfStock()])>
            <div class="balance-box__label">In stock now</div>
            <div class="balance-box__value">{{ number_format($product->stock) }} units</div>
        </div>
        <dl class="summary-list mt-2">
            <div><dt>Status</dt><dd>@include('stock._badge', ['product' => $product])</dd></div>
            <div><dt>Reorder level</dt><dd>{{ $product->reorder_level ?: 'Not set' }}</dd></div>
            <div><dt>Cost price</dt><dd>{{ money($product->cost) }}</dd></div>
            <div><dt>Stock value</dt><dd>{{ money($product->stockValue()) }}</dd></div>
            <div><dt>Selling price</dt><dd>{{ money($product->price) }}</dd></div>
        </dl>
    </x-card>
</div>
@endsection
