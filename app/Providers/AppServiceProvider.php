<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\View\Composers\LayoutComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BusinessSetting::class.'@current', fn () => BusinessSetting::resolve());
    }

    public function boot(): void
    {
        // Surface N+1 queries, silently discarded attributes and typos while developing.
        Model::shouldBeStrict(! $this->app->isProduction());

        Paginator::defaultView('components.pagination');

        Password::defaults(fn () => Password::min(8)->letters()->numbers());

        // Only force https:// links when explicitly enabled (e.g. behind a proxy that
        // terminates SSL). Forcing it on a plain-http site breaks every CSS/JS/image link.
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        // Secure-only session cookie whenever the site is actually served over HTTPS,
        // unless SESSION_SECURE_COOKIE is set explicitly.
        if (config('session.secure') === null && ! $this->app->runningInConsole()) {
            config(['session.secure' => request()->isSecure()]);
        }

        View::composer('*', LayoutComposer::class);
    }
}
