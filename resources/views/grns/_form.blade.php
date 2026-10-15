@php
    $productIndex = collect($productOptions)->keyBy('id');
    $describe = fn ($p) => $p ? $p['code'].' — '.$p['name'] : 'Unknown product';

    if (is_array(old('items'))) {
        $rows = collect(old('items'))->map(function ($item) use ($productIndex, $describe) {
            $p = $productIndex->get((int) ($item['product_id'] ?? 0));
            return [
                'product_id' => $item['product_id'] ?? '',
                'label' => $describe($p),
                'stock' => $p['stock'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'unit_cost' => $item['unit_cost'] ?? '',
            ];
        })->all();
    } else {
        $rows = $grn->exists ? $grn->items->map(fn ($item) => [
            'product_id' => $item->product_id,
            'label' => $item->product_code.' — '.$item->product_name,
            'stock' => $productIndex->get($item->product_id)['stock'] ?? null,
            'quantity' => $item->quantity,
            'unit_cost' => $item->unit_cost,
        ])->all() : [];
    }
    $supplierOptions = $suppliers->mapWithKeys(fn ($c) => [$c->id => $c->displayName()])->all();
@endphp

<div data-grn-form>
    <script type="application/json" data-products>@json($productOptions)</script>

    <x-card title="Supplier bill" subtitle="Who delivered the goods and their bill / invoice number.">
        <div class="form-grid form-grid--3">
            <x-form.select name="supplier_id" label="Supplier" :options="$supplierOptions" :value="$grn->supplier_id" placeholder="Select supplier…" required
                           hint="Need a new supplier? Add it under Suppliers." />
            <x-form.input name="grn_date" label="Received date" type="date" :value="$grn->grn_date?->toDateString()" required />
            <x-form.input name="supplier_ref" label="Supplier bill / invoice no." :value="$grn->supplier_ref" maxlength="100" />
        </div>
    </x-card>

    <x-card title="Goods received" subtitle="Quantities are added to stock when you save. Costs update each product's cost price." :flush="true">
        <div class="item-picker">
            <label for="product-search" class="sr-only">Add product</label>
            <input id="product-search" type="text" class="input" list="grn-product-list" placeholder="Search by product code or name, then press Enter…" autocomplete="off" data-product-search>
            <datalist id="grn-product-list">
                @foreach ($productOptions as $p)
                    <option value="{{ $p['code'] }} — {{ $p['name'] }}">{{ $p['description'] }} · in stock {{ $p['stock'] }}</option>
                @endforeach
            </datalist>
            <button type="button" class="btn btn-primary" data-add-product><x-icon name="plus" /> Add</button>
        </div>
        @error('items')<div class="alert alert-error alert-flat">{{ $message }}</div>@enderror

        <div class="table-wrap">
            <table class="table items-table">
                <thead>
                <tr><th>Product</th><th class="col-qty">Qty received</th><th class="col-money">Unit cost</th><th class="num col-total">Line total</th><th class="col-remove"></th></tr>
                </thead>
                <tbody data-items data-next-index="{{ count($rows) }}">
                @foreach ($rows as $i => $row)
                    @include('grns._item-row', ['index' => $i, 'row' => $row])
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="empty {{ count($rows) ? 'hidden' : '' }}" data-items-empty>
            <div class="empty__icon"><x-icon name="cube" /></div>
            <h3>No items yet</h3>
            <p>Search for a product above to add what was received. New products must be created under Products first.</p>
        </div>

        <template data-row-template>
            @include('grns._item-row', ['index' => '__INDEX__', 'row' => []])
        </template>

        <x-slot:footer>
            <div class="grid grid-2">
                <x-form.textarea name="notes" label="Notes" :value="$grn->notes" maxlength="2000" rows="3" />
                <dl class="summary-list totals-panel">
                    <div><dt>Total units</dt><dd data-units>0</dd></div>
                    <div><dt>Subtotal</dt><dd data-subtotal>0.00</dd></div>
                    <div>
                        <dt><label for="f_discount">Discount on bill</label></dt>
                        <dd><input id="f_discount" type="number" name="discount" min="0" step="0.01" inputmode="decimal" value="{{ old('discount', $grn->discount ?? 0) }}" class="input input-sm" data-discount></dd>
                    </div>
                    <div class="is-total"><dt>Bill value</dt><dd>{{ $business->currency_symbol }} <span data-total>0.00</span></dd></div>
                </dl>
            </div>
        </x-slot:footer>
    </x-card>
</div>
