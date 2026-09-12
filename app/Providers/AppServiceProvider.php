<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // App uses Bootstrap 5 markup — render paginator with Bootstrap views
        // (default Tailwind views render unstyled/huge without Tailwind CSS).
        Paginator::useBootstrapFive();

        // Force HTTPS URLs only when the app is actually served over HTTPS.
        // Local HTTP (APP_URL=http://localhost) is untouched, so this
        // never breaks local dev. Skipped during unit tests to keep
        // assertions on plain HTTP URLs stable.
        if (! $this->app->runningUnitTests()
            && (str_starts_with((string) config('app.url'), 'https://')
                || $this->app->environment('production'))) {
            URL::forceScheme('https');
        }
    }
}
