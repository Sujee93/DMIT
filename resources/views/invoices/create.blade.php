<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">

                @if (session('error'))
                    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                        Please check the fields below.
                    </div>
                @endif

                <form method="POST" action="{{ route('invoices.store') }}">
                    @csrf

                    <div>
                        <x-input-label for="invoice_choice" value="Invoice Type" />
                        <select id="invoice_choice" name="invoice_choice" required class="mt-1 block w-full border-gray-300 rounded-md focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Choose…</option>
                            <option value="tax:singer" {{ old('invoice_choice') === 'tax:singer' ? 'selected' : '' }}>Tax Invoice — Singer Sri Lanka PLC</option>
                            <option value="tax:arpico" {{ old('invoice_choice') === 'tax:arpico' ? 'selected' : '' }}>Tax Invoice — Richard Peiris Distributors Ltd (Arpico)</option>
                            <option value="tax:ramadia" {{ old('invoice_choice') === 'tax:ramadia' ? 'selected' : '' }}>Tax Invoice — Ramadia Ranmal Holiday Resort</option>
                            <option value="general" {{ old('invoice_choice') === 'general' ? 'selected' : '' }}>General Invoice — Any Customer</option>
                        </select>
                        <x-input-error :messages="$errors->get('invoice_choice')" class="mt-2" />
                    </div>

                    <div id="fixed-customer-info" class="mt-3 hidden rounded-md bg-gray-50 border border-gray-200 p-3 text-sm text-gray-600"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="date_of_invoice" value="Date of Invoice" />
                            <x-text-input id="date_of_invoice" name="date_of_invoice" type="date" class="mt-1 block w-full" value="{{ old('date_of_invoice', now()->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('date_of_invoice')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="mode_of_payment" value="Mode of Payment" />
                            <x-text-input id="mode_of_payment" name="mode_of_payment" type="text" class="mt-1 block w-full" value="{{ old('mode_of_payment') }}" placeholder="Cash / Cheque / Bank Transfer" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="vehicle_no" value="Vehicle No" />
                            <x-text-input id="vehicle_no" name="vehicle_no" type="text" class="mt-1 block w-full" value="{{ old('vehicle_no') }}" />
                        </div>
                        <div>
                            <x-input-label for="sup_no" value="Sup. No" />
                            <x-text-input id="sup_no" name="sup_no" type="text" class="mt-1 block w-full" value="{{ old('sup_no') }}" />
                        </div>
                    </div>

                    @include('invoices._type-fields')

                    @include('invoices._line-items', [
                        'items' => old('items', [['product_id' => null, 'reference' => '', 'description' => '', 'quantity' => 1, 'unit_price' => '']]),
                        'products' => $products,
                        'invoice' => null,
                    ])

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Save & Print') }}</x-primary-button>
                        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const fixedCustomers = {!! json_encode($fixedCustomers->map(fn ($c) => ['name' => $c->name, 'address' => $c->address, 'tin' => $c->tin_number])) !!};
            const choiceSelect = document.getElementById('invoice_choice');
            const infoBox = document.getElementById('fixed-customer-info');

            function applyMode() {
                const val = choiceSelect.value;
                const isTax = val.startsWith('tax:');
                window.katmoInvoiceMode = isTax ? 'tax' : (val === 'general' ? 'general' : null);

                document.querySelectorAll('[data-mode]').forEach(function (el) {
                    el.classList.toggle('hidden', el.dataset.mode !== window.katmoInvoiceMode);
                });

                if (isTax) {
                    const key = val.split(':')[1];
                    const customer = fixedCustomers[key];
                    if (customer) {
                        infoBox.classList.remove('hidden');
                        infoBox.innerHTML = '<strong>' + customer.name + '</strong><br>' +
                            (customer.address || '').replace(/\n/g, '<br>') + '<br>TIN: ' + (customer.tin || '—');
                    }
                } else {
                    infoBox.classList.add('hidden');
                }

                if (window.katmoRecalcInvoiceTotals) window.katmoRecalcInvoiceTotals();
            }

            choiceSelect.addEventListener('change', applyMode);
            applyMode();
        })();
    </script>
</x-app-layout>
