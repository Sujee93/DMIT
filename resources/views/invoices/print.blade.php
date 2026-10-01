<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $invoice->invoice_no }} · {{ $business->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Poppins:wght@500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/print.css') }}?v={{ filemtime(public_path('css/print.css')) }}">
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}" defer></script>
</head>
<body>
<div class="toolbar no-print">
    <a href="{{ route('invoices.show', $invoice) }}" class="tb-btn">&larr; Back to invoice</a>
    <button type="button" class="tb-btn tb-btn--primary" data-print>Print / Save as PDF</button>
</div>

<main class="sheet">
    <header class="inv-header">
        <div class="biz">
            @if ($business->logoUrl())
                <img src="{{ $business->logoUrl() }}" alt="" class="biz__logo">
            @endif
            <div>
                <div class="biz__name">{{ $business->name }}</div>
                @if ($business->tagline)<div class="biz__tagline">{{ $business->tagline }}</div>@endif
                <div class="biz__meta">
                    @if ($business->address){{ $business->address }}<br>@endif
                    {{ implode(' · ', array_filter([$business->phone, $business->mobile])) }}
                    @if ($business->email)<br>{{ $business->email }}@endif
                    @if ($business->website) · {{ $business->website }}@endif
                    @if ($business->registration_no || $business->tax_no)
                        <br>{{ implode(' · ', array_filter([
                            $business->registration_no ? 'Reg. No: '.$business->registration_no : null,
                            $business->tax_no ? 'Tax No: '.$business->tax_no : null,
                        ])) }}
                    @endif
                </div>
            </div>
        </div>
        <div class="inv-title">
            <h1>INVOICE</h1>
            <table class="meta">
                <tr><th>Invoice No</th><td>{{ $invoice->invoice_no }}</td></tr>
                <tr><th>Date</th><td>{{ $invoice->invoice_date->format('d M Y') }}</td></tr>
                <tr><th>Due Date</th><td>{{ $invoice->due_date?->format('d M Y') ?? 'On receipt' }}</td></tr>
            </table>
        </div>
    </header>

    <section class="parties">
        <div class="party">
            <div class="party__label">Bill To</div>
            <div class="party__name">{{ $invoice->customer->company ?: $invoice->customer->name }}</div>
            @if ($invoice->customer->company && $invoice->customer->company !== $invoice->customer->name)<div>Attn: {{ $invoice->customer->name }}</div>@endif
            @if ($invoice->customer->address)<div>{{ $invoice->customer->address }}</div>@endif
            @if ($invoice->customer->phone)<div>Tel: {{ $invoice->customer->phone }}</div>@endif
            @if ($invoice->customer->email)<div>{{ $invoice->customer->email }}</div>@endif
        </div>
        <div class="party party--status">
            <div class="party__label">Amount Due</div>
            <div class="party__amount">{{ money($invoice->balance()) }}</div>
            <div class="status status--{{ $invoice->status->value }}">{{ $invoice->status->label() }}</div>
        </div>
    </section>

    <table class="items">
        <thead>
        <tr>
            <th class="c-no">#</th>
            <th class="c-code">Code</th>
            <th>Description</th>
            <th class="c-num">Qty</th>
            <th class="c-num">Unit Price</th>
            <th class="c-num">Amount</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td class="c-no">{{ $loop->iteration }}</td>
                <td class="c-code">{{ $item->product_code }}</td>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    @if ($item->description)<div class="desc">{{ $item->description }}</div>@endif
                </td>
                <td class="c-num">{{ number_format($item->quantity) }}</td>
                <td class="c-num">{{ number_format((float) $item->unit_price, 2) }}</td>
                <td class="c-num">{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <section class="bottom">
        <div class="notes">
            @if ($invoice->notes)
                <h4>Notes</h4>
                <p>{!! nl2br(e($invoice->notes)) !!}</p>
            @endif
            @if ($business->bank_details)
                <h4>Bank Details</h4>
                <p>{!! nl2br(e($business->bank_details)) !!}</p>
            @endif
            @if ($business->invoice_terms)
                <h4>Terms &amp; Conditions</h4>
                <p class="small">{!! nl2br(e($business->invoice_terms)) !!}</p>
            @endif
        </div>
        <table class="totals">
            <tr><th>Total Qty</th><td>{{ number_format($invoice->items->sum('quantity')) }}</td></tr>
            <tr><th>Subtotal</th><td>{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
            @if ((float) $invoice->discount > 0)
                <tr><th>Discount</th><td>- {{ number_format((float) $invoice->discount, 2) }}</td></tr>
            @endif
            <tr class="grand"><th>Total ({{ $business->currency_symbol }})</th><td>{{ number_format((float) $invoice->total, 2) }}</td></tr>
            @if ((float) $invoice->amount_paid > 0)
                <tr><th>Paid</th><td>{{ number_format((float) $invoice->amount_paid, 2) }}</td></tr>
                <tr class="due"><th>Balance Due</th><td>{{ number_format((float) $invoice->balance(), 2) }}</td></tr>
            @endif
        </table>
    </section>

    <section class="signatures">
        <div><span></span>Prepared by</div>
        <div><span></span>Checked by</div>
        <div><span></span>Customer signature &amp; seal</div>
    </section>

    <footer class="sheet-footer">
        <div>{{ $business->invoice_footer ?: 'Thank you for your business!' }}</div>
        <div class="credit">Software by EdzStudio</div>
    </footer>
</main>
</body>
</html>
