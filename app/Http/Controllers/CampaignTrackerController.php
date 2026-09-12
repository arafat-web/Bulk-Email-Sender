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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'campaigns' => $campaigns->map(fn ($c) => $this->serialize($c))->values(),
                'pagination' => [
                    'current_page' => $campaigns->currentPage(),
                    'last_page' => $campaigns->lastPage(),
                    'total' => $campaigns->total(),
                ],
            ]);
        }

        return view('campaigns.index', compact('campaigns'));
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
        try {
            if (config('queue.default') !== 'database') {
                return 0;
            }

            return \Illuminate\Support\Facades\DB::table('jobs')->count()
                + \Illuminate\Support\Facades\DB::table('job_batches')->whereNull('finished_at')->count();
        } catch (\Throwable $e) {
            return 0;
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
