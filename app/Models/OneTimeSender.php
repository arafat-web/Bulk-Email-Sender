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
        // Derive queued/completed from counters without extra queries.
        if ((int) $this->total_email_address > 0
            && $this->processed_count >= (int) $this->total_email_address
            && ! in_array($this->status, ['completed', 'failed'], true)) {
            $this->status = ((int) $this->failed_count === (int) $this->total_email_address) ? 'failed' : 'completed';
            $this->completed_at = $this->completed_at ?? now();
            $this->saveQuietly();
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

        return round((($this->sent_count + $this->failed_count) / $this->total_email_address) * 100, 2);
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
