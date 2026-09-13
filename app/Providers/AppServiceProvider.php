<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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
        // Named limiter for SMTP throttling (Laravel 10: RateLimited takes
        // only a limiter name — no allow()/everyMinute()/releaseAfter()).
        // Host cap 400/hr ≈ 6/min; configurable via SMTP_RATE_PER_MINUTE.
        RateLimiter::for('smtp-account', function ($job) {
            $accountId = 'default';
            try {
                if (is_object($job) && method_exists($job, 'getEmailAccountId')) {
                    $accountId = $job->getEmailAccountId() ?? 'default';
                }
            } catch (\Throwable $t) {
                // Fall back to shared bucket — throttling must never throw.
            }

            return Limit::perMinute((int) env('SMTP_RATE_PER_MINUTE', 6))->by((string) $accountId);
        });

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
