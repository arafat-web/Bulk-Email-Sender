<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OneTimeSender extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'total_email_address',
        'subject',
        'status',
        'sent_count',
        'failed_count',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
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
