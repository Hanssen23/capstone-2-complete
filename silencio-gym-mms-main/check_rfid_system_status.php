<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing RFID System Status\n";
echo "============================\n\n";

// Check current active sessions
echo "1. Current active sessions:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Checked in: {$session->check_in_time}\n";
    echo "      Session duration: {$session->currentDuration}\n";
}

// Check Hans Timothy Samson specifically
echo "\n2. Checking Hans Timothy Samson:\n";
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
if ($hans) {
    echo "   👤 Name: {$hans->first_name} {$hans->last_name}\n";
    echo "   🆔 UID: {$hans->uid}\n";
    echo "   📊 Status: {$hans->status}\n";
    
    // Check if he has an active session
    $activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
        ->where('status', 'active')
        ->first();
    
    if ($activeSession) {
        echo "   ✅ Has active session (ID: {$activeSession->id})\n";
        echo "   🕐 Check-in time: {$activeSession->check_in_time}\n";
        echo "   ⏱️  Session duration: {$activeSession->currentDuration}\n";
    } else {
        echo "   ❌ No active session\n";
    }
} else {
    echo "   ❌ Hans Timothy Samson not found\n";
}

// Test API endpoint
echo "\n3. Testing RFID API endpoint:\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'E6415F5F',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ API Response: SUCCESS\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ API Response: FAILED\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ API Error: " . $e->getMessage() . "\n";
}

// Test getActiveMembers API
echo "\n4. Testing getActiveMembers API:\n";
try {
    $response = app('App\Http\Controllers\RfidController')->getActiveMembers();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ API Response: SUCCESS\n";
        echo "   📊 Active members count: {$data['count']}\n";
        
        if ($data['count'] > 0) {
            echo "   📋 Active members:\n";
            foreach ($data['active_members'] as $member) {
                echo "      👤 {$member['name']} (UID: {$member['uid']})\n";
                echo "         Plan: {$member['membership_plan']}\n";
                echo "         Check-in: {$member['check_in_time']}\n";
                echo "         Duration: {$member['session_duration']}\n";
            }
        }
    } else {
        echo "   ❌ API Response: FAILED\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ API Error: " . $e->getMessage() . "\n";
}

// Check recent RFID logs
echo "\n5. Recent RFID logs:\n";
$recentLogs = App\Models\RfidLog::orderBy('timestamp', 'desc')->limit(5)->get();
echo "   📊 Recent logs: " . $recentLogs->count() . "\n";

foreach ($recentLogs as $log) {
    echo "   📝 {$log->action} - {$log->status} - {$log->message}\n";
    echo "      Time: {$log->timestamp}\n";
    echo "      Card: {$log->card_uid}\n";
}

echo "\n🎯 System Status Check Complete!\n";
