<?php
/**
 * Test script to verify immediate RFID response
 * This script simulates a card tap and checks if the response is immediate
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Member;
use App\Models\ActiveSession;
use App\Models\RfidLog;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Immediate RFID Response\n";
echo "==================================\n\n";

// Test 1: Check if we can find a member
echo "1. Testing member lookup...\n";
$member = Member::where('is_active', true)->first();
if (!$member) {
    // Try to find any member and activate them
    $member = Member::first();
    if ($member) {
        $member->update(['is_active' => true]);
        echo "   ✅ Found and activated member: {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    } else {
        echo "   ❌ No members found in database\n";
        exit(1);
    }
} else {
    echo "   ✅ Found active member: {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
}

// Test 2: Simulate card tap
echo "\n2. Simulating card tap...\n";
$startTime = microtime(true);

// Simulate the RFID tap process
$cardUid = $member->uid;
$deviceId = 'test_device';

try {
    DB::beginTransaction();
    
    // Check if member has an active session
    $activeSession = ActiveSession::where('member_id', $member->id)
        ->where('status', 'active')
        ->first();
    
    if ($activeSession) {
        echo "   📤 Member is checking out...\n";
        // Check out logic
        $attendance = $activeSession->attendance;
        $attendance->update([
            'check_out_time' => now(),
            'status' => 'checked_out',
            'session_duration' => $activeSession->currentDuration,
        ]);
        
        $activeSession->update([
            'status' => 'inactive',
            'session_duration' => $activeSession->currentDuration,
        ]);
        
        $member->update(['status' => 'offline']);
        
        echo "   ✅ Check-out successful\n";
    } else {
        echo "   📥 Member is checking in...\n";
        // Check in logic
        $attendance = \App\Models\Attendance::create([
            'member_id' => $member->id,
            'check_in_time' => now(),
            'status' => 'checked_in',
        ]);
        
        $activeSession = ActiveSession::create([
            'member_id' => $member->id,
            'attendance_id' => $attendance->id,
            'check_in_time' => now(),
            'status' => 'active',
        ]);
        
        $member->update(['status' => 'active']);
        
        echo "   ✅ Check-in successful\n";
    }
    
    // Log the event
    RfidLog::create([
        'card_uid' => $cardUid,
        'action' => $activeSession ? 'check_out' : 'check_in',
        'status' => 'success',
        'message' => "Test tap for {$member->first_name} {$member->last_name}",
        'timestamp' => now(),
        'device_id' => $deviceId,
    ]);
    
    DB::commit();
    
    $endTime = microtime(true);
    $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
    
    echo "   ⏱️  Response time: " . number_format($responseTime, 2) . "ms\n";
    
    if ($responseTime < 100) {
        echo "   ✅ EXCELLENT: Response time under 100ms\n";
    } elseif ($responseTime < 500) {
        echo "   ✅ GOOD: Response time under 500ms\n";
    } else {
        echo "   ⚠️  SLOW: Response time over 500ms\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check active members count
echo "\n3. Checking active members...\n";
$activeCount = ActiveSession::where('status', 'active')->count();
echo "   📊 Currently active members: {$activeCount}\n";

// Test 4: Check recent RFID logs
echo "\n4. Checking recent RFID logs...\n";
$recentLogs = RfidLog::orderBy('timestamp', 'desc')->limit(5)->get();
foreach ($recentLogs as $log) {
    echo "   📝 {$log->action} - {$log->status} - {$log->timestamp->format('H:i:s')}\n";
}

echo "\n🎯 Test completed!\n";
echo "If response time is under 100ms, the system is working optimally.\n";
echo "If you see members appearing immediately in the dashboard, the fix is working.\n";
