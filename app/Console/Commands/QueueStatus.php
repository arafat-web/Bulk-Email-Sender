<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display current queue status and pending jobs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queueConnection = config('queue.default');
        
        $this->info('═══════════════════════════════════════════════');
        $this->info('   QUEUE STATUS - Bulk Email Sender');
        $this->info('═══════════════════════════════════════════════');
        $this->newLine();

        // Queue connection info
        $this->line("<fg=cyan>Queue Connection:</> <fg=yellow>{$queueConnection}</>");
        
        if ($queueConnection === 'sync') {
            $this->line("<fg=green>Mode:</> Synchronous (Instant sending, no worker needed)");
            $this->newLine();
            $this->comment('💡 Emails are sent immediately. To enable queue, set QUEUE_CONNECTION=database in .env');
            return 0;
        }

        if ($queueConnection === 'database') {
            $this->line("<fg=green>Mode:</> Asynchronous (Queue worker required)");
            $this->newLine();

            // Check pending jobs
            $pendingJobs = DB::table('jobs')->count();
            $failedJobs = DB::table('failed_jobs')->count();

            $this->line("<fg=cyan>Pending Jobs:</> <fg=yellow>{$pendingJobs}</>");
            $this->line("<fg=cyan>Failed Jobs:</> " . ($failedJobs > 0 ? "<fg=red>{$failedJobs}</>" : "<fg=green>0</>"));
            
            $this->newLine();

            if ($pendingJobs > 0) {
                $this->warn("⚠️  You have {$pendingJobs} pending job(s) in the queue.");
                $this->comment("Run: php artisan queue:work --queue=emails,default");
            } else {
                $this->info('✓ Queue is empty.');
            }

            if ($failedJobs > 0) {
                $this->newLine();
                $this->error("✗ You have {$failedJobs} failed job(s).");
                $this->comment("View failed jobs: php artisan queue:failed");
                $this->comment("Retry failed jobs: php artisan queue:retry all");
            }

            $this->newLine();
            $this->comment('📖 For more information, see QUEUE-SETUP.md');
        }

        return 0;
    }
}
