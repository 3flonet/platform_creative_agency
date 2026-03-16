<?php

namespace App\Console\Commands;

use App\Models\SiteVisit;
use Illuminate\Console\Command;

class PruneOldVisits extends Command
{
    protected $signature = 'visits:prune {--days=90 : Number of days to keep}';
    protected $description = 'Delete site visit records older than the specified number of days';

    public function handle(): void
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $count = SiteVisit::where('created_at', '<', $cutoff)->count();

        if ($count === 0) {
            $this->info("No visit records older than {$days} days found.");
            return;
        }

        $this->info("Found {$count} records older than {$days} days.");

        if ($this->confirm("Delete these {$count} records?", true)) {
            SiteVisit::where('created_at', '<', $cutoff)->delete();
            $this->info("✅ Successfully deleted {$count} old visit records.");
        } else {
            $this->warn('Aborted.');
        }
    }
}
