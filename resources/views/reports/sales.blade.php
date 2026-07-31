<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sales Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Total Sales</div>
                    <div class="text-2xl font-semibold text-gray-800">Rs. {{ number_format($totalSales, 2) }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $invoices->count() }} invoice(s)</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">VAT Collected</div>
                    <div class="text-2xl font-semibold text-gray-800">Rs. {{ number_format($totalVat, 2) }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $taxCount }} tax invoice(s)</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">General Invoices</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ $generalCount }}</div>
                    <div class="text-xs text-gray-400 mt-1">Subtotal Rs. {{ number_format($totalSubtotal, 2) }}</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                @include('reports._filters', ['routeName' => 'reports.sales'])

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. / Ref</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">VAT</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $invoice->date_of_invoice->format('Y-m-d') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">
                                        <a href="{{ route('invoices.print', $invoice) }}" class="text-primary-600 hover:text-primary-700">
                                            {{ $invoice->reference_number ?? $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-sm">
                                        @if ($invoice->isTaxInvoice())
                                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700">Tax</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">General</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ $invoice->customer_name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600 text-right">{{ number_format($invoice->subtotal, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600 text-right">{{ $invoice->vat_amount !== null ? number_format($invoice->vat_amount, 2) : '—' }}</td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800 text-right">{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500">
                                        No invoices in this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($invoices->isNotEmpty())
                            <tfoot>
                                <tr class="bg-gray-50 font-semibold text-gray-800">
                                    <td colspan="4" class="px-5 py-3 text-sm text-right">Totals</td>
                                    <td class="px-5 py-3 text-sm text-right">{{ number_format($totalSubtotal, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-right">{{ number_format($totalVat, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-right">{{ number_format($totalSales, 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
