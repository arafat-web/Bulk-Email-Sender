<?php

namespace App\Http\Controllers;

use App\Models\CampaignRecipient;
use App\Models\OneTimeSender;
use Illuminate\Http\Request;

class CampaignTrackerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tracker index — list of campaigns with live counters.
     * Also surfaces queue-level failures (failed_jobs / pending jobs)
     * so pre-tracker failures orphaned in the queue are visible.
     */
    public function index(Request $request)
    {
        $query = OneTimeSender::query()->latest();

        if ($request->filled('status') && in_array($request->status, ['processing', 'queued', 'completed', 'failed'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('subject', 'like', "%{$q}%")
                    ->orWhere('file_name', 'like', "%{$q}%");
            });
        }

        $campaigns = $query->paginate(15);
        $campaigns->withQueryString();

        // Prime derived attributes for the view.
        foreach ($campaigns as $campaign) {
            $campaign->refreshStatusFromCounters();
        }

        $queueHealth = $this->queueHealth();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'campaigns' => $campaigns->map(fn ($c) => $this->serialize($c))->values(),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'total' => $campaigns->total(),
                ],
                'queue' => $queueHealth,
            ]);
        }

        return view('campaigns.index', compact('campaigns') + ['queueHealth' => $queueHealth]);
    }

    /**
     * Live detail page for one campaign.
     */
    public function show(OneTimeSender $campaign, Request $request)
    {
        $campaign->refreshStatusFromCounters();

        $status = $request->get('status');
        $search = trim((string) $request->get('q', ''));

        $recipients = CampaignRecipient::forCampaign($campaign->id)
            ->when($status && in_array($status, ['queued', 'sent', 'failed', 'skipped'], true), fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where('email', 'like', "%{$search}%"))
            ->latest()
            ->paginate(50);
        $recipients->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($this->payload($campaign, $request));
        }

        return view('campaigns.show', compact('campaign', 'recipients', 'status', 'search'));
    }

    /**
     * Lightweight polling endpoint: counters + queue depth.
     * GET /campaigns/{campaign}/live
     */
    public function live(OneTimeSender $campaign)
    {
        $campaign->refresh();
        $campaign->refreshStatusFromCounters();

        $queuedJobs = $this->pendingJobsCount();

        return response()->json([
            'campaign' => $this->serialize($campaign),
            'queue' => [
                'pending_jobs' => $queuedJobs,
            ],
        ]);
    }

    /**
     * JSON for the detail-page poller (counters + recent failures).
     * GET /campaigns/{campaign}/feed?status=&q=&page=
     */
    public function feed(OneTimeSender $campaign, Request $request)
    {
        return response()->json($this->payload($campaign, $request));
    }

    /**
     * Retry all failed recipients of a campaign.
     * POST /campaigns/{campaign}/retry-failed
     */
    public function retryFailed(OneTimeSender $campaign)
    {
        $failed = CampaignRecipient::forCampaign($campaign->id)
            ->withStatus(CampaignRecipient::STATUS_FAILED)
            ->get();

        if ($failed->isEmpty()) {
            return back()->with('error', 'No failed recipients to retry.');
        }

        $mailData = [
            'subject' => $campaign->subject,
            'body' => $this->resolveCampaignBody($campaign),
            'email_account_id' => $this->resolveCampaignAccountId($campaign),
            'campaign_id' => $campaign->id,
        ];

        $useDelay = config('queue.default') !== 'sync';
        $count = 0;

        foreach ($failed as $recipient) {
            $recipient->update(['status' => CampaignRecipient::STATUS_QUEUED, 'error' => null]);
            $job = \App\Jobs\SendEmailJob::dispatch($recipient->email, $mailData);
            if ($useDelay) {
                $job->onQueue('emails')->delay(rand(1, 5));
            }
            $count++;
        }

        // Re-open the campaign so the tracker shows it as active again.
        $campaign->update([
            'status' => 'queued',
            'completed_at' => null,
            'failed_count' => max(0, (int) $campaign->failed_count - $count),
        ]);

        return back()->with('message', "Re-queued {$count} failed recipient(s) for delivery.");
    }

    /**
     * Queue-failures view: reads failed_jobs directly so orphaned failures
     * (sent before the tracker existed) are visible + retryable.
     * GET /campaigns/failures
     */
    public function failures(Request $request)
    {
        $search = trim((string) $request->get('q', ''));
        $perPage = 50;

        $query = \Illuminate\Support\Facades\DB::table('failed_jobs')->orderByDesc('id');
        if ($search !== '') {
            $query->where('payload', 'like', "%{$search}%");
        }
        $total = (clone $query)->count();
        $rows = $query->paginate($perPage);
        $rows->withQueryString();

        // Parse rows for display (email, campaign, error).
        $parser = new \App\Console\Commands\SyncTrackerFromFailedJobs;
        $items = [];
        foreach ($rows as $row) {
            $parsed = $parser->parseRow($row);
            $items[] = [
                'id' => $row->id,
                'uuid' => $row->uuid,
                'email' => $parsed['email'] ?? '(unknown)',
                'campaign_id' => $parsed['campaign_id'],
                'subject' => $parsed['subject'],
                'error' => $parsed['error'],
                'failed_at' => $row->failed_at,
                'campaign_url' => $parsed['campaign_id'] ? route('campaigns.show', $parsed['campaign_id']) : null,
            ];
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'items' => $items,
                'pagination' => [
                    'current_page' => $rows->currentPage(),
                    'last_page' => $rows->lastPage(),
                    'total' => $rows->total(),
                ],
                'queue' => $this->queueHealth(),
            ]);
        }

        $queueHealth = $this->queueHealth();

        return view('campaigns.failures', [
            'items' => $items,
            'rows' => $rows,
            'search' => $search,
            'total' => $total,
            'queueHealth' => $queueHealth,
        ]);
    }

    /**
     * Retry ALL failed_jobs (re-dispatch original job payloads).
     * POST /campaigns/failures/retry-all
     */
    public function retryAllFailures()
    {
        $count = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        if ($count === 0) {
            return back()->with('error', 'No failed jobs to retry.');
        }

        \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => 'all']);

        return back()->with('message', "Re-queued {$count} failed job(s). Watch them in Live Tracker.");
    }

    /**
     * Forget (delete) ALL failed_jobs.
     * POST /campaigns/failures/forget-all
     */
    public function forgetAllFailures()
    {
        $count = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
        \Illuminate\Support\Facades\Artisan::call('queue:flush');

        return back()->with('message', "Cleared {$count} failed job(s) from the queue.");
    }

    /**
     * Import orphaned failed_jobs into tracker campaigns.
     * POST /campaigns/failures/sync
     */
    public function syncFailures()
    {
        \Illuminate\Support\Facades\Artisan::call('tracker:sync-failed');

        return back()->with('message', trim(\Illuminate\Support\Facades\Artisan::output()) ?: 'Failed jobs synced into tracker.');
    }

    // ── helpers ──────────────────────────────────────────────

    private function payload(OneTimeSender $campaign, Request $request): array
    {
        $campaign->refresh();
        $campaign->refreshStatusFromCounters();

        $status = $request->get('status');
        $search = trim((string) $request->get('q', ''));

        $base = CampaignRecipient::forCampaign($campaign->id);
        $statusCounts = (clone $base)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->toArray();

        $recipients = (clone $base)
            ->when($status && in_array($status, ['queued', 'sent', 'failed', 'skipped'], true), fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where('email', 'like', "%{$search}%"))
            ->latest()
            ->paginate(50);

        $recentFailures = CampaignRecipient::forCampaign($campaign->id)
            ->withStatus(CampaignRecipient::STATUS_FAILED)
            ->latest()
            ->take(10)
            ->get(['email', 'error', 'attempts', 'updated_at']);

        return [
            'campaign' => $this->serialize($campaign, $statusCounts),
            'recipients' => $recipients->getCollection()->map(fn ($r) => [
                'id' => $r->id,
                'email' => $r->email,
                'status' => $r->status,
                'attempts' => $r->attempts,
                'error' => $r->error,
                'sent_at' => $r->sent_at?->toDateTimeString(),
                'updated_at' => $r->updated_at?->toDateTimeString(),
            ])->values(),
            'pagination' => [
                'current_page' => $recipients->currentPage(),
                'last_page' => $recipients->lastPage(),
                'total' => $recipients->total(),
            ],
            'recent_failures' => $recentFailures,
            'queue' => ['pending_jobs' => $this->pendingJobsCount()],
        ];
    }

    private function serialize(OneTimeSender $campaign, ?array $statusCounts = null): array
    {
        $total = (int) $campaign->total_email_address;
        $sent = (int) $campaign->sent_count;
        $failed = (int) $campaign->failed_count;
        $skipped = (int) ($campaign->skipped_count ?? 0);
        $processed = $sent + $failed + $skipped;
        $pending = max(0, $total - $processed);
        $progress = $total > 0 ? round(($processed / $total) * 100, 1) : 0;

        return [
            'id' => $campaign->id,
            'subject' => $campaign->subject,
            'file_name' => $campaign->file_name,
            'type' => $campaign->type ?? 'instant',
            'status' => $campaign->status,
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'skipped' => $skipped,
            'pending' => $pending,
            'processed' => $processed,
            'progress' => $progress,
            'last_error' => $campaign->last_error,
            'is_finished' => (bool) $campaign->is_finished,
            'status_counts' => $statusCounts,
            'created_at' => $campaign->created_at?->toDateTimeString(),
            'updated_at' => $campaign->updated_at?->toDateTimeString(),
            'completed_at' => $campaign->completed_at?->toDateTimeString(),
            'show_url' => route('campaigns.show', $campaign),
            'live_url' => route('campaigns.live', $campaign),
            'feed_url' => route('campaigns.feed', $campaign),
        ];
    }

    private function pendingJobsCount(): int
    {
        return $this->queueHealth()['pending_jobs'] ?? 0;
    }

    /**
     * Queue-level health: pending jobs, failed jobs, recent failure sample.
     * This is what makes pre-tracker failures visible in the UI.
     */
    private function queueHealth(): array
    {
        try {
            if (config('queue.default') !== 'database') {
                return ['pending_jobs' => 0, 'failed_jobs' => 0, 'recent_errors' => []];
            }

            $db = \Illuminate\Support\Facades\DB::class;
            $pending = $db::table('jobs')->count();
            $failed = $db::table('failed_jobs')->count();

            $recent = $db::table('failed_jobs')->orderByDesc('id')->limit(5)->get(['id', 'exception', 'failed_at'])
                ->map(function ($row) {
                    $firstLine = trim(strtok($row->exception ?? '', "\n"));
                    $firstLine = preg_replace('/^[A-Za-z0-9_\\\\]+(Exception|Error):\s*/', '', $firstLine);

                    return [
                        'id' => $row->id,
                        'error' => mb_substr($firstLine, 0, 160),
                        'failed_at' => $row->failed_at,
                    ];
                })->toArray();

            return ['pending_jobs' => $pending, 'failed_jobs' => $failed, 'recent_errors' => $recent];
        } catch (\Throwable $e) {
            return ['pending_jobs' => 0, 'failed_jobs' => 0, 'recent_errors' => []];
        }
    }

    private function resolveCampaignBody(OneTimeSender $campaign): string
    {
        // Body is stored on the campaign at creation so failed recipients
        // can be retried with identical content.
        return (string) ($campaign->body ?? '');
    }

    private function resolveCampaignAccountId(OneTimeSender $campaign): ?int
    {
        try {
            return \App\Models\EmailAccount::getDefault()?->id;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
