@extends('layouts.app')

@section('title', $contact->name)

@section('content')
<x-page-header :title="$contact->name" :subtitle="$contact->company">
    <x-slot:breadcrumb><a href="{{ route('contacts.index', ['type' => $contact->type->value]) }}">{{ $contact->type->plural() }}</a> / {{ $contact->name }}</x-slot:breadcrumb>
    <x-slot:actions>
        <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-secondary"><x-icon name="pencil" /> Edit</a>
        @if ($contact->isSupplier())
            <a href="{{ route('supplier-payments.create', ['supplier_id' => $contact->id]) }}" class="btn btn-primary"><x-icon name="cash" /> Pay supplier</a>
        @else
            <a href="{{ route('invoices.create', ['customer_id' => $contact->id]) }}" class="btn btn-primary"><x-icon name="plus" /> New invoice</a>
        @endif
    </x-slot:actions>
</x-page-header>

<div class="grid grid-main-side">
    <div>
        <x-card :title="$contact->isCustomer() ? 'Invoices' : 'Invoices supplied'" :flush="true">
            @if ($invoices->isEmpty())
                <x-empty-state title="No invoices yet" icon="document">Invoices for this {{ mb_strtolower($contact->type->label()) }} will appear here.</x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Invoice</th><th>Date</th>
                            @if ($contact->isSupplier())<th>Customer</th><th class="num">Cost (payable)</th>
                            @else<th>Due</th><th class="num">Total</th><th class="num">Balance</th>@endif
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td><a class="cell-title" href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_no }}</a></td>
                                <td class="nowrap">{{ $invoice->invoice_date->format('d M Y') }}</td>
                                @if ($contact->isSupplier())
                                    <td>{{ $invoice->customer->displayName() }}</td>
                                    <td class="num">{{ money($invoice->total_cost) }}</td>
                                @else
                                    <td class="nowrap">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                    <td class="num">{{ money($invoice->total) }}</td>
                                    <td class="num fw-bold">{{ money($invoice->balance()) }}</td>
                                @endif
                                <td><x-invoice-status :invoice="$invoice" /></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $invoices->links() }}
            @endif
        </x-card>

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
                                <td>{{ $payment->method->label() }}</td>
                                <td>{{ $payment->reference ?: '—' }}</td>
                                <td class="num fw-bold">{{ money($payment->amount) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-card>
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
