@php
    // Which contact type the current page is about, so Customers / Suppliers highlight correctly.
    $routeContact = request()->route('contact');
    $contactType = request()->routeIs('contacts.*')
        ? ($routeContact instanceof \App\Models\Contact ? $routeContact->type->value : request('type'))
        : null;

    $nav = [
        ['label' => 'Overview', 'items' => [
            ['url' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'home', 'text' => 'Dashboard'],
        ]],
        ['label' => 'Sales', 'items' => [
            ['url' => route('invoices.index'), 'active' => request()->routeIs('invoices.*'), 'icon' => 'document', 'text' => 'Invoices'],
            ['url' => route('contacts.index', ['type' => 'customer']), 'active' => $contactType === 'customer', 'icon' => 'users', 'text' => 'Customers'],
            ['url' => route('cheques.index'), 'active' => request()->routeIs('cheques.*'), 'icon' => 'receipt', 'text' => 'Cheques'],
        ]],
        ['label' => 'Purchasing', 'items' => [
            ['url' => route('contacts.index', ['type' => 'supplier']), 'active' => $contactType === 'supplier', 'icon' => 'truck', 'text' => 'Suppliers'],
            ['url' => route('grns.index'), 'active' => request()->routeIs('grns.*'), 'icon' => 'arrow-down', 'text' => 'GRN'],
            ['url' => route('supplier-payments.index'), 'active' => request()->routeIs('supplier-payments.*'), 'icon' => 'cash', 'text' => 'Supplier Payments'],
        ]],
        ['label' => 'Inventory', 'items' => [
            ['url' => route('products.index'), 'active' => request()->routeIs('products.*'), 'icon' => 'cube', 'text' => 'Products'],
            ['url' => route('stock.index'), 'active' => request()->routeIs('stock.*'), 'icon' => 'store', 'text' => 'Stock'],
        ]],
        ['label' => 'Insights', 'items' => [
            ['url' => route('reports.index'), 'active' => request()->routeIs('reports.*'), 'icon' => 'chart', 'text' => 'Reports'],
        ]],
    ];
    if (auth()->user()->can('admin')) {
        $nav[] = ['label' => 'Administration', 'items' => [
            ['url' => route('settings.edit'), 'active' => request()->routeIs('settings.*'), 'icon' => 'cog', 'text' => 'Business Settings'],
            ['url' => route('users.index'), 'active' => request()->routeIs('users.*'), 'icon' => 'shield', 'text' => 'System Users'],
        ]];
    }
@endphp
<aside class="sidebar" data-sidebar>
    <a href="{{ route('dashboard') }}" class="sidebar__brand">
        @if ($business->logoUrl())
            <img src="{{ $business->logoUrl() }}" alt="">
        @else
            <span class="sidebar__brand-mark">{{ mb_strtoupper(mb_substr($business->name, 0, 1)) }}</span>
        @endif
        <span>
            <span class="sidebar__brand-name">{{ $business->name }}</span><br>
            <span class="sidebar__brand-sub">Wholesale Distribution</span>
        </span>
    </a>

    <nav class="nav" aria-label="Main">
        @foreach ($nav as $group)
            <div class="nav__label">{{ $group['label'] }}</div>
            @foreach ($group['items'] as $item)
                <a href="{{ $item['url'] }}" @class(['nav__link', 'is-active' => $item['active']])>
                    <x-icon :name="$item['icon']" /> {{ $item['text'] }}
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="sidebar__footer">
        <x-dev-credit />
    </div>
</aside>
