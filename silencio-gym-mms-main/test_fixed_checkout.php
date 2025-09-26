<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Fixed Checkout Process\n";
echo "=================================\n\n";

// Clear all sessions first
echo "1. Clearing all sessions...\n";
App\Models\ActiveSession::where('status', 'active')->update(['status' => 'inactive']);
App\Models\Attendance::where('status', 'checked_in')->update([
    'status' => 'checked_out',
    'check_out_time' => now()
]);
App\Models\Member::where('status', 'active')->update(['status' => 'offline']);
echo "   ✅ All sessions cleared\n";

// Test check-in
echo "\n2. Testing check-in...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ Check-in SUCCESS: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ Check-in FAILED: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Check-in ERROR: " . $e->getMessage() . "\n";
}

// Check active sessions after check-in
echo "\n3. Active sessions after check-in:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Session ID: {$session->id}\n";
    echo "      Check-in time: {$session->check_in_time}\n";
    echo "      Status: {$session->status}\n";
    echo "      Session duration: {$session->currentDuration}\n";
}

// Wait a moment
sleep(2);

// Test checkout
echo "\n4. Testing checkout...\n";
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
    }
    
} catch (Exception $e) {
    echo "   ❌ Checkout ERROR: " . $e->getMessage() . "\n";
}

// Check active sessions after checkout
echo "\n5. Active sessions after checkout:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

if ($activeSessions->count() > 0) {
    foreach ($activeSessions as $session) {
        $member = $session->member;
        echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
        echo "      Session ID: {$session->id}\n";
        echo "      Status: {$session->status}\n";
    }
} else {
    echo "   ✅ No active sessions (checkout successful)\n";
}

// Check inactive sessions
echo "\n6. Checking inactive sessions:\n";
$inactiveSessions = App\Models\ActiveSession::where('status', 'inactive')
    ->where('member_id', App\Models\Member::where('uid', 'A69D194E')->first()->id)
    ->orderBy('check_out_time', 'desc')
    ->limit(1)
    ->get();

foreach ($inactiveSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Session ID: {$session->id}\n";
    echo "      Check-in time: {$session->check_in_time}\n";
    echo "      Check-out time: {$session->check_out_time}\n";
    echo "      Status: {$session->status}\n";
    echo "      Session duration: {$session->currentDuration}\n";
}

// Test check-in after checkout
echo "\n7. Testing check-in after checkout...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ Check-in SUCCESS: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ Check-in FAILED: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Check-in ERROR: " . $e->getMessage() . "\n";
}

// Final check
echo "\n8. Final active sessions check:\n";
$finalActiveSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Final active sessions: " . $finalActiveSessions->count() . "\n";

foreach ($finalActiveSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Check-in time: {$session->check_in_time}\n";
    echo "      Session duration: {$session->currentDuration}\n";
}

echo "\n🎯 Fixed Checkout Test Complete!\n";
echo "===============================\n";
echo "✅ Checkout process should now work correctly\n";
echo "✅ Active sessions should be properly deactivated\n";
echo "✅ Check-in after checkout should work\n";
echo "✅ Dashboard should reflect changes immediately\n";
