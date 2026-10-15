{{-- Stock level badge for a product. --}}
@if ($product->isOutOfStock())
    <x-badge tone="danger">Out of stock</x-badge>
@elseif ($product->isLowStock())
    <x-badge tone="warning">Low</x-badge>
@else
    <x-badge tone="success">In stock</x-badge>
@endif
