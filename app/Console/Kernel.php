<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * SiteGround has no daemon/supervisor, so the queue drains via cron:
     *   * * * * * cd /path/to/Bulk-Email-Sender && php artisan schedule:run >> /dev/null 2>&1
     * The queue:work invocation below runs --stop-when-empty each minute,
     * so at most one worker exists at a time and the smtp-account rate
     * limiter governs throughput (default 6/min ≈ 360/hr, see SMTP_RATE_PER_MINUTE).
     */
    protected function schedule(Schedule $schedule): void
    {
        // Drain pending mail jobs. withoutOverlapping guards double-runs when
        // a previous drain overruns the 1-minute cron tick.
        $schedule->command('queue:work --queue=emails,default --stop-when-empty --max-time=55 --tries=3 --timeout=120 --sleep=3')
            ->everyMinute()
            ->withoutOverlapping(10)
            ->runInBackground();

        // Finalize campaigns whose counters reached total but status never
        // flipped (e.g. worker killed mid-batch). Runs AFTER the drain tick.
        $schedule->command('email:process-campaigns --update-status')
            ->everyFiveMinutes()
            ->withoutOverlapping(10)
            ->runInBackground();

        // Keep failed_jobs from growing unbounded (tracker sync preserves history).
        $schedule->command('queue:failed:purge --older-than=30')
            ->daily()
            ->withoutOverlapping(10);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
