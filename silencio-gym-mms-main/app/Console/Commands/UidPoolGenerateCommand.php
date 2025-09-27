<?php

namespace App\Console\Commands;

use App\Models\UidPool;
use Illuminate\Console\Command;

class UidPoolGenerateCommand extends Command
{
    protected $signature = 'uid-pool:generate {count=10 : Number of UIDs to generate}';
    protected $description = 'Generate new UIDs for the pool';

    public function handle()
    {
        $count = (int) $this->argument('count');
        
        if ($count <= 0) {
            $this->error('Count must be a positive number.');
            return 1;
        }

        $this->info("Generating {$count} new UIDs...");
        
        UidPool::generateNewUids($count);
        
        $availableCount = UidPool::available()->count();
        $this->info("✅ Generated {$count} new UIDs.");
        $this->info("Total available UIDs: {$availableCount}");
        
        return 0;
    }
}
