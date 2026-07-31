<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A5; margin: 8mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 10.5px; margin: 0; padding: 0; background: #e5e7eb; }
        .toolbar { background: #f3f4f6; padding: 10px 16px; display: flex; justify-content: flex-end; gap: 8px; }
        .toolbar a, .toolbar button { font-family: inherit; font-size: 13px; padding: 6px 14px; border-radius: 6px; border: 1px solid #d1d5db; background: #fff; color: #111827; text-decoration: none; cursor: pointer; }
        .toolbar .primary { background: #059669; color: #fff; border-color: #059669; }
        .sheet { width: 148mm; min-height: 210mm; margin: 10px auto; padding: 8mm; background: #fff; border: 1px solid #000; }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
        .header .logo-mark { width: 42px; height: auto; flex-shrink: 0; }
        .header .header-text { flex: 1; text-align: center; }
        .header .company { font-size: 16px; font-weight: 700; letter-spacing: 0.5px; }
        .header .tagline { font-size: 8.5px; margin-top: 2px; line-height: 1.3; }
        .header .address { font-size: 9px; }
        .title { text-align: center; font-weight: 700; text-decoration: underline; margin: 6px 0; font-size: 12px; }
        .meta-row { display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 2px; }
        .field-line { font-size: 10px; margin-bottom: 2px; }
        .field-line .fill { display: inline-block; border-bottom: 1px dotted #000; min-width: 60%; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items th, table.items td { border: 1px solid #000; padding: 3px 5px; font-size: 9.5px; }
        table.items th { font-weight: 600; text-align: left; }
        table.items td.num, table.items th.num { text-align: right; }
        table.totals { width: 60%; margin-left: auto; border-collapse: collapse; margin-top: 4px; }
        table.totals td { border: 1px solid #000; padding: 3px 6px; font-size: 10px; }
        table.totals td.label { font-weight: 600; }
        table.totals td.num { text-align: right; width: 40%; }
        .signatures { display: flex; justify-content: space-between; margin-top: 60px; }
        .signatures div { width: 30%; text-align: center; border-top: 1px solid #000; padding-top: 2px; font-size: 9px; }

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
                <div class="tagline">All kind of solid timber / MDF carpentry work / Pantry units /<br>Bedroom Set / Dining Set / Office Furniture / Doors / Windows &amp; Roofing</div>
                <div class="address">No. 39/1C, Sangabo Mawatha, Keselwatta, Panadura.</div>
                <div class="address">Mobile: 0777-593515, Fax: 038 2297333</div>
            </div>
        </div>

        <div class="title">INVOICE No. {{ $invoice->invoice_number }}</div>

        <div class="meta-row">
            <span>Sup. No.: <span class="fill">{{ $invoice->sup_no }}</span></span>
            <span>Date: <span class="fill">{{ $invoice->date_of_invoice->format('Y/m/d') }}</span></span>
        </div>
        <div class="field-line">M/s.: <span class="fill">{{ $invoice->customer_name }}</span></div>
        <div class="field-line">Address: <span class="fill">{{ $invoice->customer_address }}</span></div>
        <div class="meta-row">
            <span>Telephone: <span class="fill">{{ $invoice->customer_telephone }}</span></span>
            <span>Vehicle No: <span class="fill">{{ $invoice->vehicle_no }}</span></span>
        </div>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:14%">Item Code</th>
                    <th>Description</th>
                    <th class="num" style="width:10%">Qty.</th>
                    <th class="num" style="width:15%">Rate</th>
                    <th class="num" style="width:18%">Amount (Rs.)</th>
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
                @for ($i = count($invoice->items); $i < 5; $i++)
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                @endfor
            </tbody>
        </table>

        <table class="totals">
            <tr><td class="label">Total</td><td class="num"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td></tr>
            <tr><td class="label">Advance</td><td class="num">{{ number_format($invoice->advance, 2) }}</td></tr>
            <tr><td class="label">Balance</td><td class="num">{{ number_format($invoice->balance, 2) }}</td></tr>
        </table>

        <div class="field-line" style="margin-top:6px;">Amount in words: <span class="fill">{{ $invoice->amount_in_words }}</span></div>
        <div class="field-line">Mode of Payment: <span class="fill">{{ $invoice->mode_of_payment }}</span></div>

        <div class="signatures">
            <div>Prepared by</div>
            <div>Checked by</div>
            <div>Customer's Name &amp; Signature</div>
        </div>
    </div>
</body>
</html>
