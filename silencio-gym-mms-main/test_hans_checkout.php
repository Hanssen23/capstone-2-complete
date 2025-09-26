<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Hans Timothy Samson Checkout\n";
echo "======================================\n\n";

// Check current status
echo "1. Current status:\n";
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
$activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

if ($activeSession) {
    echo "   ✅ Hans has active session (ID: {$activeSession->id})\n";
    echo "   🕐 Check-in time: {$activeSession->check_in_time}\n";
    echo "   ⏱️  Session duration: {$activeSession->currentDuration}\n";
} else {
    echo "   ❌ No active session found\n";
}

// Test checkout
echo "\n2. Testing checkout (tap card again):\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'E6415F5F',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ SUCCESS: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ FAILED: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Check status after checkout
echo "\n3. Status after checkout:\n";
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
$activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

echo "   📊 Hans status: {$hans->status}\n";

if ($activeSession) {
    echo "   ⚠️  Still has active session (ID: {$activeSession->id})\n";
} else {
    echo "   ✅ No active session (checkout successful)\n";
}

// Check all active sessions
echo "\n4. All active sessions:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Total active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
}

echo "\n🎯 Checkout Test Complete!\n";
