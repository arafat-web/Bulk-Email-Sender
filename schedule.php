#!/usr/bin/env php
<?php

/*
|--------------------------------------------------------------------------
| File-Based Cron Scheduler (cPanel friendly)
|--------------------------------------------------------------------------
|
| Point your cron job at THIS file instead of `php artisan schedule:run`:
|
|   * * * * * /usr/bin/php /home/USER/public_html/schedule.php >> /dev/null 2>&1
|
| (Replace /home/USER/public_html with your real app path. On some hosts
|  the PHP binary is /usr/local/bin/php — check cPanel → Cron Jobs docs.)
|
| What it does, every minute:
|   1. queue:work --queue=emails,default --stop-when-empty ...  (drains mail)
|   2. email:process-campaigns --update-status  (every 5th minute, finalizes)
|
| It mirrors app/Console/Kernel.php, but needs NO scheduler daemon and no
| `schedule:run` wrapper. A file lock (storage/framework/schedule.lock)
| guarantees only one run at a time; a stale lock (>10 min) self-heals.
|
| Tip: run `php schedule.php --dry-run` once via SSH to preview the
| commands without executing them.
|
*/

use Illuminate\Contracts\Console\Kernel;

$dryRun = in_array('--dry-run', $argv ?? [], true);

// --- File lock: skip if previous tick still running -------------------------
$lockPath = __DIR__.'/storage/framework/schedule.lock';

$lock = @fopen($lockPath, 'c+');
if ($lock === false) {
    fwrite(STDERR, '[schedule.php] Cannot open lock file: '.$lockPath.PHP_EOL);
    exit(1);
}

if (! flock($lock, LOCK_EX | LOCK_NB)) {
    $age = time() - (int) @filemtime($lockPath);
    if ($age > 600) {
        // Previous run died (killed process, fatal). Remove + exit so the
        // NEXT minute tick drains; this tick just reports the stale lock.
        flock($lock, LOCK_UN);
        fclose($lock);
        @unlink($lockPath);
        fwrite(STDERR, "[schedule.php] Removed stale lock ({$age}s old). Next tick will drain.".PHP_EOL);
        exit(0);
    }
    fwrite(STDERR, '[schedule.php] Previous tick still running — skipping.'.PHP_EOL);
    fclose($lock);
    exit(0);
}

ftruncate($lock, 0);
fwrite($lock, (string) time());
fflush($lock);

// --- Boot Laravel ------------------------------------------------------------
define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

// --- 1. Drain pending mail jobs (mirrors Kernel.php schedule) ----------------
$drain = [
    'command' => 'queue:work',
    'params' => [
        '--queue' => 'emails,default',
        '--stop-when-empty' => true,
        '--max-time' => (int) config('email_sender.worker_max_time', 55),
        '--tries' => 3,
        '--timeout' => 120,
        '--sleep' => 3,
    ],
];

// --- 2. Finalize campaigns every 5th minute -----------------------------------
// Cron has no "everyFiveMinutes" helper, so gate on the clock: minutes
// 0,5,10,... run the finalizer after the drain (same as Kernel.php).
$nowMinute = (int) date('i');
$finalize = ($nowMinute % 5 === 0);

if ($dryRun) {
    echo '[dry-run] Would call: queue:work '.http_build_query($drain['params'], '', ' ').PHP_EOL;
    echo '[dry-run] Finalize tick (minute '.$nowMinute.'): '.($finalize ? 'YES' : 'no').PHP_EOL;
    flock($lock, LOCK_UN);
    fclose($lock);
    exit(0);
}

$exit = 0;

try {
    $exit = $kernel->call($drain['command'], $drain['params']);
    echo $kernel->output().PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, '[schedule.php] queue:work failed: '.$e->getMessage().PHP_EOL);
    $exit = 1;
}

if ($finalize) {
    try {
        $code = $kernel->call('email:process-campaigns', ['--update-status' => true]);
        echo $kernel->output().PHP_EOL;
        $exit = $exit ?: $code;
    } catch (Throwable $e) {
        fwrite(STDERR, '[schedule.php] email:process-campaigns failed: '.$e->getMessage().PHP_EOL);
        $exit = 1;
    }
}

// NOTE: queue:failed:purge (daily prune from Kernel.php) is intentionally
// left out of the per-minute file — run it manually once a month:
//   php artisan queue:failed:purge --older-than=30

$kernel->terminate(
    new Symfony\Component\Console\Input\ArgvInput,
    $exit
);

flock($lock, LOCK_UN);
fclose($lock);

exit($exit);
