<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invoices') }}
            </h2>
            <a href="{{ route('invoices.create') }}" class="inline-flex items-center rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                New Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('status'))
                <div class="rounded-md bg-primary-50 border border-primary-200 text-primary-700 text-sm px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="p-4 border-b border-gray-100 flex items-center gap-2">
                    <a href="{{ route('invoices.index') }}" class="px-3 py-1.5 rounded-md text-sm {{ $type === '' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
                    <a href="{{ route('invoices.index', ['type' => 'tax']) }}" class="px-3 py-1.5 rounded-md text-sm {{ $type === 'tax' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Tax Invoices</a>
                    <a href="{{ route('invoices.index', ['type' => 'general']) }}" class="px-3 py-1.5 rounded-md text-sm {{ $type === 'general' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">General Invoices</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. / Ref</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($invoices as $invoice)
                                <tr>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $invoice->date_of_invoice->format('Y-m-d') }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-600">{{ $invoice->reference_number ?? $invoice->invoice_number }}</td>
                                    <td class="px-5 py-3 text-sm">
                                        @if ($invoice->isTaxInvoice())
                                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-medium text-primary-700">Tax</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">General</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ $invoice->customer_name }}</td>
                                    <td class="px-5 py-3 text-sm text-gray-700 text-right">Rs. {{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="px-5 py-3 text-right text-sm space-x-3 whitespace-nowrap">
                                        <a href="{{ route('invoices.print', $invoice) }}" class="text-primary-600 hover:text-primary-700 font-medium">Print</a>
                                        <a href="{{ route('invoices.edit', $invoice) }}" class="text-gray-500 hover:text-gray-700 font-medium">Edit</a>
                                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Delete this invoice?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500">
                                        No invoices found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($invoices->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
