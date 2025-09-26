<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Manual Checkout Process\n";
echo "==================================\n\n";

// Get John Doe's active session
$john = App\Models\Member::where('uid', 'A69D194E')->first();
if (!$john) {
    echo "❌ John Doe not found\n";
    exit;
}

$activeSession = App\Models\ActiveSession::where('member_id', $john->id)
    ->where('status', 'active')
    ->first();

if (!$activeSession) {
    echo "❌ No active session found for John Doe\n";
    exit;
}

echo "1. Before checkout:\n";
echo "   👤 Member: {$john->first_name} {$john->last_name}\n";
echo "   📊 Member Status: {$john->status}\n";
echo "   🆔 Session ID: {$activeSession->id}\n";
echo "   📊 Session Status: {$activeSession->status}\n";
echo "   🕐 Check-in: {$activeSession->check_in_time}\n";
echo "   🕐 Check-out: " . ($activeSession->check_out_time ? $activeSession->check_out_time : 'NULL') . "\n";
echo "   ⏱️  Duration: {$activeSession->currentDuration}\n";

// Try to manually update the session
echo "\n2. Attempting manual checkout...\n";
try {
    DB::beginTransaction();
    
    // Update attendance record
    $attendance = $activeSession->attendance;
    if ($attendance) {
        $attendance->update([
            'check_out_time' => now(),
            'status' => 'checked_out',
            'session_duration' => $activeSession->currentDuration,
        ]);
        echo "   ✅ Attendance record updated\n";
    } else {
        echo "   ⚠️  No attendance record found\n";
    }
    
    // Update active session
    $updateResult = $activeSession->update([
        'status' => 'inactive',
        'check_out_time' => now(),
        'session_duration' => $activeSession->currentDuration,
    ]);
    
    if ($updateResult) {
        echo "   ✅ Active session updated successfully\n";
    } else {
        echo "   ❌ Failed to update active session\n";
    }
    
    // Update member status
    $memberUpdate = $john->update(['status' => 'offline']);
    if ($memberUpdate) {
        echo "   ✅ Member status updated to offline\n";
    } else {
        echo "   ❌ Failed to update member status\n";
    }
    
    DB::commit();
    echo "   ✅ Transaction committed\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "   ❌ Error during checkout: " . $e->getMessage() . "\n";
    echo "   📊 Error details: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Check the result
echo "\n3. After checkout:\n";
$john = App\Models\Member::where('uid', 'A69D194E')->first();
$activeSession = App\Models\ActiveSession::where('id', $activeSession->id)->first();

echo "   👤 Member: {$john->first_name} {$john->last_name}\n";
echo "   📊 Member Status: {$john->status}\n";
echo "   🆔 Session ID: {$activeSession->id}\n";
echo "   📊 Session Status: {$activeSession->status}\n";
echo "   🕐 Check-in: {$activeSession->check_in_time}\n";
echo "   🕐 Check-out: " . ($activeSession->check_out_time ? $activeSession->check_out_time : 'NULL') . "\n";
echo "   ⏱️  Duration: {$activeSession->currentDuration}\n";

// Check if session is still considered active
$stillActive = App\Models\ActiveSession::where('member_id', $john->id)
    ->where('status', 'active')
    ->exists();

if ($stillActive) {
    echo "\n   ⚠️  Session is still considered active in database!\n";
} else {
    echo "\n   ✅ Session is no longer active in database!\n";
}

echo "\n🎯 Manual Checkout Test Complete!\n";
