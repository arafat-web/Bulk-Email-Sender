<?php

namespace App\Services;

use App\Models\EmailAccount;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

/**
 * Pre-flight checks before a campaign is dispatched: verifies the SMTP
 * account is alive (transport-level AUTH, no email sent), the queue worker
 * path exists (database queue reachable), and the rate limiter is registered.
 * Returns a list of blocking errors — empty means safe to dispatch.
 */
class CampaignPreflight
{
    /**
     * @return string[] blocking error messages (empty = ready to send)
     */
    public static function check(?EmailAccount $account, ?int $recipientCount = null): array
    {
        $errors = [];

        // 1. Account present + active.
        if (! $account) {
            return ['No sending account configured. Add an email account first.'];
        }
        if (! $account->is_active) {
            $errors[] = "Sending account \"{$account->name}\" is deactivated. Activate it first.";
        }

        // 2. SMTP transport check (connect + AUTH, no email sent).
        $smtpError = self::checkSmtp($account);
        if ($smtpError !== null) {
            $errors[] = $smtpError;
        }

        // 3. Queue reachable (database queue) — jobs must have somewhere to go.
        if (config('queue.default') === 'database') {
            try {
                DB::table('jobs')->limit(1)->count();
            } catch (\Throwable $t) {
                $errors[] = 'Queue table unreachable: '.$t->getMessage();
            }

            // 3b. Worker liveness hint: pending jobs older than 15 min with no
            // recent sends suggests no worker is draining (cron missing).
            // Blocking: without a worker the campaign would sit forever.
            try {
                $stale = DB::table('jobs')->where('created_at', '<', now()->subMinutes(15)->toDateTimeString())->count();
                if ($stale > 0) {
                    $recentSend = \App\Models\CampaignRecipient::where('sent_at', '>=', now()->subMinutes(15))->exists();
                    if (! $recentSend) {
                        $errors[] = $stale.' job(s) already waited 15+ min with no sends: the queue worker may not be running. Set the 1-minute schedule:run cron first.';
                    }
                }
            } catch (\Throwable $t) {
                // Advisory only — never blocks.
            }
        }

        // 4. Rate limiter registered (misconfiguration = instant burst or fatal).
        try {
            if (\Illuminate\Support\Facades\RateLimiter::limiter('smtp-account') === null) {
                $errors[] = 'Rate limiter "smtp-account" is not registered — sends would bypass the hourly cap.';
            }
        } catch (\Throwable $t) {
            $errors[] = 'Rate limiter check failed: '.$t->getMessage();
        }

        // 5. Size hint vs drain time at current rate.
        if ($recipientCount && $recipientCount > 0) {
            $perMinute = max(1, (int) config('email_sender.rate_per_minute', 6));
            $hours = $recipientCount / ($perMinute * 60);
            if ($hours > 48) {
                $errors[] = number_format($recipientCount).' recipients at '.$perMinute.'/min need ~'.round($hours, 1).'h — beyond the 48h job window. Split the campaign.';
            }
        }

        return $errors;
    }

    /**
     * Connect + AUTH against the SMTP server without sending any mail.
     * Returns null on success, error string on failure.
     */
    public static function checkSmtp(EmailAccount $account): ?string
    {
        try {
            $scheme = $account->smtp_encryption === 'ssl' ? 'smtps' : 'smtp';
            $transport = new EsmtpTransport(
                (string) $account->smtp_host,
                (int) $account->smtp_port,
                $account->smtp_encryption === 'none' ? false : true
            );
            $transport->setUsername($account->smtp_username ?: $account->email);
            $transport->setPassword((string) $account->smtp_password);

            // start() connects + authenticates; stop() closes cleanly.
            $transport->start();
            $transport->stop();

            return null;
        } catch (\Throwable $t) {
            $msg = $t->getMessage();
            // Strip credentials from the message just in case.
            $msg = str_replace((string) $account->smtp_password, '***', $msg);

            return 'SMTP check failed for "'.$account->name.'" ('.$account->smtp_host.':'.$account->smtp_port.'): '.mb_substr($msg, 0, 300);
        }
    }
}
