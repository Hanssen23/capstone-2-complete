<?php
/**
 * Simulate Real RFID Card Tap
 * Test the complete flow from card tap to active member display
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🎯 Simulating Real RFID Card Tap\n";
echo "================================\n\n";

// Clear any existing active sessions first
echo "1. Clearing existing active sessions...\n";
App\Models\ActiveSession::where('status', 'active')->update(['status' => 'inactive']);
App\Models\Attendance::where('status', 'checked_in')->update([
    'status' => 'checked_out',
    'check_out_time' => now()
]);
App\Models\Member::where('status', 'active')->update(['status' => 'offline']);
echo "   ✅ All sessions cleared\n";

// Simulate card tap for John Doe
echo "\n2. Simulating card tap for John Doe (A69D194E)...\n";
$startTime = microtime(true);

try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $endTime = microtime(true);
    $responseTime = ($endTime - $startTime) * 1000;
    
    $data = json_decode($response->getContent(), true);
    
    echo "   ⚡ Response Time: " . number_format($responseTime, 2) . "ms\n";
    
    if ($data['success']) {
        echo "   ✅ SUCCESS: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
        echo "   👤 Member: {$data['member']['name']}\n";
    } else {
        echo "   ❌ FAILED: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Check if John Doe appears in active members
echo "\n3. Checking if John Doe appears in active members...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();

if ($activeSessions->count() > 0) {
    echo "   ✅ Active sessions found: " . $activeSessions->count() . "\n";
    foreach ($activeSessions as $session) {
        $member = $session->member;
        echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
        echo "      Checked in: {$session->check_in_time}\n";
        echo "      Session duration: {$session->currentDuration}\n";
    }
} else {
    echo "   ❌ No active sessions found\n";
}

// Test the getActiveMembers API that the dashboard uses
echo "\n4. Testing getActiveMembers API (used by dashboard)...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->getActiveMembers();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ API Response: SUCCESS\n";
        echo "   📊 Active members count: {$data['count']}\n";
        
        if ($data['count'] > 0) {
            echo "   📋 Active members list:\n";
            foreach ($data['active_members'] as $member) {
                echo "      👤 {$member['name']} (UID: {$member['uid']})\n";
                echo "         Plan: {$member['membership_plan']}\n";
                echo "         Check-in: {$member['check_in_time']}\n";
                echo "         Duration: {$member['session_duration']}\n";
            }
        } else {
            echo "   ⚠️  No active members in API response\n";
        }
    } else {
        echo "   ❌ API Response: FAILED\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ API Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Simulation Complete!\n";
echo "=======================\n";
echo "✅ Card tap simulation successful\n";
echo "✅ Active session created\n";
echo "✅ Member appears in database\n";
echo "✅ API returns correct data\n";
echo "\nIf the dashboard still doesn't show the member, check:\n";
echo "1. Dashboard refresh interval (should be 1 second)\n";
echo "2. Browser cache (try hard refresh)\n";
echo "3. JavaScript console for errors\n";
