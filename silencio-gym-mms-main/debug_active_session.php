<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging Active Session Detection\n";
echo "====================================\n\n";

// Check Hans Timothy Samson
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
echo "1. Hans Timothy Samson details:\n";
echo "   ID: {$hans->id}\n";
echo "   UID: {$hans->uid}\n";
echo "   Status: {$hans->status}\n";

// Check all active sessions
echo "\n2. All active sessions:\n";
$allActiveSessions = App\Models\ActiveSession::where('status', 'active')->get();
echo "   📊 Total active sessions: " . $allActiveSessions->count() . "\n";

foreach ($allActiveSessions as $session) {
    echo "   📝 Session ID: {$session->id}\n";
    echo "      Member ID: {$session->member_id}\n";
    echo "      Status: {$session->status}\n";
    echo "      Check-in time: {$session->check_in_time}\n";
}

// Check Hans's specific active session
echo "\n3. Hans's active session query:\n";
$hansActiveSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

if ($hansActiveSession) {
    echo "   ✅ Found active session for Hans\n";
    echo "   📝 Session ID: {$hansActiveSession->id}\n";
    echo "   📊 Status: {$hansActiveSession->status}\n";
    echo "   🕐 Check-in time: {$hansActiveSession->check_in_time}\n";
} else {
    echo "   ❌ No active session found for Hans\n";
}

// Check if there's a database transaction issue
echo "\n4. Testing with fresh query:\n";
DB::beginTransaction();
$freshSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

if ($freshSession) {
    echo "   ✅ Fresh query found active session\n";
    echo "   📝 Session ID: {$freshSession->id}\n";
} else {
    echo "   ❌ Fresh query found no active session\n";
}
DB::rollBack();

// Check if there are any database locks
echo "\n5. Checking for database issues:\n";
try {
    $testSession = App\Models\ActiveSession::where('member_id', $hans->id)
        ->where('status', 'active')
        ->lockForUpdate()
        ->first();
    
    if ($testSession) {
        echo "   ✅ Lock query found active session\n";
    } else {
        echo "   ❌ Lock query found no active session\n";
    }
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Debug Complete!\n";
