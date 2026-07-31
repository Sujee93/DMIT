@foreach ($products as $product)
    <option value="{{ $product->id }}"
        data-code="{{ $product->item_code }}"
        data-name="{{ $product->name }}"
        data-price="{{ $product->unit_price }}"
        @selected(($selected ?? null) == $product->id)>
        {{ $product->name }}{{ $product->item_code ? ' ('.$product->item_code.')' : '' }}
    </option>
@endforeach
