<?php
/**
 * UID Pool Synchronization Validator
 * Runs automatically to check and fix UID pool sync issues
 * This should be run periodically to prevent duplicate UID errors
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;
use App\Models\UidPool;

function validateAndSyncUidPool() {
    echo "🔍 UID POOL SYNCHRONIZATION VALIDATOR\n";
    echo "=" . str_repeat("=", 50) . "\n\n";
    
    $issuesFixed = 0;
    $issuesFound = 0;
    
    // Get all member UIDs
    $memberUids = Member::whereNotNull('uid')->pluck('uid')->toArray();
    
    // Get all pool entries
    $poolEntries = UidPool::all();
    
    echo "📊 Analyzing UID assignments...\n";
    echo "Members with UIDs: " . count($memberUids) . "\n";
    echo "Pool entries: " . $poolEntries->count() . "\n\n";
    
    // Check for UIDs assigned to members but marked available in pool
    foreach ($memberUids as $memberUid) {
        $poolEntry = UidPool::where('uid', $memberUid)->first();
        
        if ($poolEntry && $poolEntry->status === 'available') {
            $issuesFound++;
            $member = Member::where('uid', $memberUid)->first();
            
            echo "🔧 Fixing: UID {$memberUid} assigned to '{$member->first_name} {$member->last_name}' but pool shows it as available\n";
            
            $poolEntry->update([
                'status' => 'assigned',
                'assigned_at' => $member->created_at,
                'updated_at' => now()
            ]);
            
            $issuesFixed++;
        }
    }
    
    // Check for UIDs assigned in pool but no member has them
    foreach ($poolEntries as $entry) {
        if ($entry->status === 'assigned' && !in_array($entry->uid, $memberUids)) {
            $issuesFound++;
            
            echo "🔧 Fixing: UID {$entry->uid} marked as assigned in pool but no member has this UID\n";
            
            $entry->update([
                'status' => 'available',
                'assigned_at' => null,
                'returned_at' => now(),
                'updated_at' => now()
            ]);
            
            $issuesFixed++;
        }
    }
    
    // Check for members with UIDs not in pool
    foreach ($memberUids as $memberUid) {
        $poolEntry = UidPool::where('uid', $memberUid)->first();
        
        if (!$poolEntry) {
            $issuesFound++;
            $member = Member::where('uid', $memberUid)->first();
            
            echo "🔧 Fixing: Member '{$member->first_name} {$member->last_name}' has UID {$memberUid} but it's not in the pool\n";
            
            UidPool::create([
                'uid' => $memberUid,
                'status' => 'assigned',
                'assigned_at' => $member->created_at,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $issuesFixed++;
        }
    }
    
    echo "\n📊 SYNCHRONIZATION SUMMARY:\n";
    echo str_repeat("-", 30) . "\n";
    echo "Issues found: {$issuesFound}\n";
    echo "Issues fixed: {$issuesFixed}\n";
    
    if ($issuesFixed > 0) {
        echo "✅ UID pool is now synchronized with member assignments\n";
    } else {
        echo "✅ No synchronization issues found - pool is already correct\n";
    }
    
    return [$issuesFound, $issuesFixed];
}

// Run the validator
[$found, $fixed] = validateAndSyncUidPool();

echo "\n🎯 RECOMMENDATIONS:\n";
echo str_repeat("-", 25) . "\n";
echo "1. Run this validator daily: php uid_pool_sync_validator.php\n";
echo "2. Add this to cron job for automatic synchronization\n";
echo "3. Always scan RFID cards to assign UIDs (don't manually enter)\n";
echo "4. Use member creation validation before saving\n";

echo "\n✅ UID Pool Synchronization Validator complete!\n";
echo "=" . str_repeat("=", 50) . "\n";
?>
