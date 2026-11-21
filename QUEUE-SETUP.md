# Queue Setup Guide

## 1. cPanel / Shared Hosting Setup (File-Based Cron — Recommended)

Point your cPanel Cron Job **directly at `schedule.php`** — no `schedule:run` wrapper, no `exec()`/`proc_open()`, and no Supervisor daemon needed. `schedule.php` enforces CLI-only execution (returns `403` over HTTP), boots Laravel in-process, and runs the same jobs as `app/Console/Kernel.php`:

- `queue:work --queue=emails,default --stop-when-empty --max-time=55 --tries=3 --timeout=120 --sleep=3` every minute (drains, then exits; the `smtp-account` limiter governs the `SMTP_RATE_PER_MINUTE` pace)
- `email:process-campaigns --update-status` every 5th minute (flips stale queued/processing campaigns to completed/failed)

A file lock (`storage/framework/schedule.lock`) guarantees only one run at a time; a stale lock (>10 min, e.g. killed process) self-heals.

### cPanel Cron Command (1-minute — best)
In **cPanel → Cron Jobs**, add one job running every minute (`* * * * *`):

```bash
* * * * * /usr/local/bin/php /home/USER/app/schedule.php >> /dev/null 2>&1
```

> **Note:** On some cPanel hosts the PHP 8.3/8.4 CLI binary is `/opt/cpanel/ea-php83/root/usr/bin/php` or `/opt/cpanel/ea-php84/root/usr/bin/php` instead of `/usr/local/bin/php`.
> If your cPanel host restricts crons to every 5 minutes (`*/5 * * * *`), set `QUEUE_WORKER_MAX_TIME=280` in `.env` so each tick drains up to ~4m40s.

### cPanel Checklist
- **PHP version:** 8.3, 8.4, or 8.5 (`zip`, `xml`, `mbstring`, `fileinfo`, `gd`, `pdo_mysql` enabled in *Select PHP Version*).
- **Document root:** Point your domain/subdomain root to `public/` (or place the app outside `public_html/`). Never expose `.env` or `storage/` publicly.
- **No Node.js required:** All UI assets load via CDN.
- **Dry-run test via SSH / Terminal:** `php schedule.php --dry-run`
- **After every deploy:** `php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan queue:restart`

---

## 2. Dedicated Server / VPS Setup (Supervisor + Cron)

On a VPS or dedicated server, run a persistent worker under Supervisor and let Laravel's scheduler handle campaign finalization and log/job cleanup:

1. **Scheduler cron (1 line in `crontab -e`):**
   ```bash
   * * * * * cd /var/www/Bulk-Email-Sender && php artisan schedule:run >> /dev/null 2>&1
   ```
2. **Queue worker (Supervisor — recommended):**
   ```bash
   php artisan queue:work --queue=emails,default --tries=3 --timeout=120 --sleep=3
   ```
   See: https://laravel.com/docs/13.x/queues#supervisor-configuration
3. **Optional scaling:** Switch `CACHE_DRIVER`, `SESSION_DRIVER`, and `QUEUE_CONNECTION` to `redis` and raise `SMTP_RATE_PER_MINUTE` to match your mail provider's limit.

---

## Queue Modes

This application defaults to database queue. Sync mode is kept as a legacy fallback.

### 1. Database Queue Mode (Default - Recommended)
Emails are queued in the database and processed by a background worker.

**Pros:**
- Fast campaign creation
- Better performance for bulk emails
- Automatic retry on failures
- Can handle thousands of emails
- Won't timeout

**Cons:**
- Requires running a queue worker

**Setup:**

1. Update your `.env` file:
```bash
QUEUE_CONNECTION=database
```

2. Run the queue worker in a separate terminal:
```bash
php artisan queue:work --queue=emails,default --tries=3 --timeout=120
```

3. Or run it in the background (production):
```bash
# Using nohup
nohup php artisan queue:work --queue=emails,default --tries=3 --timeout=120 > /dev/null 2>&1 &

# Or use supervisor (recommended for production)
# See: https://laravel.com/docs/13.x/queues#supervisor-configuration
```

---

## Quick Start Commands

### Check Queue Status
```bash
# Count pending jobs
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Start Queue Worker
```bash
# Basic
php artisan queue:work

# With specific queues and settings (recommended)
php artisan queue:work --queue=emails,default --tries=3 --timeout=120 --sleep=3
```

### Stop Queue Worker
Press `Ctrl+C` in the terminal running the queue worker.

---

---

### 2. Sync Mode (Legacy Fallback - No Queue Worker Needed)
Emails are sent immediately when campaigns are created.

**Pros:**
- No queue worker needed
- Simple setup

**Cons:**
- Slower for large campaigns
- Can timeout on huge batches
- No retry mechanism, after_commit not enforced

**Setup:**
```bash
# In .env file
QUEUE_CONNECTION=sync
```

---

## Switching Between Modes

You can switch at any time:

1. **To enable queue (default):**
   - Set `QUEUE_CONNECTION=database` in `.env`
   - Set `CACHE_DRIVER=database` and `SESSION_DRIVER=database` (required for fresh installs / migrations)
   - Run `php artisan queue:work`

2. **To disable queue (legacy):**
   - Set `QUEUE_CONNECTION=sync` in `.env`
   - No worker needed

The application will automatically adapt to the current mode.

---

## Troubleshooting

### Emails not sending?
```bash
# Check if queue worker is running (if using database mode)
ps aux | grep "queue:work"

# Check for failed jobs
php artisan queue:failed

# View logs
tail -f storage/logs/laravel.log
```

### Clear stuck jobs
```bash
# Clear all jobs from queue
php artisan queue:flush

# Restart queue worker
# Press Ctrl+C, then run queue:work again
```

### Monitor in real-time
```bash
# Watch the queue table
php artisan tinker
>>> DB::table('jobs')->count();

# Or watch logs
tail -f storage/logs/laravel.log
```
