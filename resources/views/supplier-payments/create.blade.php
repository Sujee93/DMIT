@extends('layouts.app')

@section('title', 'Pay supplier')

@section('content')
<x-page-header title="Pay a supplier" subtitle="Record money paid to a supplier. It reduces what you owe them.">
    <x-slot:breadcrumb><a href="{{ route('supplier-payments.index') }}">Supplier payments</a> / New</x-slot:breadcrumb>
</x-page-header>

<div class="grid grid-main-side">
    <form method="POST" action="{{ route('supplier-payments.store') }}" data-submit-once>
        @csrf
        <x-card title="Payment details">
            <div class="form-grid mb-2">
                <x-form.select name="supplier_id" label="Supplier" wrapper-class="span-2" required placeholder="Select supplier…"
                               :options="$suppliers->mapWithKeys(fn ($s) => [$s->id => $s->displayName()])->all()" :value="$selected?->id"
                               data-navigate-param="supplier_id" />
            </div>
            <x-form.payment-fields :methods="$paymentMethods" :default-amount="$balance !== null && (float) $balance > 0 ? $balance : null" />
            <x-slot:footer>
                <div class="form-actions">
                    <a href="{{ $selected ? route('contacts.show', $selected) : route('supplier-payments.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><x-icon name="check" /> Save payment</button>
                </div>
            </x-slot:footer>
        </x-card>
    </form>

    <div>
        @if ($selected)
            <x-card :title="$selected->displayName()">
                <div @class(['balance-box', 'balance-box--paid' => (float) $balance <= 0])>
                    <div class="balance-box__label">Currently owed</div>
                    <div class="balance-box__value">{{ money($balance) }}</div>
                </div>
                <p class="text-muted mt-2 mb-0">Payable = cost of goods on invoices supplied by this supplier, minus payments already made.</p>
            </x-card>
        @else
            <x-card>
                <x-empty-state title="Choose a supplier" icon="truck">Select a supplier to see how much you currently owe them.</x-empty-state>
            </x-card>
        @endif
    </div>
</div>
@endsection
