<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UidPool;

class FixUidPool extends Command
{
    protected $signature = 'uid:fix';
    protected $description = 'Fix UID pool inconsistencies';

    public function handle()
    {
        $this->info('Fixing UID Pool Issues...');

        // Reset the problematic UID B688164E
        try {
            DB::table('uid_pool')
                ->where('uid', 'B688164E')
                ->update([
                    'status' => 'available',
                    'assigned_at' => null,
                    'returned_at' => now(),
                    'updated_at' => now()
                ]);
            
            $this->info('✅ Reset UID B688164E to available status');
        } catch (\Exception $e) {
            $this->error('❌ Error resetting UID: ' . $e->getMessage());
        }

        // Check for any other UIDs that might be in inconsistent state
        try {
            $inconsistentUids = DB::table('uid_pool')
                ->where('status', 'available')
                ->whereNotNull('assigned_at')
                ->get();
            
            if ($inconsistentUids->count() > 0) {
                $this->info("Found " . $inconsistentUids->count() . " UIDs in inconsistent state:");
                
                foreach ($inconsistentUids as $uid) {
                    $this->line("- " . $uid->uid . " (status: " . $uid->status . ", assigned_at: " . $uid->assigned_at . ")");
                    
                    // Reset these UIDs
                    DB::table('uid_pool')
                        ->where('uid', $uid->uid)
                        ->update([
                            'assigned_at' => null,
                            'returned_at' => now(),
                            'updated_at' => now()
                        ]);
                }
                
                $this->info('✅ Fixed all inconsistent UIDs');
            } else {
                $this->info('✅ No inconsistent UIDs found');
            }
        } catch (\Exception $e) {
            $this->error('❌ Error checking inconsistent UIDs: ' . $e->getMessage());
        }

        // Show current UID pool status
        try {
            $availableCount = DB::table('uid_pool')->where('status', 'available')->count();
            $assignedCount = DB::table('uid_pool')->where('status', 'assigned')->count();
            
            $this->info("\nUID Pool Status:");
            $this->line("- Available UIDs: " . $availableCount);
            $this->line("- Assigned UIDs: " . $assignedCount);
            $this->line("- Total UIDs: " . ($availableCount + $assignedCount));
        } catch (\Exception $e) {
            $this->error('❌ Error getting UID pool status: ' . $e->getMessage());
        }

        $this->info("\n✅ UID Pool fix completed!");
        return 0;
    }
}
