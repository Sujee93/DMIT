@extends('layouts.app')

@section('title', 'Adjust stock')

@section('content')
<x-page-header title="Adjust stock" subtitle="For opening stock, stock counts, damages or returns. Supplier deliveries should be entered as a GRN instead.">
    <x-slot:breadcrumb><a href="{{ route('stock.index') }}">Stock</a> / Adjust</x-slot:breadcrumb>
</x-page-header>

<form method="POST" action="{{ route('stock.adjust.store') }}" data-submit-once>
    @csrf
    <x-card title="Adjustment">
        <div class="form-grid">
            <x-form.select name="product_id" label="Product" required placeholder="Select product…" wrapper-class="span-2"
                           :options="$products->mapWithKeys(fn ($p) => [$p->id => $p->code.' — '.$p->name.' (in stock: '.$p->stock.')'])->all()" :value="$selectedId" />
            <x-form.select name="direction" label="Change" required :options="['add' => 'Add to stock (+)', 'remove' => 'Remove from stock (−)']" value="add" />
            <x-form.input name="quantity" label="Quantity" type="number" min="1" step="1" required />
            <x-form.input name="movement_date" label="Date" type="date" :value="today()->toDateString()" required />
            <x-form.input name="note" label="Reason" required maxlength="255" placeholder="e.g. Opening stock, stock count, damaged" />
        </div>
        <x-slot:footer>
            <div class="form-actions">
                <a href="{{ route('stock.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="check" /> Save adjustment</button>
            </div>
        </x-slot:footer>
    </x-card>
</form>
@endsection
