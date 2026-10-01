<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@yield('title', 'Dashboard') · {{ $business->name }}</title>
</head>
<body>
<div class="app">
    @include('partials.sidebar')
    <div class="backdrop" data-sidebar-backdrop></div>

    <div class="main">
        <header class="topbar">
            <div class="topbar__left">
                <button type="button" class="btn btn-ghost btn-icon menu-toggle" data-sidebar-toggle aria-label="Open menu">
                    <x-icon name="menu" />
                </button>
                <span class="topbar__date">{{ now()->format('l, d F Y') }}</span>
            </div>

            <div class="topbar__right">
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> <span>New Invoice</span></a>

            <div class="user-menu">
                <button type="button" class="user-menu__button" data-dropdown-toggle aria-haspopup="true" aria-expanded="false">
                    <span class="avatar">{{ auth()->user()->initials() }}</span>
                    <span class="user-menu__text">
                        <span class="user-menu__name">{{ auth()->user()->name }}</span><br>
                        <span class="user-menu__role">{{ auth()->user()->role->label() }}</span>
                    </span>
                </button>
                <div class="dropdown" data-dropdown>
                    @can('admin')
                        <a href="{{ route('settings.edit') }}"><x-icon name="cog" /> Business settings</a>
                        <a href="{{ route('users.index') }}"><x-icon name="shield" /> System users</a>
                    @endcan
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"><x-icon name="logout" /> Sign out</button>
                    </form>
                </div>
            </div>
            </div>
        </header>

        <main class="content">
            <x-alerts />
            @yield('content')
        </main>

        <footer class="app-footer">
            <span>&copy; {{ date('Y') }} {{ $business->name }}</span>
            <x-dev-credit />
        </footer>
    </div>
</div>
</body>
</html>
