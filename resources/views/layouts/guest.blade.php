<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    <title>@yield('title', 'Sign in') · {{ $business->name }}</title>
</head>
<body>
    @yield('content')
</body>
</html>
