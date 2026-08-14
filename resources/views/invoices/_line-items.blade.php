@php($items = $items ?? [['product_id' => null, 'reference' => '', 'description' => '', 'quantity' => 1, 'unit_price' => '']])

<div class="mt-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-medium text-gray-700">Line Items</h3>
        <button type="button" id="add-item" class="text-sm text-primary-600 hover:text-primary-700 font-medium">+ Add Item</button>
    </div>

    <div class="overflow-x-auto border border-gray-200 rounded-md">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ref/PLU</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-2 py-2"></th>
                </tr>
            </thead>
            <tbody id="items-body">
                @foreach ($items as $i => $item)
                    @include('invoices._item-row', ['index' => $i, 'item' => $item])
                @endforeach
            </tbody>
        </table>
    </div>

    <template id="item-row-template">
        <table><tbody>
            @include('invoices._item-row', ['index' => '__INDEX__', 'item' => []])
        </tbody></table>
    </template>
</div>

<div class="mt-6 flex justify-end">
    <div class="w-full max-w-xs space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500">Subtotal</span>
            <span>Rs. <span id="subtotal-display">0.00</span></span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-gray-500">Discount (<input type="number" id="discount_rate" name="discount_rate" step="0.01" min="0" max="100" value="{{ old('discount_rate', optional($invoice)->discount_rate ?? 0) }}" class="w-14 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500 py-0.5">%)</span>
            <span>Rs. <span id="discount-display">0.00</span></span>
        </div>
        <x-input-error :messages="$errors->get('discount_rate')" class="-mt-1" />

        <div data-mode="tax" class="flex justify-between items-center">
            <span class="text-gray-500">VAT (<input type="number" id="vat_rate" name="vat_rate" step="0.01" min="0" max="100" value="{{ old('vat_rate', optional($invoice)->vat_rate ?? 18) }}" class="w-14 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500 py-0.5">%)</span>
            <span>Rs. <span id="vat-display">0.00</span></span>
        </div>

        <div data-mode="general" class="flex justify-between items-center">
            <label class="text-gray-500 flex items-center gap-1.5">
                <input type="checkbox" id="vat_enabled" name="vat_enabled" value="1" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('vat_enabled', optional($invoice)->vat_rate !== null) ? 'checked' : '' }}>
                Add VAT
            </label>
            <span class="flex items-center gap-1 text-gray-500">
                <input type="number" id="general_vat_rate" name="general_vat_rate" step="0.01" min="0" max="100" value="{{ old('general_vat_rate', optional($invoice)->vat_rate ?? 18) }}" class="w-14 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500 py-0.5">%
            </span>
        </div>

        <div data-mode="general" id="general-vat-amount-row" class="flex justify-between hidden">
            <span class="text-gray-500">VAT Amount</span>
            <span>Rs. <span id="general-vat-display">0.00</span></span>
        </div>

        <div class="flex justify-between font-semibold text-gray-800 border-t pt-2">
            <span>Total</span>
            <span>Rs. <span id="total-display">0.00</span></span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-gray-500">Advance</span>
            <input type="number" id="advance" name="advance" step="0.01" min="0" value="{{ old('advance', optional($invoice)->advance ?? 0) }}" class="w-24 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500 py-0.5">
        </div>

        <div class="flex justify-between font-semibold text-gray-800 border-t pt-2">
            <span>Balance</span>
            <span>Rs. <span id="balance-display">0.00</span></span>
        </div>
    </div>
</div>

