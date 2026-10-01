@extends('layouts.app')

@section('title', 'Products')

@section('content')
<x-page-header title="Products" subtitle="Your shoe catalogue with cost and selling prices.">
    <x-slot:actions>
        <a href="{{ route('products.create') }}" class="btn btn-primary"><x-icon name="plus" /> Add product</a>
    </x-slot:actions>
</x-page-header>

<x-card :flush="true">
    <form method="GET" class="filters card-toolbar">
        <div class="search-input">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ $search }}" class="input" placeholder="Search code, name, colour, size" maxlength="100">
        </div>
        <select name="status" class="select" aria-label="Status">
            <option value="">All statuses</option>
            <option value="active" @selected($status === 'active')>Active</option>
            <option value="inactive" @selected($status === 'inactive')>Inactive</option>
        </select>
        <button class="btn btn-secondary" type="submit">Filter</button>
        @if ($search !== '' || $status)
            <a href="{{ route('products.index') }}" class="btn btn-ghost">Clear</a>
        @endif
    </form>

    @if ($products->isEmpty())
        <x-empty-state title="No products found" icon="cube">Add products so you can start invoicing.</x-empty-state>
    @else
        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>Code</th><th>Product</th><th>Colour / Size</th>
                    <th class="num">Cost</th><th class="num">Price</th><th class="num">Margin</th><th>Status</th><th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($products as $product)
                    @php $margin = (float) $product->price - (float) $product->cost; @endphp
                    <tr>
                        <td class="mono fw-bold">{{ $product->code }}</td>
                        <td>
                            <div class="cell-title">{{ $product->name }}</div>
                            @if ($product->description)<div class="cell-sub">{{ \Illuminate\Support\Str::limit($product->description, 70) }}</div>@endif
                        </td>
                        <td>{{ $product->variant() ?: '—' }}</td>
                        <td class="num">{{ money($product->cost) }}</td>
                        <td class="num fw-bold">{{ money($product->price) }}</td>
                        <td class="num {{ $margin < 0 ? 'text-danger' : 'text-success' }}">{{ money($margin, false) }}</td>
                        <td>
                            @if ($product->is_active)<x-badge tone="success">Active</x-badge>@else<x-badge>Inactive</x-badge>@endif
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-soft btn-sm btn-icon" title="Edit"><x-icon name="pencil" /><span class="sr-only">Edit</span></a>
                                @can('admin')
                                    <x-delete-button :action="route('products.destroy', $product)" :icon-only="true"
                                        confirm="Delete product {{ $product->code }}? Existing invoices keep their copy of the details." />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $products->links() }}
    @endif
</x-card>
@endsection
