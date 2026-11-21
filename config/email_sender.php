<?php

return [
    /*
     | Per-minute SMTP send ceiling for the `smtp-account` rate limiter.
     | Host cap is 400/hour (~6.6/min); 6/min => 360/hour stays safely under.
     | Read via config() (NOT env()) so it survives `php artisan config:cache`.
     */
    'rate_per_minute' => (int) env('SMTP_RATE_PER_MINUTE', 6),

    /* Seconds a single send may block before failing (guards dead SMTP). */
    'mail_timeout' => (int) env('MAIL_TIMEOUT', 30),

    /* Worker --max-time per cron tick (seconds, must be < 60). */
    'worker_max_time' => (int) env('QUEUE_WORKER_MAX_TIME', 55),
];
