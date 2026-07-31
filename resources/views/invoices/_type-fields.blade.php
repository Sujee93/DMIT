@php($invoice = $invoice ?? null)

<div id="tax-fields" data-mode="tax" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 hidden">
    <div>
        <x-input-label for="date_of_supply" value="Date of Supply" />
        <x-text-input id="date_of_supply" name="date_of_supply" type="date" class="mt-1 block w-full" value="{{ old('date_of_supply', optional($invoice?->date_of_supply)->toDateString()) }}" />
        <x-input-error :messages="$errors->get('date_of_supply')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="place_of_supply" value="Place of Supply" />
        <x-text-input id="place_of_supply" name="place_of_supply" type="text" class="mt-1 block w-full" value="{{ old('place_of_supply', $invoice->place_of_supply ?? '') }}" />
    </div>
</div>

<div id="general-fields" data-mode="general" class="mt-4 hidden space-y-4">
    <div>
        <x-input-label for="customer_name" value="Customer Name" />
        <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full" value="{{ old('customer_name', $invoice->customer_name ?? '') }}" />
        <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="customer_address" value="Customer Address" />
            <textarea id="customer_address" name="customer_address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">{{ old('customer_address', $invoice->customer_address ?? '') }}</textarea>
        </div>
        <div>
            <x-input-label for="customer_telephone" value="Telephone" />
            <x-text-input id="customer_telephone" name="customer_telephone" type="text" class="mt-1 block w-full" value="{{ old('customer_telephone', $invoice->customer_telephone ?? '') }}" />
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="vehicle_no" value="Vehicle No" />
            <x-text-input id="vehicle_no" name="vehicle_no" type="text" class="mt-1 block w-full" value="{{ old('vehicle_no', $invoice->vehicle_no ?? '') }}" />
        </div>
        <div>
            <x-input-label for="sup_no" value="Sup. No" />
            <x-text-input id="sup_no" name="sup_no" type="text" class="mt-1 block w-full" value="{{ old('sup_no', $invoice->sup_no ?? '') }}" />
        </div>
    </div>
</div>
