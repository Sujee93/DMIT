@extends('layouts.app')

@section('title', 'GRN')

@section('content')
<x-page-header title="GRN - goods received" subtitle="Stock received from suppliers. Each GRN adds stock and adds the bill to what you owe the supplier.">
    <x-slot:actions>
        <a href="{{ route('grns.create', array_filter(['supplier_id' => $supplierId])) }}" class="btn btn-primary"><x-icon name="plus" /> New GRN</a>
    </x-slot:actions>
</x-page-header>

<x-card :flush="true">
    <form method="GET" class="filters card-toolbar">
        <div class="search-input">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ $search }}" class="input" placeholder="GRN no. or supplier bill no." maxlength="100">
        </div>
        <select name="supplier_id" class="select" aria-label="Supplier">
            <option value="">All suppliers</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected($supplierId === $supplier->id)>{{ $supplier->displayName() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if ($search !== '' || $supplierId)<a href="{{ route('grns.index') }}" class="btn btn-ghost">Clear</a>@endif
    </form>

    @if ($grns->isEmpty())
        <x-empty-state title="No GRNs yet" icon="arrow-down">When stock arrives from a supplier, record it here to add it to stock.
            <x-slot:action><a href="{{ route('grns.create') }}" class="btn btn-primary"><x-icon name="plus" /> New GRN</a></x-slot:action>
        </x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>GRN</th><th>Date</th><th>Supplier</th><th>Supplier bill no.</th><th class="num">Units</th><th class="num">Bill value</th></tr></thead>
                <tbody>
                @foreach ($grns as $grn)
                    <tr>
                        <td><a href="{{ route('grns.show', $grn) }}" class="cell-title nowrap">{{ $grn->grn_no }}</a></td>
                        <td class="nowrap">{{ $grn->grn_date->format('d M Y') }}</td>
                        <td><a href="{{ route('contacts.show', $grn->supplier_id) }}">{{ $grn->supplier->displayName() }}</a></td>
                        <td>{{ $grn->supplier_ref ?: '—' }}</td>
                        <td class="num">{{ number_format((int) $grn->total_quantity) }}</td>
                        <td class="num fw-bold">{{ money($grn->total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $grns->links() }}
    @endif
</x-card>
@endsection
