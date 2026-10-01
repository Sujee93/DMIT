@php
    $nav = [
        ['label' => 'Overview', 'items' => [
            ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'home', 'text' => 'Dashboard'],
        ]],
        ['label' => 'Sales', 'items' => [
            ['route' => 'invoices.index', 'match' => 'invoices.*', 'icon' => 'document', 'text' => 'Invoices'],
            ['route' => 'supplier-payments.index', 'match' => 'supplier-payments.*', 'icon' => 'cash', 'text' => 'Supplier Payments'],
        ]],
        ['label' => 'Catalogue', 'items' => [
            ['route' => 'products.index', 'match' => 'products.*', 'icon' => 'cube', 'text' => 'Products'],
            ['route' => 'contacts.index', 'match' => 'contacts.*', 'icon' => 'users', 'text' => 'Customers & Suppliers'],
        ]],
        ['label' => 'Insights', 'items' => [
            ['route' => 'reports.index', 'match' => 'reports.*', 'icon' => 'chart', 'text' => 'Reports'],
        ]],
    ];
    if (auth()->user()->can('admin')) {
        $nav[] = ['label' => 'Administration', 'items' => [
            ['route' => 'settings.edit', 'match' => 'settings.*', 'icon' => 'cog', 'text' => 'Business Settings'],
            ['route' => 'users.index', 'match' => 'users.*', 'icon' => 'shield', 'text' => 'System Users'],
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
                <a href="{{ route($item['route']) }}" @class(['nav__link', 'is-active' => request()->routeIs($item['match'])])>
                    <x-icon :name="$item['icon']" /> {{ $item['text'] }}
                </a>
            @endforeach
        @endforeach
    </nav>

    <div class="sidebar__footer">
        <x-dev-credit />
    </div>
</aside>
