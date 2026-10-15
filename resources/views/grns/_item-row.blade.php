{{-- One GRN line. Rendered server-side for existing lines and inside a <template> for new ones. --}}
@php
    $prefix = "items[{$index}]";
    $errKey = "items.{$index}";
@endphp
<tr data-item-row>
    <td>
        <input type="hidden" name="{{ $prefix }}[product_id]" value="{{ $row['product_id'] ?? '' }}" data-field="product_id">
        <div class="cell-title" data-field="label">{{ $row['label'] ?? '' }}</div>
        <div class="cell-sub" data-field="stock">{{ isset($row['stock']) ? 'In stock now: '.$row['stock'] : '' }}</div>
        @error("{$errKey}.product_id")<span class="field__error">{{ $message }}</span>@enderror
    </td>
    <td class="col-qty">
        <input type="number" name="{{ $prefix }}[quantity]" value="{{ $row['quantity'] ?? 1 }}" min="1" max="1000000" step="1" required
               @class(['input', 'input-sm', 'is-invalid' => $errors->has("{$errKey}.quantity")]) data-field="quantity" aria-label="Quantity received">
    </td>
    <td class="col-money">
        <input type="number" name="{{ $prefix }}[unit_cost]" value="{{ $row['unit_cost'] ?? '' }}" min="0" step="0.01" required inputmode="decimal"
               @class(['input', 'input-sm', 'is-invalid' => $errors->has("{$errKey}.unit_cost")]) data-field="unit_cost" aria-label="Unit cost">
    </td>
    <td class="num col-total fw-bold" data-field="line_total">0.00</td>
    <td class="col-remove">
        <button type="button" class="btn btn-ghost btn-sm btn-icon" data-remove-row title="Remove line"><x-icon name="x" /><span class="sr-only">Remove</span></button>
    </td>
</tr>
