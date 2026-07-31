<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Tax Invoice {{ $invoice->reference_number ?? $invoice->invoice_number }}</title>
    <style>
        @page { size: A5; margin: 8mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 10.5px; margin: 0; padding: 0; background: #e5e7eb; }
        .toolbar { background: #f3f4f6; padding: 10px 16px; display: flex; justify-content: flex-end; gap: 8px; }
        .toolbar a, .toolbar button { font-family: inherit; font-size: 13px; padding: 6px 14px; border-radius: 6px; border: 1px solid #d1d5db; background: #fff; color: #111827; text-decoration: none; cursor: pointer; }
        .toolbar .primary { background: #059669; color: #fff; border-color: #059669; }
        .sheet { width: 148mm; min-height: 210mm; margin: 10px auto; padding: 8mm; background: #fff; border: 1px solid #000; }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
        .header .logo-mark { width: 46px; height: auto; flex-shrink: 0; }
        .header .header-text { flex: 1; text-align: center; }
        .header .company { font-size: 17px; font-weight: 700; letter-spacing: 0.5px; }
        .header .tagline { font-size: 9px; margin-top: 2px; line-height: 1.3; }
        .header .address { font-size: 9px; }
        .title { text-align: center; font-weight: 700; text-decoration: underline; margin: 6px 0; font-size: 12px; }
        table.box { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.box td { border: 1px solid #000; padding: 3px 5px; vertical-align: top; font-size: 10px; }
        table.box td.label { font-weight: 600; width: 26%; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.items th, table.items td { border: 1px solid #000; padding: 3px 5px; font-size: 9.5px; }
        table.items th { font-weight: 600; text-align: left; }
        table.items td.num, table.items th.num { text-align: right; }
        table.totals { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.totals td { border: 1px solid #000; padding: 3px 5px; font-size: 10px; }
        table.totals td.label { font-weight: 600; }
        table.totals td.num { text-align: right; }
        .signatures { display: flex; justify-content: space-between; margin-top: 26px; }
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
            <img src="{{ asset('images/logo-mark.png') }}" alt="Katmo Interiors" class="logo-mark">
            <div class="header-text">
                <div class="company">KATMO INTERIORS (PVT) LTD</div>
                <div class="tagline">All kind of solid timber / MDF carpentry work /<br>Pantry units / Doors/ Furnitures</div>
                <div class="address">No. 163, Diggala Road, Keselwatta, Panadura.</div>
                <div class="address">Reg.No: PV114382</div>
            </div>
        </div>

        <div class="title">TAX INVOICE</div>

        <table class="box">
            <tr>
                <td class="label">Date of Invoice:</td>
                <td>{{ $invoice->date_of_invoice->format('Y/m/d') }}</td>
                <td class="label">Tax Invoice No.</td>
                <td>{{ $invoice->reference_number }}</td>
            </tr>
            <tr>
                <td class="label">Supplier's TIN:</td>
                <td>100909430-7000</td>
                <td class="label">Purchaser's TIN:</td>
                <td>{{ $invoice->customer_tin }}</td>
            </tr>
            <tr>
                <td class="label">Supplier's Name:</td>
                <td>Katmo Interiors (Pvt) Ltd</td>
                <td class="label">Purchaser's Name:</td>
                <td>{{ $invoice->customer_name }}</td>
            </tr>
            <tr>
                <td class="label">Address:</td>
                <td>No. 163, Diggala Road,<br>Keselwatta, Panadura.</td>
                <td class="label">Address:</td>
                <td>{!! nl2br(e($invoice->customer_address)) !!}</td>
            </tr>
            <tr>
                <td class="label">Telephone No:</td>
                <td>0777-596836 / 0777-593515</td>
                <td class="label">Telephone No:</td>
                <td>{{ $invoice->customer_telephone }}</td>
            </tr>
            <tr>
                <td class="label">Date of Supply</td>
                <td>{{ optional($invoice->date_of_supply)->format('Y/m/d') }}</td>
                <td class="label">Place of Supply:</td>
                <td>{{ $invoice->place_of_supply }}</td>
            </tr>
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:12%">Reference</th>
                    <th>Description of Goods or Services</th>
                    <th class="num" style="width:10%">Quantity</th>
                    <th class="num" style="width:15%">Unit Price</th>
                    <th class="num" style="width:18%">Amount Excl. VAT (Rs.)</th>
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
                @for ($i = count($invoice->items); $i < 4; $i++)
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                @endfor
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td class="label">Total Value of Supply:</td>
                <td class="num">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="label">VAT Amount (Total Value of Supply @ {{ rtrim(rtrim(number_format($invoice->vat_rate, 2), '0'), '.') }}%)</td>
                <td class="num">{{ number_format($invoice->vat_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Total Amount / consideration including VAT:</td>
                <td class="num"><strong>{{ number_format($invoice->total_amount, 2) }}</strong></td>
            </tr>
        </table>

        <table class="box" style="margin-top:4px;">
            <tr><td class="label">Total Amount in words:</td><td>{{ $invoice->amount_in_words }}</td></tr>
            <tr><td class="label">Mode of Payment:</td><td>{{ $invoice->mode_of_payment }}</td></tr>
        </table>

        <div class="signatures">
            <div>Prepared by</div>
            <div>Checked by</div>
            <div>Customer's Name &amp; Signature</div>
        </div>
    </div>
</body>
</html>
