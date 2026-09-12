<?php

namespace App\Console\Commands;

use App\Models\CampaignRecipient;
use App\Models\OneTimeSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTrackerFromFailedJobs extends Command
{
    protected $signature = 'tracker:sync-failed {--limit=5000 : Max failed_jobs rows to scan} {--prune : Delete synced rows from failed_jobs after import}';

    protected $description = 'Backfill realtime tracker (campaign_recipients + counters) from orphaned failed_jobs rows';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $rows = DB::table('failed_jobs')->orderBy('id')->limit($limit)->get();

        if ($rows->isEmpty()) {
            $this->info('No failed jobs to sync.');

            return self::SUCCESS;
        }

        $synced = 0;
        $orphans = [];
        $syncedIds = [];

        foreach ($rows as $row) {
            $parsed = $this->parseRow($row);
            if (! $parsed['email'] && ! $parsed['campaign_id']) {
                continue;
            }

            $campaign = null;
            if ($parsed['campaign_id']) {
                $campaign = OneTimeSender::find($parsed['campaign_id']);
            }

            // Orphaned failure (campaign deleted or never tracked): group them
            // under a single recovery campaign so the tracker can display them.
            if (! $campaign) {
                $key = ($parsed['subject'] ?: 'recovered-failures').'|'.($parsed['campaign_id'] ?? 0);
                if (! isset($orphans[$key])) {
                    $orphans[$key] = OneTimeSender::create([
                        'type' => 'recovered',
                        'file_name' => 'recovered-from-failed-jobs',
                        'total_email_address' => 0,
                        'subject' => $parsed['subject'] ?: 'Recovered failures (campaign #'.($parsed['campaign_id'] ?? '?').')',
                        'body' => '',
                        'status' => 'failed',
                        'started_at' => now(),
                    ]);
                }
                $campaign = $orphans[$key];
            }

            $email = strtolower(trim((string) $parsed['email']));
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $existing = CampaignRecipient::where('campaign_id', $campaign->id)
                ->where('email', $email)
                ->first();

            if (! $existing) {
                CampaignRecipient::create([
                    'campaign_id' => $campaign->id,
                    'email' => $email,
                    'status' => CampaignRecipient::STATUS_FAILED,
                    'attempts' => $parsed['attempts'] ?? 3,
                    'error' => substr($parsed['error'] ?? 'Failed (from failed_jobs)', 0, 1000),
                ]);
                $campaign->increment('failed_count');
                $campaign->increment('total_email_address');
            } elseif ($existing->status !== CampaignRecipient::STATUS_FAILED) {
                $existing->update([
                    'status' => CampaignRecipient::STATUS_FAILED,
                    'error' => substr($parsed['error'] ?? 'Failed (from failed_jobs)', 0, 1000),
                ]);
                $campaign->increment('failed_count');
            }

            if (empty($campaign->last_error) && $parsed['error']) {
                $campaign->update(['last_error' => substr($parsed['error'], 0, 1000)]);
            }

            $synced++;
            $syncedIds[] = $row->id;
        }

        // Recompute status on touched campaigns.
        $touched = OneTimeSender::whereIn('id', array_unique(array_merge(
            array_map(fn ($c) => $c->id, array_values($orphans)),
            CampaignRecipient::whereIn('id', function () use ($syncedIds) {
                // no-op: just refresh orphans + any campaign with new failures
            })->pluck('campaign_id')->toArray()
        )))->get();
        foreach ($touched as $campaign) {
            $campaign->refresh();
            $campaign->refreshStatusFromCounters();
        }
        foreach (OneTimeSender::where('type', 'recovered')->get() as $campaign) {
            $campaign->refresh();
            // Recovered campaigns are historical failures — always mark failed/complete.
            if ($campaign->status !== 'failed') {
                $campaign->update(['status' => 'failed', 'completed_at' => $campaign->completed_at ?? now()]);
            }
        }

        if ($this->option('prune') && ! empty($syncedIds)) {
            DB::table('failed_jobs')->whereIn('id', $syncedIds)->delete();
            $this->warn('Pruned '.count($syncedIds).' rows from failed_jobs.');
        }

        $this->info("Synced {$synced} failed job(s) into tracker.");

        return self::SUCCESS;
    }

    /**
     * Parse one failed_jobs row -> email, campaign_id, subject, error, attempts.
     */
    public function parseRow(object $row): array
    {
        $email = null;
        $campaignId = null;
        $subject = null;
        $attempts = 3;
        $error = null;

        try {
            $payload = json_decode($row->payload ?? '', true);
            $command = $payload['data']['command'] ?? null;
            if ($command) {
                $job = unserialize($command);
                $ref = new \ReflectionObject($job);
                foreach (['email', 'mailData', 'recipients', 'subject'] as $prop) {
                    if (! $ref->hasProperty($prop)) {
                        continue;
                    }
                    $p = $ref->getProperty($prop);
                    $p->setAccessible(true);
                    $val = $p->getValue($job);
                    if ($prop === 'email' && is_string($val)) {
                        $email = $val;
                    }
                    if ($prop === 'recipients') {
                        $email = is_array($val) ? ($val[0] ?? null) : $val;
                    }
                    if ($prop === 'mailData' && is_array($val)) {
                        $campaignId = $val['campaign_id'] ?? null;
                        $subject = $val['subject'] ?? null;
                    }
                    if ($prop === 'subject' && is_string($val)) {
                        $subject = $subject ?? $val;
                    }
                }
                // Attempts: job may carry attemptsMade — fall back to tries.
                if ($ref->hasProperty('tries')) {
                    $tp = $ref->getProperty('tries');
                    $tp->setAccessible(true);
                    $attempts = (int) ($tp->getValue($job) ?: 3);
                }
            }
        } catch (\Throwable $e) {
            // fall through to regex fallback
        }

        // Regex fallback on raw payload (works even if class changed).
        if (! $email && isset($row->payload)) {
            if (preg_match('/"email"\s*;\s*s:\d+:"([^"]+@[^"]+)"/', $row->payload, $m)) {
                $email = $m[1];
            } elseif (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $row->payload, $m)) {
                // Avoid matching SMTP hostnames — prefer the SendEmailJob email prop.
                $email = $m[0];
            }
        }
        if (! $campaignId && isset($row->payload)) {
            if (preg_match('/"campaign_id"\s*;\s*i:(\d+)/', $row->payload, $m)) {
                $campaignId = (int) $m[1];
            }
        }

        // First line of exception is the human-readable error.
        if (isset($row->exception)) {
            $error = trim(strtok($row->exception, "\n"));
            // Strip leading "Exception: " noise.
            $error = preg_replace('/^[A-Za-z0-9_\\\\]+(Exception|Error):\s*/', '', $error);
        }

        return [
            'email' => $email,
            'campaign_id' => $campaignId ? (int) $campaignId : null,
            'subject' => $subject,
            'attempts' => $attempts,
            'error' => $error,
        ];
    }
}
