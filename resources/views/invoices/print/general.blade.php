<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->reference_number ?? $invoice->invoice_number }}</title>
    <style>
        /* Continuous dot-matrix stationery: 24cm physical width (9.5"), 20mm sprocket-hole
           margin each side leaves ~20cm printable, 13cm sheet height. */
        @page { size: 240mm 130mm; margin: 4mm 20mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 9.5px; margin: 0; padding: 0; background: #e5e7eb; }
        .toolbar { background: #f3f4f6; padding: 10px 16px; display: flex; justify-content: flex-end; gap: 8px; }
        .toolbar a, .toolbar button { font-family: inherit; font-size: 13px; padding: 6px 14px; border-radius: 6px; border: 1px solid #d1d5db; background: #fff; color: #111827; text-decoration: none; cursor: pointer; }
        .toolbar .primary { background: #059669; color: #fff; border-color: #059669; }
        .sheet { width: 200mm; min-height: 122mm; margin: 10px auto; padding: 3mm 4mm; background: #fff; border: 1px solid #000; }

        .header { display: flex; align-items: center; gap: 8px; }
        .header .logo-mark { width: 28px; height: auto; flex-shrink: 0; }
        .header .header-text { flex: 1; text-align: center; line-height: 1.25; }
        .header .company { font-size: 13px; font-weight: 700; letter-spacing: 0.5px; }
        .header .tagline { font-size: 7.5px; }
        .header .address { font-size: 8px; }
        .header .title { flex-shrink: 0; text-align: right; font-weight: 700; text-decoration: underline; font-size: 11px; }

        /* One bordered box for customer/invoice meta — a vertical divider between the two
           columns, no line between individual fields. */
        .meta { display: flex; border: 1px solid #000; margin-top: 3px; }
        .meta-col { flex: 1; padding: 2px 5px; }
        .meta-col + .meta-col { border-left: 1px solid #000; }
        .meta-col div { padding: 0.5px 0; }
        .meta .lbl { font-weight: 600; }

        table.items { width: 100%; border-collapse: collapse; margin-top: 3px; border: 1px solid #000; }
        table.items th { border-bottom: 1px solid #000; padding: 2px 4px; font-size: 9px; font-weight: 600; text-align: left; }
        table.items td { padding: 1.5px 4px; font-size: 9px; }
        table.items th + th, table.items td + td { border-left: 1px solid #000; }
        table.items td.num, table.items th.num { text-align: right; }

        .totals { width: 62%; margin-left: auto; margin-top: 3px; border: 1px solid #000; }
        .totals-row { display: flex; justify-content: space-between; padding: 1.5px 5px; }
        .totals-row.strong { font-weight: 700; }

        .footer-box { border: 1px solid #000; margin-top: 3px; padding: 2px 5px; }
        .footer-box div { padding: 0.5px 0; }
        .footer-box .lbl { font-weight: 600; }

        .signatures { display: flex; justify-content: space-between; margin-top: 36px; }
        .signatures div { width: 23%; text-align: center; border-top: 1px solid #000; padding-top: 2px; font-size: 8.5px; }

        /* If an invoice has enough items to spill past one 13cm sheet, it should
           continue cleanly onto the next sheet of continuous stationery — repeat the
           column headers, and never slice a row or box in half at the tear line. */
        table.items thead { display: table-header-group; }
        table.items tr { page-break-inside: avoid; break-inside: avoid; }
        .meta, .totals, .footer-box, .signatures { page-break-inside: avoid; break-inside: avoid; }

        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .sheet { border: none; margin: 0; width: auto; min-height: auto; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <a href="{{ route('invoices.edit', $invoice) }}">Edit</a>
        <a href="{{ route('invoices.index') }}">Back to list</a>
        <button class="primary" onclick="window.print()">Print</button>
    </div>

    <div class="sheet">
        <div class="header">
            <img src="{{ asset('images/logo-mark.png') }}" alt="H.F. Amila Dilruk Fonseka" class="logo-mark">
            <div class="header-text">
                <div class="company">H.F. AMILA DILRUK FONSEKA</div>
                <div class="tagline">All kind of solid timber / MDF carpentry work / Pantry units / Bedroom Set / Dining Set / Office Furniture / Doors / Windows &amp; Roofing</div>
                <div class="address">No. 39/1C, Sangabo Mawatha, Keselwatta, Panadura. &middot; Mobile: 0777-593515, Fax: 038 2297333</div>
            </div>
            <div class="title">INVOICE No. {{ $invoice->reference_number ?? $invoice->invoice_number }}</div>
        </div>

        <div class="meta">
            <div class="meta-col">
                <div><span class="lbl">Sup. No:</span> {{ $invoice->sup_no }}</div>
                <div><span class="lbl">Date:</span> {{ $invoice->date_of_invoice->format('Y/m/d') }}</div>
                <div><span class="lbl">Vehicle No:</span> {{ $invoice->vehicle_no }}</div>
            </div>
            <div class="meta-col">
                <div><span class="lbl">M/s:</span> {{ $invoice->customer_name }}</div>
                <div><span class="lbl">Address:</span> {{ $invoice->customer_address }}</div>
                <div><span class="lbl">Telephone:</span> {{ $invoice->customer_telephone }}</div>
            </div>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:12%">Item Code</th>
                    <th>Description</th>
                    <th class="num" style="width:8%">Qty</th>
                    <th class="num" style="width:14%">Rate</th>
                    <th class="num" style="width:16%">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->reference }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="num">{{ rtrim(rtrim(number_format($item->quantity, 2), '0'), '.') }}</td>
                        <td class="num">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="num">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>Subtotal</span>
                <span>{{ number_format($invoice->subtotal, 2) }}</span>
            </div>
            @if ($invoice->discount > 0)
                <div class="totals-row">
                    <span>Discount</span>
                    <span>-{{ number_format($invoice->discount, 2) }}</span>
                </div>
            @endif
            @if ($invoice->vat_amount !== null)
                <div class="totals-row">
                    <span>VAT ({{ rtrim(rtrim(number_format($invoice->vat_rate, 2), '0'), '.') }}%)</span>
                    <span>{{ number_format($invoice->vat_amount, 2) }}</span>
                </div>
            @endif
            <div class="totals-row strong">
                <span>Total</span>
                <span>{{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>Advance</span>
                <span>{{ number_format($invoice->advance, 2) }}</span>
            </div>
            <div class="totals-row strong">
                <span>Balance</span>
                <span>{{ number_format($invoice->balance, 2) }}</span>
            </div>
        </div>

        <div class="footer-box">
            <div><span class="lbl">Amount in words:</span> {{ $invoice->amount_in_words }}</div>
            <div><span class="lbl">Mode of Payment:</span> {{ $invoice->mode_of_payment }}</div>
        </div>

        <div class="signatures">
            <div>Prepared by</div>
            <div>Authorized by</div>
            <div>Checked by</div>
            <div>Customer's Name &amp; Signature</div>
        </div>
    </div>
</body>
</html>
