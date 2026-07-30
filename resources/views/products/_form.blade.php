@php($product = $product ?? null)

<div>
    <x-input-label for="item_code" value="Item Code" />
    <x-text-input id="item_code" name="item_code" type="text" class="mt-1 block w-full" value="{{ old('item_code', $product->item_code ?? '') }}" placeholder="e.g. 775645" />
    <x-input-error :messages="$errors->get('item_code')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="name" value="Name" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $product->name ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="unit_price" value="Unit Price (Rs.)" />
    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('unit_price', $product->unit_price ?? '') }}" required />
    <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
</div>

<div class="mt-4 flex items-center">
    <input type="hidden" name="is_active" value="0">
    <input id="is_active" type="checkbox" name="is_active" value="1"
        class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500"
        {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="ms-2 text-sm text-gray-600">Active (available when creating invoices)</label>
    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
</div>
