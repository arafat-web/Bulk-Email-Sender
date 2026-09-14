<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeSender extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'file_name',
        'total_email_address',
        'subject',
        'body',
        'status',
        'sent_count',
        'failed_count',
        'skipped_count',
        'last_error',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
        'skipped_count' => 'integer',
        'total_email_address' => 'integer',
    ];

    public function recipients()
    {
        return $this->hasMany(CampaignRecipient::class, 'campaign_id');
    }

    public function getProcessedCountAttribute(): int
    {
        return (int) $this->sent_count + (int) $this->failed_count + (int) $this->skipped_count;
    }

    public function getPendingCountAttribute(): int
    {
        return max(0, (int) $this->total_email_address - $this->processed_count);
    }

    public function getIsFinishedAttribute(): bool
    {
        return in_array($this->status, ['completed', 'failed'], true)
            || ($this->total_email_address > 0 && $this->processed_count >= (int) $this->total_email_address);
    }

    public function refreshStatusFromCounters(): void
    {
        // Self-heal: counters can drift above total when retries double-counted
        // (old bug). Reconcile from recipient rows so pending/is_finished stay truthful.
        $this->recalcCountersFromRecipients(true);
        // Derive queued/completed from counters without extra queries.
        if ((int) $this->total_email_address > 0
            && $this->processed_count >= (int) $this->total_email_address
            && ! in_array($this->status, ['completed', 'failed'], true)) {
            $this->status = ((int) $this->failed_count === (int) $this->total_email_address ? 'failed' : 'completed');
            $this->completed_at = $this->completed_at ?? now();
            $this->saveQuietly();
        }
    }

    /**
     * Reconcile sent/failed/skipped counters from campaign_recipients rows.
     * Makes the live tracker self-healing against double-counted retries.
     */
    public function recalcCountersFromRecipients(bool $onlyIfDrifted = true): void
    {
        try {
            $counts = $this->recipients()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status')->toArray();
            $sent = (int) ($counts[\App\Models\CampaignRecipient::STATUS_SENT] ?? 0);
            $failed = (int) ($counts[\App\Models\CampaignRecipient::STATUS_FAILED] ?? 0);
            $skipped = (int) ($counts[\App\Models\CampaignRecipient::STATUS_SKIPPED] ?? 0);
            if ($onlyIfDrifted && $sent === (int) $this->sent_count && $failed === (int) $this->failed_count && $skipped === (int) ($this->skipped_count ?? 0)) {
                return;
            }
            $this->updateQuietly([
                'sent_count' => $sent,
                'failed_count' => $failed,
                'skipped_count' => $skipped,
            ]);
            $this->refresh();
        } catch (\Throwable $t) {
            // Tracker must never break sending.
        }
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'processing' => '<span class="badge bg-warning">Processing</span>',
            'queued' => '<span class="badge bg-info">Queued</span>',
            'completed' => '<span class="badge bg-success">Completed</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_email_address == 0) {
            return 0;
        }

        return min(100, round((($this->sent_count + $this->failed_count + ($this->skipped_count ?? 0)) / $this->total_email_address) * 100, 2));
    }

    /**
     * Sends/hour over the trailing 60 min (for card partials that don't
     * go through CampaignTrackerController::serialize()).
     */
    public function getSendsPerHourAttribute(): int
    {
        try {
            return (int) $this->recipients()
                ->where('status', CampaignRecipient::STATUS_SENT)
                ->where('sent_at', '>=', now()->subHour())
                ->count();
        } catch (\Throwable $t) {
            return 0;
        }
    }

    /**
     * ETA timestamp string (or null) derived from trailing-hour throughput.
     */
    public function getEtaAtAttribute(): ?string
    {
        try {
            $rate = $this->sends_per_hour;
            $pending = $this->pending_count;
            if ($rate <= 0 || $pending <= 0) {
                return null;
            }

            return now()->addMinutes((int) round($pending / $rate * 60))->toDateTimeString();
        } catch (\Throwable $t) {
            return null;
        }
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
