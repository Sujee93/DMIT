{{-- One invoice line. Rendered server-side for existing lines and inside a <template> for new ones. --}}
@php
    $prefix = "items[{$index}]";
    $errKey = "items.{$index}";
@endphp
<tr data-item-row data-default-price="{{ $row['default_price'] ?? '' }}">
    <td>
        <input type="hidden" name="{{ $prefix }}[product_id]" value="{{ $row['product_id'] ?? '' }}" data-field="product_id">
        <div class="cell-title" data-field="label">{{ $row['label'] ?? '' }}</div>
        <input type="text" name="{{ $prefix }}[description]" value="{{ $row['description'] ?? '' }}" maxlength="500"
               class="input input-sm mt-1" placeholder="Description / colour / size" data-field="description" aria-label="Description">
        @error("{$errKey}.product_id")<span class="field__error">{{ $message }}</span>@enderror
    </td>
    <td class="col-qty">
        <input type="number" name="{{ $prefix }}[quantity]" value="{{ $row['quantity'] ?? 1 }}" min="1" max="1000000" step="1" required
               @class(['input', 'input-sm', 'is-invalid' => $errors->has("{$errKey}.quantity")]) data-field="quantity" aria-label="Quantity">
    </td>
    <td class="col-money">
        <input type="number" name="{{ $prefix }}[unit_cost]" value="{{ $row['unit_cost'] ?? '' }}" min="0" step="0.01" inputmode="decimal"
               @class(['input', 'input-sm', 'is-invalid' => $errors->has("{$errKey}.unit_cost")]) data-field="unit_cost" aria-label="Unit cost">
    </td>
    <td class="col-money">
        <input type="number" name="{{ $prefix }}[unit_price]" value="{{ $row['unit_price'] ?? '' }}" min="0" step="0.01" required inputmode="decimal"
               @class(['input', 'input-sm', 'is-invalid' => $errors->has("{$errKey}.unit_price")]) data-field="unit_price" aria-label="Unit price">
    </td>
    <td class="num col-total fw-bold" data-field="line_total">0.00</td>
    <td class="col-remove">
        <button type="button" class="btn btn-ghost btn-sm btn-icon" data-remove-row title="Remove line"><x-icon name="x" /><span class="sr-only">Remove</span></button>
    </td>
</tr>
