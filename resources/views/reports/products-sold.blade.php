<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Products Sold Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Total Sales Amount</div>
                    <div class="text-2xl font-semibold text-gray-800">Rs. {{ number_format($totalAmount, 2) }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Total Quantity Sold</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ rtrim(rtrim(number_format($totalQuantity, 2), '0'), '.') }}</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
                    <div class="text-sm text-gray-500">Distinct Products</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ $rows->count() }}</div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                @include('reports._filters', ['routeName' => 'reports.products-sold'])

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty Sold</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Amount</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Invoices</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($rows as $row)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ $row->product_name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600 text-right">{{ rtrim(rtrim(number_format($row->total_quantity, 2), '0'), '.') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600 text-right">Rs. {{ number_format($row->total_amount, 2) }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600 text-right">{{ $row->invoice_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">
                                        No sales in this period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
