<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Tap-in/Tap-out Reflection Issue\n";
echo "==========================================\n\n";

// Clear all sessions first
echo "1. Clearing all sessions...\n";
App\Models\ActiveSession::where('status', 'active')->update(['status' => 'inactive']);
App\Models\Attendance::where('status', 'checked_in')->update([
    'status' => 'checked_out',
    'check_out_time' => now()
]);
App\Models\Member::where('status', 'active')->update(['status' => 'offline']);
echo "   ✅ All sessions cleared\n";

// Test complete cycle
echo "\n2. Testing complete tap-in/tap-out cycle...\n";

// Step 1: Check-in John Doe
echo "   📥 Step 1: Check-in John Doe\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "      ✅ SUCCESS: {$data['message']}\n";
        echo "      🎯 Action: {$data['action']}\n";
    } else {
        echo "      ❌ FAILED: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "      ❌ ERROR: " . $e->getMessage() . "\n";
}

// Check database state immediately after check-in
echo "\n   📊 Database state after check-in:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "      Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "      👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "         Session ID: {$session->id}\n";
    echo "         Status: {$session->status}\n";
    echo "         Check-in time: {$session->check_in_time}\n";
    echo "         Check-out time: " . ($session->check_out_time ? $session->check_out_time : 'NULL') . "\n";
}

// Check getActiveMembers API
echo "\n   📊 getActiveMembers API after check-in:\n";
try {
    $response = app('App\Http\Controllers\RfidController')->getActiveMembers();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "      ✅ API Response: SUCCESS (count: {$data['count']})\n";
        if ($data['count'] > 0) {
            foreach ($data['active_members'] as $member) {
                echo "      👤 {$member['name']} (UID: {$member['uid']})\n";
                echo "         Check-in: {$member['check_in_time']}\n";
                echo "         Duration: {$member['session_duration']}\n";
            }
        }
    } else {
        echo "      ❌ API Response: FAILED\n";
    }
} catch (Exception $e) {
    echo "      ❌ API Error: " . $e->getMessage() . "\n";
}

// Wait a moment
sleep(2);

// Step 2: Check-out John Doe
echo "\n   📤 Step 2: Check-out John Doe\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "      ✅ SUCCESS: {$data['message']}\n";
        echo "      🎯 Action: {$data['action']}\n";
    } else {
        echo "      ❌ FAILED: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "      ❌ ERROR: " . $e->getMessage() . "\n";
}

// Check database state after check-out
echo "\n   📊 Database state after check-out:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "      Active sessions: " . $activeSessions->count() . "\n";

// Check getActiveMembers API after check-out
echo "\n   📊 getActiveMembers API after check-out:\n";
try {
    $response = app('App\Http\Controllers\RfidController')->getActiveMembers();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "      ✅ API Response: SUCCESS (count: {$data['count']})\n";
        if ($data['count'] > 0) {
            foreach ($data['active_members'] as $member) {
                echo "      👤 {$member['name']} (UID: {$member['uid']})\n";
            }
        } else {
            echo "      ✅ No active members (checkout successful)\n";
        }
    } else {
        echo "      ❌ API Response: FAILED\n";
    }
} catch (Exception $e) {
    echo "      ❌ API Error: " . $e->getMessage() . "\n";
}

// Check recent RFID logs
echo "\n3. Recent RFID logs:\n";
try {
    $request = new \Illuminate\Http\Request();
    $response = app('App\Http\Controllers\RfidController')->getRfidLogs($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ API Response: SUCCESS\n";
        echo "   📋 Recent logs:\n";
        foreach (array_slice($data['logs']['data'], 0, 5) as $log) {
            $statusIcon = $log['status'] === 'success' ? '✅' : '❌';
            echo "      {$statusIcon} {$log['action']} - {$log['message']}\n";
            echo "         Time: {$log['timestamp']} | Card: {$log['card_uid']}\n";
        }
    } else {
        echo "   ❌ API Response: FAILED\n";
    }
} catch (Exception $e) {
    echo "   ❌ API Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Tap-in/Tap-out Reflection Test Complete!\n";
