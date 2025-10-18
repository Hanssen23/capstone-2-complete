<?php

namespace App\Console\Commands;

use App\Models\UidPool;
use Illuminate\Console\Command;

class UidPoolStatusCommand extends Command
{
    protected $signature = 'uid-pool:status';
    protected $description = 'Check UID pool status and available UIDs';

    public function handle()
    {
        $availableCount = UidPool::available()->count();
        $assignedCount = UidPool::assigned()->count();
        $totalCount = $availableCount + $assignedCount;

        $this->info("📊 UID Pool Status");
        $this->info("==================");
        $this->info("Total UIDs: {$totalCount}");
        $this->info("Available UIDs: {$availableCount}");
        $this->info("Assigned UIDs: {$assignedCount}");

        if ($availableCount === 0) {
            $this->warn("⚠️  No UIDs available in the pool!");
            $this->info("Run 'php artisan uid-pool:generate' to add more UIDs.");
        } elseif ($availableCount < 5) {
            $this->warn("⚠️  Low UID count! Consider adding more UIDs.");
        } else {
            $this->info("✅ UID pool is healthy.");
        }

        return 0;
    }
}
