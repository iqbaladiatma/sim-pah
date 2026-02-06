<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class CleanupOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activitylog:cleanup {--days=90 : Number of days to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup activity logs older than specified days (default: 90 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Deleting activity logs older than {$days} days (before {$cutoffDate->format('Y-m-d')})...");

        $count = Activity::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No old logs found to delete.');
            return Command::SUCCESS;
        }

        if ($this->confirm("This will delete {$count} activity log(s). Continue?", true)) {
            $deleted = Activity::where('created_at', '<', $cutoffDate)->delete();

            // Clear cache stats
            \Cache::forget('activity_log_stats');

            $this->info("Successfully deleted {$deleted} activity log(s).");
            return Command::SUCCESS;
        }

        $this->info('Operation cancelled.');
        return Command::SUCCESS;
    }
}
