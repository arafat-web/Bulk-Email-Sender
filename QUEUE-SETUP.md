# Queue Setup Guide

## Queue Modes

This application supports two queue modes:

### 1. Sync Mode (Default - No Queue Worker Needed)
Emails are sent immediately when campaigns are created.

**Pros:**
- No queue worker needed
- Simple setup
- Works immediately

**Cons:**
- Slower for large campaigns
- Can timeout on huge batches
- No retry mechanism

**Setup:**
```bash
# In .env file
QUEUE_CONNECTION=sync
```

No additional commands needed. Just create campaigns and emails will send immediately.

---

### 2. Database Queue Mode (Recommended for Production)
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
# See: https://laravel.com/docs/10.x/queues#supervisor-configuration
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

## Switching Between Modes

You can switch at any time:

1. **To enable queue (recommended):**
   - Set `QUEUE_CONNECTION=database` in `.env`
   - Run `php artisan queue:work`

2. **To disable queue (instant mode):**
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
