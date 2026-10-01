@extends('layouts.app')

@section('title', 'Supplier payments')

@section('content')
<x-page-header title="Supplier payments" subtitle="Money paid out to your suppliers.">
    <x-slot:actions>
        <a href="{{ route('supplier-payments.create', array_filter(['supplier_id' => $supplierId])) }}" class="btn btn-primary"><x-icon name="plus" /> Pay a supplier</a>
    </x-slot:actions>
</x-page-header>

<x-card :flush="true">
    <form method="GET" class="filters card-toolbar">
        <select name="supplier_id" class="select" aria-label="Supplier">
            <option value="">All suppliers</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected($supplierId === $supplier->id)>{{ $supplier->displayName() }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if ($supplierId)<a href="{{ route('supplier-payments.index') }}" class="btn btn-ghost">Clear</a>@endif
    </form>

    @if ($payments->isEmpty())
        <x-empty-state title="No supplier payments" icon="cash">When you pay a supplier, record it here to keep payables accurate.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Date</th><th>Supplier</th><th>Method</th><th>Details</th><th>By</th><th class="num">Amount</th>@can('admin')<th></th>@endcan</tr></thead>
                <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td class="nowrap">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td><a href="{{ route('contacts.show', $payment->supplier_id) }}" class="cell-title">{{ $payment->supplier->displayName() }}</a></td>
                        <td><x-badge tone="info">{{ $payment->method->label() }}</x-badge></td>
                        <td>
                            {{ $payment->reference ?: '—' }}
                            @if ($payment->bank || $payment->cheque_date)
                                <div class="cell-sub">{{ $payment->bank }} @if ($payment->cheque_date) · Cheque date {{ $payment->cheque_date->format('d M Y') }} @endif</div>
                            @endif
                            @if ($payment->notes)<div class="cell-sub">{{ $payment->notes }}</div>@endif
                        </td>
                        <td class="text-muted">{{ $payment->creator?->name ?? '—' }}</td>
                        <td class="num fw-bold">{{ money($payment->amount) }}</td>
                        @can('admin')
                            <td><x-delete-button :action="route('supplier-payments.destroy', $payment)" :icon-only="true" label="Remove payment"
                                    confirm="Remove this payment of {{ money($payment->amount) }}?" /></td>
                        @endcan
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $payments->links() }}
    @endif
</x-card>
@endsection
