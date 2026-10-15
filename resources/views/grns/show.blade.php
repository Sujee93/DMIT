@extends('layouts.app')

@section('title', $grn->grn_no)

@section('content')
<x-page-header :title="'GRN '.$grn->grn_no" :subtitle="'Received '.$grn->grn_date->format('d M Y')">
    <x-slot:breadcrumb><a href="{{ route('grns.index') }}">GRN</a> / {{ $grn->grn_no }}</x-slot:breadcrumb>
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
        <a href="{{ route('grns.edit', $grn) }}" class="btn btn-secondary"><x-icon name="pencil" /> Edit</a>
        @can('admin')
            <x-delete-button :action="route('grns.destroy', $grn)" size="md" confirm="Delete {{ $grn->grn_no }}? Its quantities will be removed from stock." />
        @endcan
        <a href="{{ route('supplier-payments.create', ['supplier_id' => $grn->supplier_id]) }}" class="btn btn-primary"><x-icon name="cash" /> Pay supplier</a>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Goods Received Note '.$grn->grn_no])

<div class="grid grid-main-side">
    <div>
        <x-card title="Items received" :flush="true">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>#</th><th>Product</th><th class="num">Qty</th><th class="num">Unit cost</th><th class="num">Total</th></tr></thead>
                    <tbody>
                    @foreach ($grn->items as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td><a href="{{ route('stock.show', $item->product_id) }}" class="cell-title"><span class="mono">{{ $item->product_code }}</span> — {{ $item->product_name }}</a></td>
                            <td class="num">{{ number_format($item->quantity) }}</td>
                            <td class="num">{{ money($item->unit_cost) }}</td>
                            <td class="num fw-bold">{{ money($item->line_total) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr><td colspan="2">Total</td><td class="num">{{ number_format($grn->totalQuantity()) }}</td><td></td><td class="num">{{ money($grn->subtotal) }}</td></tr>
                    @if ((float) $grn->discount > 0)
                        <tr><td colspan="4" class="num">Discount</td><td class="num">- {{ money($grn->discount) }}</td></tr>
                        <tr><td colspan="4" class="num">Bill value</td><td class="num">{{ money($grn->total) }}</td></tr>
                    @endif
                    </tfoot>
                </table>
            </div>
        </x-card>
    </div>
    <div>
        <x-card>
            <div class="balance-box">
                <div class="balance-box__label">Bill value</div>
                <div class="balance-box__value">{{ money($grn->total) }}</div>
            </div>
            <dl class="details mt-2">
                <dt>Supplier</dt><dd><a href="{{ route('contacts.show', $grn->supplier) }}">{{ $grn->supplier->displayName() }}</a></dd>
                <dt>Supplier bill no.</dt><dd>{{ $grn->supplier_ref ?: '—' }}</dd>
                <dt>Received</dt><dd>{{ $grn->grn_date->format('d M Y') }}</dd>
                <dt>Entered by</dt><dd>{{ $grn->creator?->name ?? '—' }}</dd>
                @if ($grn->notes)<dt>Notes</dt><dd>{!! nl2br(e($grn->notes)) !!}</dd>@endif
            </dl>
        </x-card>
    </div>
</div>
@endsection
