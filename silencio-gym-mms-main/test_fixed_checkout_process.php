<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Fixed Checkout Process\n";
echo "=================================\n\n";

// Test checkout for Hans Timothy Samson
echo "1. Testing checkout for Hans Timothy Samson...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'E6415F5F',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ Checkout SUCCESS: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ Checkout FAILED: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Checkout ERROR: " . $e->getMessage() . "\n";
}

// Check active sessions after checkout
echo "\n2. Checking active sessions after checkout:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Session ID: {$session->id}\n";
    echo "      Status: {$session->status}\n";
    echo "      Check-out time: " . ($session->check_out_time ? $session->check_out_time : 'NULL') . "\n";
}

// Test checkout for John Doe
echo "\n3. Testing checkout for John Doe...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ Checkout SUCCESS: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ Checkout FAILED: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Checkout ERROR: " . $e->getMessage() . "\n";
}

// Final check
echo "\n4. Final active sessions check:\n";
$finalActiveSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Final active sessions: " . $finalActiveSessions->count() . "\n";

if ($finalActiveSessions->count() > 0) {
    foreach ($finalActiveSessions as $session) {
        $member = $session->member;
        echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    }
} else {
    echo "   ✅ No active sessions (all members checked out successfully)\n";
}

echo "\n🎯 Fixed Checkout Test Complete!\n";
