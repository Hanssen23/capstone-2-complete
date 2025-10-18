<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fixing UID Pool Sync Issues ===\n";

// Get all members with UIDs
$members = DB::table('members')->whereNotNull('uid')->get(['uid', 'created_at']);

echo "Found " . count($members) . " members with UIDs\n";

foreach($members as $member) {
    // Check if this UID exists in uid_pool
    $uidPool = DB::table('uid_pool')->where('uid', $member->uid)->first();
    
    if ($uidPool) {
        if ($uidPool->status === 'available') {
            // Mark as assigned since it's being used by a member
            DB::table('uid_pool')->where('uid', $member->uid)->update([
                'status' => 'assigned',
                'assigned_at' => $member->created_at
            ]);
            echo "Fixed UID " . $member->uid . " - marked as assigned\n";
        } else {
            echo "UID " . $member->uid . " - already marked as assigned\n";
        }
    } else {
        echo "WARNING: UID " . $member->uid . " not found in uid_pool table\n";
    }
}

echo "\n=== Final UID Pool Status ===\n";
$availableCount = DB::table('uid_pool')->where('status', 'available')->count();
echo "Available UIDs: " . $availableCount . "\n";

$assignedCount = DB::table('uid_pool')->where('status', 'assigned')->count();
echo "Assigned UIDs: " . $assignedCount . "\n";

echo "\nUID Pool sync completed!\n";
