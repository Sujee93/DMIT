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

        if ($this->app->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        View::composer('*', LayoutComposer::class);
    }
}
