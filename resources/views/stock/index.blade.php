@extends('layouts.app')

@section('title', 'Stock')

@section('content')
<x-page-header title="Stock" subtitle="Units on hand. Stock goes up with GRNs and down with invoices.">
    <x-slot:actions>
        <a href="{{ route('reports.stock') }}" class="btn btn-secondary"><x-icon name="printer" /> Stock report</a>
        @can('admin')
            <a href="{{ route('stock.adjust') }}" class="btn btn-secondary"><x-icon name="pencil" /> Adjust stock</a>
        @endcan
        <a href="{{ route('grns.create') }}" class="btn btn-primary"><x-icon name="plus" /> New GRN</a>
    </x-slot:actions>
</x-page-header>

<div class="grid grid-4">
    <x-stat label="Units in stock" :value="number_format((int) $totals->units)" icon="cube" tone="blue" />
    <x-stat label="Stock value (cost)" :value="money($totals->value)" icon="cash" tone="green" />
    <x-stat label="Low stock" :value="(int) $totals->low_count" icon="alert" tone="amber" :href="route('stock.index', ['filter' => 'low'])" hint="At or below reorder level" />
    <x-stat label="Out of stock" :value="(int) $totals->out_count" icon="x" tone="red" :href="route('stock.index', ['filter' => 'out'])" />
</div>

<x-card :flush="true" class="mt-3">
    <form method="GET" class="filters card-toolbar">
        <div class="search-input">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Search code, name, colour, size" maxlength="100">
        </div>
        <select name="filter" class="select" aria-label="Stock level">
            <option value="">All products</option>
            <option value="in" @selected($filter === 'in')>In stock</option>
            <option value="low" @selected($filter === 'low')>Low stock</option>
            <option value="out" @selected($filter === 'out')>Out of stock</option>
        </select>
        <button class="btn btn-secondary" type="submit">Filter</button>
        @if ($search !== '' || $filter !== '')<a href="{{ route('stock.index') }}" class="btn btn-ghost">Clear</a>@endif
    </form>

    @if ($products->isEmpty())
        <x-empty-state title="No products found" icon="cube">Add products, then receive stock with a GRN.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Code</th><th>Product</th><th class="num">In stock</th><th class="num">Reorder at</th><th class="num">Cost</th><th class="num">Value</th><th>Status</th><th></th></tr></thead>
                <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td class="mono fw-bold">{{ $product->code }}</td>
                        <td>
                            <div class="cell-title">{{ $product->name }}</div>
                            @if ($product->variant())<div class="cell-sub">{{ $product->variant() }}</div>@endif
                        </td>
                        <td class="num fw-bold">{{ number_format($product->stock) }}</td>
                        <td class="num text-muted">{{ $product->reorder_level ?: '—' }}</td>
                        <td class="num">{{ money($product->cost, false) }}</td>
                        <td class="num">{{ money($product->stockValue(), false) }}</td>
                        <td>@include('stock._badge', ['product' => $product])</td>
                        <td><div class="actions"><a href="{{ route('stock.show', $product) }}" class="btn btn-soft btn-sm">History</a></div></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    @endif
</x-card>
@endsection
