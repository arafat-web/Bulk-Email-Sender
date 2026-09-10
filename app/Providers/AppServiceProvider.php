<?php

namespace App\Providers;

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
