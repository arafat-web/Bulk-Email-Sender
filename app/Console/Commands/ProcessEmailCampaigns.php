<?php

namespace App\Console\Commands;

use App\Models\OneTimeSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessEmailCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-campaigns {--update-status : Update campaign statuses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and monitor email campaigns status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing email campaigns...');

        if ($this->option('update-status')) {
            $this->updateCampaignStatuses();
        }

        $this->showCampaignStats();

        return Command::SUCCESS;
    }

    private function updateCampaignStatuses()
    {
        $campaigns = OneTimeSender::whereIn('status', ['processing', 'queued'])->get();

        foreach ($campaigns as $campaign) {
            $progress = $campaign->sent_count + $campaign->failed_count;

            if ($progress >= $campaign->total_email_address) {
                $campaign->update([
                    'status' => $campaign->failed_count > 0 ? 'completed' : 'completed',
                    'completed_at' => now()
                ]);
            }
        }

        $this->info('Campaign statuses updated.');
    }

    private function showCampaignStats()
    {
        $stats = [
            'Total Campaigns' => OneTimeSender::count(),
            'Processing' => OneTimeSender::where('status', 'processing')->count(),
            'Queued' => OneTimeSender::where('status', 'queued')->count(),
            'Completed' => OneTimeSender::where('status', 'completed')->count(),
            'Failed' => OneTimeSender::where('status', 'failed')->count(),
        ];

        $this->table(['Metric', 'Count'], collect($stats)->map(fn($value, $key) => [$key, $value])->toArray());

        // Show recent campaigns
        $recent = OneTimeSender::latest()->take(5)->get();

        if ($recent->count() > 0) {
            $this->info("\nRecent Campaigns:");
            $this->table(
                ['ID', 'Subject', 'Total', 'Sent', 'Failed', 'Status', 'Created'],
                $recent->map(fn($campaign) => [
                    $campaign->id,
                    \Str::limit($campaign->subject ?? 'No subject', 30),
                    $campaign->total_email_address,
                    $campaign->sent_count,
                    $campaign->failed_count,
                    $campaign->status,
                    $campaign->created_at->format('M d, H:i')
                ])->toArray()
            );
        }
    }
}
