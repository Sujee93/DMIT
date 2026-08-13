<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Invoice') }}
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

                <div class="mb-4 rounded-md bg-gray-50 border border-gray-200 p-3 text-sm text-gray-600">
                    <strong>
                        @if ($invoice->isTaxInvoice())
                            Tax Invoice — {{ $invoice->customer_name }}
                        @else
                            General Invoice
                        @endif
                    </strong>
                    &middot; No. {{ $invoice->reference_number ?? $invoice->invoice_number }}
                    <span class="text-gray-400">(type cannot be changed after creation)</span>
                </div>

                <form method="POST" action="{{ route('invoices.update', $invoice) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="date_of_invoice" value="Date of Invoice" />
                            <x-text-input id="date_of_invoice" name="date_of_invoice" type="date" class="mt-1 block w-full" value="{{ old('date_of_invoice', $invoice->date_of_invoice->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('date_of_invoice')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="mode_of_payment" value="Mode of Payment" />
                            <x-text-input id="mode_of_payment" name="mode_of_payment" type="text" class="mt-1 block w-full" value="{{ old('mode_of_payment', $invoice->mode_of_payment) }}" placeholder="Cash / Cheque / Bank Transfer" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="vehicle_no" value="Vehicle No" />
                            <x-text-input id="vehicle_no" name="vehicle_no" type="text" class="mt-1 block w-full" value="{{ old('vehicle_no', $invoice->vehicle_no) }}" />
                        </div>
                        <div>
                            <x-input-label for="sup_no" value="Sup. No" />
                            <x-text-input id="sup_no" name="sup_no" type="text" class="mt-1 block w-full" value="{{ old('sup_no', $invoice->sup_no) }}" />
                        </div>
                    </div>

                    @include('invoices._type-fields', ['invoice' => $invoice])

                    @include('invoices._line-items', [
                        'items' => old('items', $invoice->items->map(fn ($item) => [
                            'product_id' => $item->product_id,
                            'reference' => $item->reference,
                            'description' => $item->description,
                            'quantity' => (float) $item->quantity,
                            'unit_price' => (float) $item->unit_price,
                        ])->all()),
                        'products' => $products,
                        'invoice' => $invoice,
                    ])

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Update Invoice') }}</x-primary-button>
                        <a href="{{ route('invoices.print', $invoice) }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            window.katmoInvoiceMode = '{{ $invoice->invoice_type }}';
            document.querySelectorAll('[data-mode]').forEach(function (el) {
                el.classList.toggle('hidden', el.dataset.mode !== window.katmoInvoiceMode);
            });
            if (window.katmoRecalcInvoiceTotals) window.katmoRecalcInvoiceTotals();
        })();
    </script>
</x-app-layout>
