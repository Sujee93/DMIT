@extends('layouts.app')

@section('title', $invoice->invoice_no)

@section('content')
@php $balance = (float) $invoice->balance(); @endphp
<x-page-header :title="'Invoice '.$invoice->invoice_no">
    <x-slot:breadcrumb><a href="{{ route('invoices.index') }}">Invoices</a> / {{ $invoice->invoice_no }}</x-slot:breadcrumb>
    <x-slot:actions>
        <a href="{{ route('invoices.print', $invoice) }}" class="btn btn-secondary" target="_blank" rel="noopener"><x-icon name="printer" /> Print A4</a>
        @unless ($invoice->hasPayments())
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-secondary"><x-icon name="pencil" /> Edit</a>
            @can('admin')
                <x-delete-button :action="route('invoices.destroy', $invoice)" size="md" confirm="Delete invoice {{ $invoice->invoice_no }}? This cannot be undone." />
            @endcan
        @endunless
    </x-slot:actions>
</x-page-header>

<div class="grid grid-main-side">
    <div>
        <x-card>
            <div class="grid grid-2">
                <div>
                    <div class="label text-muted">Bill to</div>
                    <h3 class="mt-1">{{ $invoice->customer->name }}</h3>
                    @if ($invoice->customer->company && $invoice->customer->company !== $invoice->customer->name)<div>{{ $invoice->customer->company }}</div>@endif
                    @if ($invoice->customer->address)<div class="text-muted">{{ $invoice->customer->address }}</div>@endif
                    @if ($invoice->customer->phone)<div class="text-muted">{{ $invoice->customer->phone }}</div>@endif
                    @if ($invoice->customer->code)<div class="text-muted">Customer code: <span class="mono">{{ $invoice->customer->code }}</span></div>@endif
                </div>
                <dl class="details">
                    <dt>Status</dt><dd><x-invoice-status :invoice="$invoice" /></dd>
                    <dt>Invoice date</dt><dd>{{ $invoice->invoice_date->format('d M Y') }}</dd>
                    <dt>Due date</dt><dd>{{ $invoice->due_date?->format('d M Y') ?? 'On receipt' }}</dd>
                    <dt>Supplier</dt>
                    <dd>
                        @if ($invoice->supplier)
                            <a href="{{ route('contacts.show', $invoice->supplier) }}">{{ $invoice->supplier->displayName() }}</a>
                        @else — @endif
                    </dd>
                </dl>
            </div>
        </x-card>

        <x-card title="Items" :flush="true">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>#</th><th>Product</th><th class="num">Qty</th><th class="num">Unit price</th><th class="num">Discount</th><th class="num">Total</th></tr></thead>
                    <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="cell-title"><span class="mono">{{ $item->product_code }}</span> — {{ $item->product_name }}</div>
                                @if ($item->description)<div class="cell-sub">{{ $item->description }}</div>@endif
                            </td>
                            <td class="num">{{ number_format($item->quantity) }}</td>
                            <td class="num">{{ money($item->unit_price) }}</td>
                            <td class="num">{{ (float) $item->discount > 0 ? money($item->discount) : '—' }}</td>
                            <td class="num fw-bold">{{ money($item->line_total) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    @if ((float) $invoice->totalDiscount() > 0)
                        <tr><td colspan="5" class="num">Gross value</td><td class="num">{{ money($invoice->grossValue()) }}</td></tr>
                        <tr><td colspan="5" class="num">Total discount</td><td class="num">- {{ money($invoice->totalDiscount()) }}</td></tr>
                    @endif
                    <tr><td colspan="5" class="num">Net invoice value</td><td class="num">{{ money($invoice->total) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </x-card>

        <x-card title="Payments received" :flush="true">
            @if ($invoice->payments->isEmpty())
                <x-empty-state title="No payments yet" icon="cash">Record a payment when the customer pays.</x-empty-state>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Date</th><th>Method</th><th>Details</th><th>By</th><th class="num">Amount</th>@can('admin')<th></th>@endcan</tr></thead>
                        <tbody>
                        @foreach ($invoice->payments as $payment)
                            <tr>
                                <td class="nowrap">{{ $payment->payment_date->format('d M Y') }}</td>
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
                                    <td><x-delete-button :action="route('invoices.payments.destroy', [$invoice, $payment])" :icon-only="true" label="Remove payment"
                                            confirm="Remove this payment of {{ money($payment->amount) }}? The invoice balance will be restored." /></td>
                                @endcan
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
            <div @class(['balance-box', 'balance-box--paid' => $balance <= 0])>
                <div class="balance-box__label">{{ $balance > 0 ? 'Balance due' : 'Fully paid' }}</div>
                <div class="balance-box__value">{{ money($invoice->balance()) }}</div>
            </div>
            <dl class="summary-list mt-2">
                <div><dt>Invoice total</dt><dd>{{ money($invoice->total) }}</dd></div>
                <div><dt>Paid</dt><dd class="text-success">{{ money($invoice->amount_paid) }}</dd></div>
                <div><dt>Cost of goods</dt><dd>{{ money($invoice->total_cost) }}</dd></div>
                <div><dt>Gross profit</dt><dd>{{ money($invoice->profit()) }}</dd></div>
                <div><dt>Created by</dt><dd>{{ $invoice->creator?->name ?? '—' }}</dd></div>
            </dl>
            @if ($invoice->notes)
                <div class="label text-muted mt-2">Internal notes</div>
                <p class="mb-0">{!! nl2br(e($invoice->notes)) !!}</p>
            @endif
        </x-card>

        @if ($balance > 0)
            <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" data-submit-once>
                @csrf
                <x-card title="Record payment" subtitle="Enter what the customer paid. Paying the full balance marks the invoice as paid.">
                    <x-form.payment-fields :methods="$paymentMethods" :max-amount="$invoice->balance()" :default-amount="$invoice->balance()" :stacked="true" />
                    <x-slot:footer>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success btn-block"><x-icon name="check" /> Save payment</button>
                        </div>
                    </x-slot:footer>
                </x-card>
            </form>
        @endif
    </div>
</div>
@endsection
