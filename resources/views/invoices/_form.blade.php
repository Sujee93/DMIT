@php
    $productIndex = collect($productOptions)->keyBy('id');
    $describe = fn ($p) => $p ? $p['code'].' — '.$p['name'] : 'Unknown product';

    if (is_array(old('items'))) {
        $rows = collect(old('items'))->map(function ($item) use ($productIndex, $describe) {
            $p = $productIndex->get((int) ($item['product_id'] ?? 0));
            return [
                'product_id' => $item['product_id'] ?? '',
                'label' => $describe($p),
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_cost' => $item['unit_cost'] ?? '',
                'unit_price' => $item['unit_price'] ?? '',
                'discount_percent' => $item['discount_percent'] ?? 0,
                'default_price' => $p['price'] ?? '',
            ];
        })->all();
    } else {
        $rows = $invoice->exists ? $invoice->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'label' => $item->product_code.' — '.$item->product_name,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_cost' => $item->unit_cost,
            'unit_price' => $item->unit_price,
            'discount_percent' => (float) $item->discount_percent,
            'default_price' => $productIndex->get($item->product_id)['price'] ?? '',
        ])->all() : [];
    }
    $customerOptions = $customers->mapWithKeys(fn ($c) => [$c->id => $c->displayName()])->all();
    $supplierOptions = $suppliers->mapWithKeys(fn ($c) => [$c->id => $c->displayName()])->all();
@endphp

<div data-invoice-form>
    <script type="application/json" data-products>@json($productOptions)</script>

    <x-card title="Invoice details" subtitle="Choose who you are selling to and which supplier is fulfilling the goods.">
        <div class="form-grid">
            <x-form.select name="customer_id" label="Customer" :options="$customerOptions" :value="$invoice->customer_id" placeholder="Select customer…" required
                           hint="Need a new customer? Add it under Customers & Suppliers." />
            <x-form.select name="supplier_id" label="Supplier" :options="$supplierOptions" :value="$invoice->supplier_id" placeholder="Select supplier (optional)…"
                           hint="The cost of these goods is added to this supplier's payable." />
            <x-form.input name="invoice_date" label="Invoice date" type="date" :value="$invoice->invoice_date?->toDateString()" required />
            <x-form.input name="due_date" label="Due date" type="date" :value="$invoice->due_date?->toDateString()" hint="Leave empty for cash / on-delivery invoices." />
        </div>
    </x-card>

    <x-card title="Items" subtitle="Prices default to the product's price - change any line if this customer gets a different rate." :flush="true">
        <div class="item-picker">
            <label for="product-search" class="sr-only">Add product</label>
            <input id="product-search" type="text" class="input" list="product-list" placeholder="Search by product code or name, then press Enter…" autocomplete="off" data-product-search>
            <datalist id="product-list">
                @foreach ($productOptions as $p)
                    <option value="{{ $p['code'] }} — {{ $p['name'] }}">{{ $p['description'] }}</option>
                @endforeach
            </datalist>
            <button type="button" class="btn btn-primary" data-add-product><x-icon name="plus" /> Add</button>
        </div>
        @error('items')<div class="alert alert-error alert-flat">{{ $message }}</div>@enderror

        <div class="table-wrap">
            <table class="table items-table">
                <thead>
                <tr>
                    <th>Product</th><th class="col-qty">Qty</th><th class="col-money">Unit cost</th><th class="col-money">Unit price</th><th class="col-disc">Disc %</th><th class="num col-total">Line total</th><th class="col-remove"></th>
                </tr>
                </thead>
                <tbody data-items data-next-index="{{ count($rows) }}">
                @foreach ($rows as $i => $row)
                    @include('invoices._item-row', ['index' => $i, 'row' => $row])
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="empty {{ count($rows) ? 'hidden' : '' }}" data-items-empty>
            <div class="empty__icon"><x-icon name="cube" /></div>
            <h3>No items yet</h3>
            <p>Search for a product above to add it to this invoice.</p>
        </div>

        <template data-row-template>
            @include('invoices._item-row', ['index' => '__INDEX__', 'row' => []])
        </template>

        <x-slot:footer>
            <div class="grid grid-2">
                 <x-form.textarea name="notes" label="Internal notes (not printed)" :value="$invoice->notes" maxlength="2000" rows="3" />
                <dl class="summary-list totals-panel">
                    <div><dt>Subtotal</dt><dd data-subtotal>0.00</dd></div>
                    <div>
                        <dt><label for="f_discount">Extra discount (amount)</label></dt>
                        <dd><input id="f_discount" type="number" name="discount" min="0" step="0.01" inputmode="decimal" value="{{ old('discount', $invoice->discount ?? 0) }}" class="input input-sm" data-discount></dd>
                    </div>
                    <div class="is-total"><dt>Net invoice value</dt><dd>{{ $business->currency_symbol ?? '' }} <span data-total>0.00</span></dd></div>
                    <div><dt class="text-soft">Cost of goods</dt><dd class="text-soft" data-cost>0.00</dd></div>
                    <div><dt class="text-soft">Gross profit</dt><dd class="text-soft" data-profit>0.00</dd></div>
                </dl>
            </div>
        </x-slot:footer>
    </x-card>
</div>
