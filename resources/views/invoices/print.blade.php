<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $invoice->invoice_no }} · {{ $business->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}?v={{ filemtime(public_path('css/print.css')) }}">
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
</head>
<body>
@php
    $customer = $invoice->customer;
    $phones = implode(' / ', array_filter([$business->phone, $business->mobile]));
@endphp
<div class="toolbar no-print">
    <a href="{{ route('invoices.show', $invoice) }}" class="tb-btn">&larr; Back to invoice</a>
    <button type="button" class="tb-btn tb-btn--primary" data-print>Print / Save as PDF</button>
</div>

<main class="sheet">
    <header class="biz">
        @if ($business->logoUrl())
            <img src="{{ $business->logoUrl() }}" alt="" class="biz__logo">
        @endif
        <div class="biz__name">{{ $business->name }}</div>
        @if ($business->address)<div>{{ $business->address }}</div>@endif
        @if ($phones)<div>{{ $phones }}</div>@endif
        @if ($business->email)<div>{{ $business->email }}</div>@endif
    </header>

    <h1 class="doc-title">SALES INVOICE</h1>

    <section class="parties">
        <div class="customer">
            <div class="label">Customer:</div>
            <div>{{ $customer->company ?: $customer->name }}</div>
            @if ($customer->company && $customer->company !== $customer->name)<div>{{ $customer->name }}</div>@endif
            @if ($customer->address)<div>{{ $customer->address }}</div>@endif
            @if ($customer->phone)<div>{{ $customer->phone }}</div>@endif
            @if ($customer->code)<div>Customer Code: {{ $customer->code }}</div>@endif
        </div>
        <table class="meta">
            <tr><th>Invoice date:</th><td class="strong">{{ $invoice->invoice_date->format('Y-m-d') }}</td></tr>
            <tr><th>Invoice no:</th><td class="strong">{{ $invoice->invoice_no }}</td></tr>
            @if ($invoice->due_date)
                <tr><th>Due date:</th><td>{{ $invoice->due_date->format('Y-m-d') }}</td></tr>
            @endif
        </table>
    </section>

    <table class="items">
        <thead>
        <tr>
            <th class="c-code">Item Code</th>
            <th>Description</th>
            <th class="c-num">Qty</th>
            <th class="c-num">Unit Price</th>
            <th class="c-num">Discount</th>
            <th class="c-num">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td class="c-code">{{ $item->product_code }}</td>
                <td>
                    {{ $item->product_name }}
                    @if ($item->description)<span class="desc">- {{ $item->description }}</span>@endif
                </td>
                <td class="c-num">{{ number_format($item->quantity) }}</td>
                <td class="c-num">{{ number_format((float) $item->unit_price, 2) }}</td>
                <td class="c-num">{{ number_format((float) $item->discount, 2) }}</td>
                <td class="c-num">{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals">
        @if ((float) $invoice->totalDiscount() > 0)
            <tr><th>Gross Value:</th><td>{{ number_format((float) $invoice->grossValue(), 2) }}</td></tr>
            <tr><th>Total Discount:</th><td>{{ number_format((float) $invoice->totalDiscount(), 2) }}</td></tr>
        @endif
        <tr class="net"><th>Net Invoice Value:</th><td>{{ number_format((float) $invoice->total, 2) }}</td></tr>
    </table>

    <footer class="sheet-footer">
        <div class="packages">
            <span>Box:<i class="blank"></i></span>
            <span>Bag:<i class="blank"></i></span>
            <span>Others:<i class="blank"></i></span>
            <span class="push">Total Package:<i class="blank blank--wide"></i></span>
        </div>
        <div class="signatures">
            <span>Checked By<i class="dots"></i></span>
            <span>Packed By<i class="dots"></i></span>
            <span>Receiver<i class="dots"></i></span>
        </div>
        <div class="powered">Powered By EdzStudio</div>
    </footer>
</main>
</body>
</html>