<script>
(function () {
    const tbody = document.getElementById('items-body');
    const template = document.getElementById('item-row-template');
    let nextIndex = {{ count($items) }};

    function recalcTotals() {
        let subtotal = 0;
        tbody.querySelectorAll('.item-row').forEach(function (row) {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const p = parseFloat(row.querySelector('.price-input').value) || 0;
            row.querySelector('.amount-display').textContent = (q * p).toFixed(2);
            subtotal += q * p;
        });

        document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);

        const discountRateInput = document.getElementById('discount_rate');
        const discountDisplay = document.getElementById('discount-display');
        const discountRate = discountRateInput ? (parseFloat(discountRateInput.value) || 0) : 0;
        const discountAmount = subtotal * discountRate / 100;
        if (discountDisplay) discountDisplay.textContent = discountAmount.toFixed(2);
        const taxable = Math.max(subtotal - discountAmount, 0);

        const vatRateInput = document.getElementById('vat_rate');
        const vatDisplay = document.getElementById('vat-display');
        const totalDisplay = document.getElementById('total-display');
        const advanceInput = document.getElementById('advance');
        const balanceDisplay = document.getElementById('balance-display');
        const vatEnabledCheckbox = document.getElementById('vat_enabled');
        const generalVatRateInput = document.getElementById('general_vat_rate');
        const generalVatRow = document.getElementById('general-vat-amount-row');
        const generalVatDisplay = document.getElementById('general-vat-display');

        let total = taxable;

        if (vatRateInput && window.katmoInvoiceMode === 'tax') {
            const rate = parseFloat(vatRateInput.value) || 0;
            const vat = taxable * rate / 100;
            vatDisplay.textContent = vat.toFixed(2);
            total = taxable + vat;
        } else if (window.katmoInvoiceMode === 'general' && vatEnabledCheckbox) {
            const enabled = vatEnabledCheckbox.checked;
            if (generalVatRateInput) generalVatRateInput.disabled = ! enabled;

            if (enabled) {
                const rate = parseFloat(generalVatRateInput.value) || 0;
                const vat = taxable * rate / 100;
                if (generalVatDisplay) generalVatDisplay.textContent = vat.toFixed(2);
                if (generalVatRow) generalVatRow.classList.remove('hidden');
                total = taxable + vat;
            } else if (generalVatRow) {
                generalVatRow.classList.add('hidden');
            }
        }

        totalDisplay.textContent = total.toFixed(2);

        if (advanceInput && balanceDisplay) {
            const advance = parseFloat(advanceInput.value) || 0;
            balanceDisplay.textContent = (total - advance).toFixed(2);
        }
    }

    function bindRow(row) {
        const qty = row.querySelector('.qty-input');
        const price = row.querySelector('.price-input');
        const select = row.querySelector('.product-select');
        const ref = row.querySelector('.reference-input');
        const desc = row.querySelector('.description-input');
        const removeBtn = row.querySelector('.remove-row');

        qty.addEventListener('input', recalcTotals);
        price.addEventListener('input', recalcTotals);

        select.addEventListener('change', function () {
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                if (!ref.value) ref.value = opt.dataset.code || '';
                if (!desc.value) desc.value = opt.dataset.name || '';
                price.value = opt.dataset.price || '';
            }
            recalcTotals();
        });

        removeBtn.addEventListener('click', function () {
            if (tbody.querySelectorAll('.item-row').length > 1) {
                row.remove();
                recalcTotals();
            }
        });
    }

    tbody.querySelectorAll('.item-row').forEach(bindRow);

    document.getElementById('add-item').addEventListener('click', function () {
        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex++);
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = html;
        const row = wrapper.querySelector('tr');
        tbody.appendChild(row);
        bindRow(row);
        recalcTotals();
    });

    const vatRateInput = document.getElementById('vat_rate');
    if (vatRateInput) vatRateInput.addEventListener('input', recalcTotals);

    const advanceInput = document.getElementById('advance');
    if (advanceInput) advanceInput.addEventListener('input', recalcTotals);

    const discountRateInputEl = document.getElementById('discount_rate');
    if (discountRateInputEl) discountRateInputEl.addEventListener('input', recalcTotals);

    const vatEnabledCheckbox = document.getElementById('vat_enabled');
    if (vatEnabledCheckbox) vatEnabledCheckbox.addEventListener('change', recalcTotals);

    const generalVatRateInput = document.getElementById('general_vat_rate');
    if (generalVatRateInput) generalVatRateInput.addEventListener('input', recalcTotals);

    window.katmoRecalcInvoiceTotals = recalcTotals;
    recalcTotals();
})();
</script>
