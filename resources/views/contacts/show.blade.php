@extends('layouts.app')

@section('title', $contact->name)

@section('content')
<x-page-header :title="$contact->name" :subtitle="$contact->company">
    <x-slot:breadcrumb><a href="{{ route('contacts.index', ['type' => $contact->type->value]) }}">{{ $contact->type->plural() }}</a> / {{ $contact->name }}</x-slot:breadcrumb>
    <x-slot:actions>
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary"><x-icon name="pencil" /> Edit</a>
        @if ($contact->isSupplier())
            <a href="{{ route('grns.create', ['supplier_id' => $contact->id]) }}" class="btn btn-secondary"><x-icon name="arrow-down" /> New GRN</a>
            <a href="{{ route('supplier-payments.create', ['supplier_id' => $contact->id]) }}" class="btn btn-primary"><x-icon name="cash" /> Pay supplier</a>
        @else
            <a href="{{ route('invoices.create', ['customer_id' => $contact->id]) }}" class="btn btn-primary"><x-icon name="plus" /> New invoice</a>
        @endif
    </x-slot:actions>
</x-page-header>

<div class="grid grid-main-side">
    <div>
        @if ($contact->isSupplier())
            <x-card title="Goods received (GRN)" subtitle="Supplier bills - these make up what you owe." :flush="true">
                @if ($documents->isEmpty())
                    <x-empty-state title="No GRNs yet" icon="arrow-down">Record stock received from this supplier with a GRN.
                        <x-slot:action><a href="{{ route('grns.create', ['supplier_id' => $contact->id]) }}" class="btn btn-primary"><x-icon name="plus" /> New GRN</a></x-slot:action>
                    </x-empty-state>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead><tr><th>GRN</th><th>Date</th><th>Supplier bill no.</th><th class="num">Units</th><th class="num">Bill value</th></tr></thead>
                            <tbody>
                            @foreach ($documents as $grn)
                                <tr>
                                    <td><a class="cell-title nowrap" href="{{ route('grns.show', $grn) }}">{{ $grn->grn_no }}</a></td>
                                    <td class="nowrap">{{ $grn->grn_date->format('d M Y') }}</td>
                                    <td>{{ $grn->supplier_ref ?: '—' }}</td>
                                    <td class="num">{{ number_format((int) $grn->total_quantity) }}</td>
                                    <td class="num fw-bold">{{ money($grn->total) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $documents->links() }}
                @endif
            </x-card>
        @else
            <x-card title="Invoices" :flush="true">
                @if ($documents->isEmpty())
                    <x-empty-state title="No invoices yet" icon="document">Invoices for this customer will appear here.</x-empty-state>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead><tr><th>Invoice</th><th>Date</th><th>Due</th><th class="num">Total</th><th class="num">Balance</th><th>Status</th></tr></thead>
                            <tbody>
                            @foreach ($documents as $invoice)
                                <tr>
                                    <td><a class="cell-title nowrap" href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_no }}</a></td>
                                    <td class="nowrap">{{ $invoice->invoice_date->format('d M Y') }}</td>
                                    <td class="nowrap">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="num">{{ money($invoice->total) }}</td>
                                    <td class="num fw-bold">{{ money($invoice->balance()) }}</td>
                                    <td><x-invoice-status :invoice="$invoice" /></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $documents->links() }}
                @endif
            </x-card>
        @endif

        <x-card :title="$contact->isCustomer() ? 'Payments received' : 'Payments made'" subtitle="Latest 15" :flush="true">
            @if ($contact->isSupplier())
                <x-slot:actions>
                    <a href="{{ route('supplier-payments.index', ['supplier_id' => $contact->id]) }}" class="btn btn-soft btn-sm">All payments</a>
                </x-slot:actions>
            @endif
            @if ($payments->isEmpty())
                <x-empty-state title="No payments yet" icon="cash">Recorded payments will appear here.</x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Date</th>@if ($contact->isCustomer())<th>Invoice</th>@endif<th>Method</th><th>Reference</th><th class="num">Amount</th></tr></thead>
                        <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="nowrap">{{ $payment->payment_date->format('d M Y') }}</td>
                                @if ($contact->isCustomer())
                                    <td><a href="{{ route('invoices.show', $payment->invoice_id) }}">{{ $payment->invoice->invoice_no }}</a></td>
                                @endif
                                <td><x-badge tone="info">{{ $payment->method->label() }}</x-badge></td>
                                <td>{{ $payment->reference ?: '—' }}</td>
                                <td class="num fw-bold">{{ money($payment->amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>

        @if ($contact->isCustomer() && $cheques->isNotEmpty())
            <x-card title="Cheques from this customer" subtitle="In hand, given to suppliers or returned" :flush="true">
                <x-slot:actions>
                    <a href="{{ route('cheques.index', ['status' => 'all', 'q' => $contact->name]) }}" class="btn btn-soft btn-sm">Cheque register</a>
                </x-slot:actions>
                @include('cheques._table', ['cheques' => $cheques, 'showCustomer' => false, 'showActions' => false])
            </x-card>
        @endif
    </div>

    <div>
        <x-card>
            <div @class(['balance-box', 'balance-box--paid' => (float) $balance <= 0])>
                <div class="balance-box__label">{{ $contact->isCustomer() ? 'Customer owes you' : 'You owe supplier' }}</div>
                <div class="balance-box__value">{{ money($balance) }}</div>
            </div>
            <dl class="details mt-2">
                @if ($contact->code)<dt>Code</dt><dd class="mono">{{ $contact->code }}</dd>@endif
                <dt>Type</dt><dd><x-badge :tone="$contact->isCustomer() ? 'info' : 'warning'">{{ $contact->type->label() }}</x-badge></dd>
                <dt>Status</dt><dd>{{ $contact->is_active ? 'Active' : 'Inactive' }}</dd>
                <dt>Phone</dt><dd>{{ $contact->phone ?: '—' }}</dd>
                <dt>Email</dt><dd>{{ $contact->email ?: '—' }}</dd>
                <dt>Address</dt><dd>{{ $contact->address ?: '—' }}</dd>
                @if ($contact->notes)
                    <dt>Notes</dt><dd>{!! nl2br(e($contact->notes)) !!}</dd>
                @endif
            </dl>
        </x-card>
    </div>
</div>
@endsection
