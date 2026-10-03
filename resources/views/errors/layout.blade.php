{{-- Standalone error page: must not depend on the database or on being logged in. --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') · @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-page">
    <div class="card error-card">
        <div class="error-card__code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p class="text-muted">@yield('message')</p>
        @hasSection('reference')
            <p class="error-card__ref">Error code: <strong class="mono">@yield('reference')</strong></p>
        @endif
        <div class="form-actions error-card__actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="btn btn-secondary">Go back</a>
            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
        </div>
    </div>
    <div class="auth-footer"><x-dev-credit /></div>
</div>
</body>
</html>
