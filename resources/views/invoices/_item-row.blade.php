@php($item = $item ?? [])
<tr class="item-row border-b border-gray-100 last:border-0">
    <td class="px-2 py-2 align-top">
        <select name="items[{{ $index }}][product_id]" class="product-select block w-full text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
            <option value="">— custom —</option>
            @include('invoices._product-options', ['selected' => $item['product_id'] ?? null])
        </select>
    </td>
    <td class="px-2 py-2 align-top">
        <input type="text" name="items[{{ $index }}][reference]" value="{{ $item['reference'] ?? '' }}" class="reference-input block w-24 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
    </td>
    <td class="px-2 py-2 align-top">
        <input type="text" name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" required class="description-input block w-full text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
    </td>
    <td class="px-2 py-2 align-top">
        <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" class="qty-input block w-20 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
    </td>
    <td class="px-2 py-2 align-top">
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" class="price-input block w-28 text-sm border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
    </td>
    <td class="px-2 py-2 align-top text-right text-sm text-gray-700 amount-display">0.00</td>
    <td class="px-2 py-2 align-top text-center">
        <button type="button" class="remove-row text-red-500 hover:text-red-700 text-lg leading-none">&times;</button>
    </td>
</tr>
