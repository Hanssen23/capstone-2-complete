<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Investigating Hans Timothy Samson Status Issue\n";
echo "================================================\n\n";

// Check current status
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
if ($hans) {
    echo "1. Current Hans Timothy Samson status:\n";
    echo "   Status: '{$hans->status}'\n";
    echo "   UID: {$hans->uid}\n";
    echo "   ID: {$hans->id}\n";
    echo "   Updated at: {$hans->updated_at}\n";
}

// Check if there are any active sessions for Hans
echo "\n2. Checking active sessions for Hans:\n";
$activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

if ($activeSession) {
    echo "   ✅ Active session found\n";
    echo "   Session ID: {$activeSession->id}\n";
    echo "   Check-in time: {$activeSession->check_in_time}\n";
    echo "   Status: {$activeSession->status}\n";
} else {
    echo "   ❌ No active session found\n";
}

// Check recent RFID logs for Hans
echo "\n3. Checking recent RFID logs for Hans:\n";
$recentLogs = App\Models\RfidLog::where('card_uid', 'E6415F5F')
    ->orderBy('timestamp', 'desc')
    ->limit(10)
    ->get();

echo "   Recent logs count: " . $recentLogs->count() . "\n";
foreach ($recentLogs as $log) {
    echo "   📝 {$log->action} - {$log->status} - {$log->message}\n";
    echo "      Time: {$log->timestamp}\n";
}

// Check if there's a model observer or event that might be changing the status
echo "\n4. Checking for any model events...\n";
echo "   Checking if there are any model observers or events that might modify member status\n";

// Test setting status again
echo "\n5. Testing status update:\n";
$hans->status = 'active';
$hans->save();
echo "   Status set to 'active' and saved\n";

// Check immediately after save
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
echo "   Status after save: '{$hans->status}'\n";
