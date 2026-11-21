<?php

namespace App\Console\Commands;

use App\Models\OneTimeSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PurgeFailedJobs extends Command
{
    protected $signature = 'queue:failed:purge {--older-than=30 : Delete failed jobs older than N days} {--all : Delete ALL failed jobs}';

    protected $description = 'Prune old rows from failed_jobs table';

    public function handle(): int
    {
        if ($this->option('all')) {
            if (! $this->confirm('Delete ALL rows from failed_jobs?')) {
                return self::SUCCESS;
            }
            $count = DB::table('failed_jobs')->count();
            DB::table('failed_jobs')->truncate();
            $this->info("Deleted {$count} failed job(s).");

            return self::SUCCESS;
        }

        $days = (int) $this->option('older-than');
        $cutoff = now()->subDays($days)->toDateTimeString();
        $count = DB::table('failed_jobs')->where('failed_at', '<', $cutoff)->delete();
        $this->info("Deleted {$count} failed job(s) older than {$days} days.");

        // Mark recovered campaigns complete so they don't poll forever.
        foreach (OneTimeSender::where('type', 'recovered')->whereNotIn('status', ['completed', 'failed'])->get() as $c) {
            $c->update(['status' => 'failed', 'completed_at' => $c->completed_at ?? now()]);
        }

        return self::SUCCESS;
    }
}
