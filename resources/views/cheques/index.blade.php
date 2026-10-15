@extends('layouts.app')

@section('title', 'Cheques')

@section('content')
<x-page-header title="Cheque register" subtitle="Cheques received from customers - deposit them, pass them to suppliers, or record bounces.">
    <x-slot:actions>
        <button type="button" class="btn btn-secondary" data-print><x-icon name="printer" /> Print</button>
        <a href="{{ route('supplier-payments.create', ['method' => 'customer_cheque']) }}" class="btn btn-primary"><x-icon name="cash" /> Pay supplier with cheques</a>
    </x-slot:actions>
</x-page-header>
@include('reports._print-header', ['title' => 'Cheque register as at '.today()->format('d M Y')])

<div class="grid grid-4">
    <x-stat label="In hand (usable)" :value="money($summary['available']['amount'])" icon="receipt" tone="blue" :hint="$summary['available']['count'].' cheques'" :href="route('cheques.index', ['status' => 'available'])" />
    <x-stat label="Expiring in {{ config('distribution.cheque_expiry_warning_days') }} days" :value="money($summary['expiring']['amount'])" icon="clock" tone="amber" :hint="$summary['expiring']['count'].' cheques'" :href="route('cheques.index', ['status' => 'available'])" />
    <x-stat label="Given to suppliers" :value="money($summary['endorsed']['amount'])" icon="truck" tone="sky" :hint="$summary['endorsed']['count'].' cheques'" :href="route('cheques.index', ['status' => 'endorsed'])" />
    <x-stat label="Returned / expired" :value="money($summary['returned']['amount'] + $summary['expired']['amount'])" icon="alert" tone="red"
            :hint="$summary['returned']['count'].' returned · '.$summary['expired']['count'].' expired'" :href="route('cheques.index', ['status' => 'returned'])" />
</div>

<x-card :flush="true" class="mt-3">
    <div class="filters card-toolbar">
        <nav class="tabs" aria-label="Cheque status">
            @foreach ([
                'available' => 'In hand',
                'endorsed' => 'Given to suppliers',
                'deposited' => 'Deposited',
                'returned' => 'Returned',
                'expired' => 'Expired',
                'all' => 'All',
            ] as $key => $label)
                <a href="{{ route('cheques.index', array_filter(['status' => $key, 'q' => $search])) }}" @class(['is-active' => $filter === $key])>{{ $label }}</a>
            @endforeach
        </nav>
        <form method="GET" class="filters push-right">
            <input type="hidden" name="status" value="{{ $filter }}">
            <div class="search-input">
                <x-icon name="search" />
                <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Cheque no., bank or customer" maxlength="100">
            </div>
        </form>
    </div>

    @if ($cheques->isEmpty())
        <x-empty-state title="No cheques here" icon="receipt">Cheques are added automatically when a customer pays an invoice by cheque.</x-empty-state>
    @else
        @include('cheques._table', ['cheques' => $cheques, 'showCustomer' => true, 'showActions' => true])
        {{ $cheques->links() }}
    @endif
</x-card>

<p class="text-muted mt-2">
    A cheque is valid for {{ \App\Models\Cheque::validMonths() }} months from the date written on it. Only cheques that are in hand and still valid can be given to a supplier.
</p>
@endsection
