<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="shrink-0 w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375C2.754 3.75 2.25 4.254 2.25 4.875v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $productCount }}</div>
                        <div class="text-sm text-gray-500">Products ({{ $activeProductCount }} active)</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="shrink-0 w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $customerCount }}</div>
                        <div class="text-sm text-gray-500">Customers</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="shrink-0 w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $userCount }}</div>
                        <div class="text-sm text-gray-500">Users</div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="shrink-0 w-11 h-11 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-semibold text-gray-800">&mdash;</div>
                        <div class="text-sm text-gray-500">Invoices (coming soon)</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-medium text-gray-800">Recently added products</h3>
                        <a href="{{ route('products.index') }}" class="text-sm text-primary-600 hover:text-primary-700">View all</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse ($recentProducts as $product)
                            <div class="px-5 py-3 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">{{ $product->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $product->item_code ?? '—' }}</div>
                                </div>
                                <div class="text-sm text-gray-700">Rs. {{ number_format($product->unit_price, 2) }}</div>
                            </div>
                        @empty
                            <div class="px-5 py-6 text-sm text-gray-500 text-center">
                                No products yet.
                                <a href="{{ route('products.create') }}" class="text-primary-600 hover:text-primary-700">Add your first product</a>.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h3 class="font-medium text-gray-800">Quick actions</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <a href="{{ route('products.create') }}" class="block w-full text-center rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition">
                            Add Product
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('users.create') }}" class="block w-full text-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                                Add User
                            </a>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
