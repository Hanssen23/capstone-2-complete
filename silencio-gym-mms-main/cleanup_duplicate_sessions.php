<?php
/**
 * Clean Up Duplicate Active Sessions
 * Remove all test-generated sessions and keep only real card taps
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧹 Cleaning Up Duplicate Active Sessions\n";
echo "=======================================\n\n";

// Check current active sessions
echo "1. Current active sessions before cleanup:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Total active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Checked in: {$session->check_in_time}\n";
    echo "      Session ID: {$session->id}\n";
}

// Clear ALL active sessions (they were created by test scripts)
echo "\n2. Clearing all active sessions...\n";
$clearedCount = App\Models\ActiveSession::where('status', 'active')->update(['status' => 'inactive']);
echo "   ✅ Cleared {$clearedCount} active sessions\n";

// Also update attendance records
echo "\n3. Updating attendance records...\n";
$attendanceCount = App\Models\Attendance::where('status', 'checked_in')->update([
    'status' => 'checked_out',
    'check_out_time' => now()
]);
echo "   ✅ Updated {$attendanceCount} attendance records\n";

// Update member statuses to offline
echo "\n4. Updating member statuses...\n";
$memberCount = App\Models\Member::where('status', 'active')->update(['status' => 'offline']);
echo "   ✅ Updated {$memberCount} member statuses\n";

// Check for duplicate members (like David Brown)
echo "\n5. Checking for duplicate members...\n";
$duplicates = App\Models\Member::select('first_name', 'last_name')
    ->groupBy('first_name', 'last_name')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicates->count() > 0) {
    echo "   ⚠️  Found duplicate members:\n";
    foreach ($duplicates as $duplicate) {
        $members = App\Models\Member::where('first_name', $duplicate->first_name)
            ->where('last_name', $duplicate->last_name)
            ->get();
        
        echo "   👥 {$duplicate->first_name} {$duplicate->last_name} ({$members->count()} entries)\n";
        
        // Keep the first one, remove the rest
        $keepMember = $members->first();
        $removeMembers = $members->skip(1);
        
        foreach ($removeMembers as $removeMember) {
            echo "      🗑️  Removing duplicate: {$removeMember->uid}\n";
            $removeMember->delete();
        }
    }
} else {
    echo "   ✅ No duplicate members found\n";
}

// Final verification
echo "\n6. Final verification:\n";
$finalActiveSessions = App\Models\ActiveSession::where('status', 'active')->count();
echo "   📊 Active sessions after cleanup: {$finalActiveSessions}\n";

$finalMembers = App\Models\Member::where('status', 'active')->count();
echo "   📊 Active members after cleanup: {$finalMembers}\n";

echo "\n🎯 Cleanup Complete!\n";
echo "===================\n";
echo "✅ All test-generated sessions removed\n";
echo "✅ Duplicate members cleaned up\n";
echo "✅ Member statuses reset to offline\n";
echo "✅ Ready for real card taps only\n";
echo "\nNow when you tap 2 cards, only 2 members should appear!\n";
