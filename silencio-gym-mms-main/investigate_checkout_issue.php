<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Investigating Checkout Issue\n";
echo "==============================\n\n";

// Check current active sessions
echo "1. Current active sessions:\n";
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

// Check John Doe specifically
echo "\n2. Checking John Doe details:\n";
$john = App\Models\Member::where('uid', 'A69D194E')->first();
if ($john) {
    echo "   👤 Name: {$john->first_name} {$john->last_name}\n";
    echo "   🆔 UID: {$john->uid}\n";
    echo "   📊 Status: {$john->status}\n";
    
    // Check if he has an active session
    $activeSession = App\Models\ActiveSession::where('member_id', $john->id)
        ->where('status', 'active')
        ->first();
    
    if ($activeSession) {
        echo "   ✅ Has active session (ID: {$activeSession->id})\n";
        echo "   📊 Session status: {$activeSession->status}\n";
        echo "   🕐 Check-in time: {$activeSession->check_in_time}\n";
        echo "   ⏱️  Session duration: {$activeSession->currentDuration}\n";
    } else {
        echo "   ❌ No active session found\n";
    }
}

// Test checkout process
echo "\n3. Testing checkout process...\n";
if ($john && $activeSession) {
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
}

// Check status after checkout
echo "\n4. Status after checkout:\n";
$john = App\Models\Member::where('uid', 'A69D194E')->first();
$activeSession = App\Models\ActiveSession::where('member_id', $john->id)
    ->where('status', 'active')
    ->first();

echo "   📊 John's status: {$john->status}\n";

if ($activeSession) {
    echo "   ⚠️  Still has active session (ID: {$activeSession->id})\n";
    echo "   📊 Session status: {$activeSession->status}\n";
} else {
    echo "   ✅ No active session (checkout successful)\n";
}

// Check all active sessions after checkout
echo "\n5. All active sessions after checkout:\n";
$allActiveSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Total active sessions: " . $allActiveSessions->count() . "\n";

foreach ($allActiveSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
}

// Test check-in after checkout
echo "\n6. Testing check-in after checkout...\n";
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
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Check-in ERROR: " . $e->getMessage() . "\n";
}

// Final check
echo "\n7. Final status check:\n";
$finalActiveSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Final active sessions: " . $finalActiveSessions->count() . "\n";

foreach ($finalActiveSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Check-in time: {$session->check_in_time}\n";
    echo "      Session duration: {$session->currentDuration}\n";
}

echo "\n🎯 Investigation Complete!\n";
