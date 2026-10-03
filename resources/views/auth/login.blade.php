@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-illustration">
            @include('partials.login-illustration')
        </div>

        <div class="auth-form">
            <div class="auth-form__logo">
                @if ($business->logoUrl())
                    <img src="{{ $business->logoUrl() }}" alt="{{ $business->name }}">
                @else
                    <img src="{{ asset('images/edzstudio-logo.png') }}" alt="EdzStudio">
                @endif
            </div>
            <h1>Login to {{ $business->name }}</h1>

            @if ($errors->any() || session('error'))
                <div class="alert alert-error" role="alert">
                    <x-icon name="alert" />
                    <div>{{ $errors->first() ?: session('error') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" class="input" value="{{ old('email') }}"
                           placeholder="Please type your email" autocomplete="username" required autofocus maxlength="255">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" class="input"
                           placeholder="Please type your password" autocomplete="current-password" required maxlength="255">
                </div>
                <div class="auth-form__remember">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        <span>Remember Me</span>
                    </label>
                </div>
                <div class="auth-form__submit">
                    <button type="submit" class="btn btn-primary btn-lg">LOGIN</button>
                </div>
            </form>
        </div>
    </div>

    <div class="auth-footer"><x-dev-credit /></div>
</div>
@endsection
