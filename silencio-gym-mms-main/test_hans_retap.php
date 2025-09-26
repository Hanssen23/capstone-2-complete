<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing Hans Timothy Samson Retap Behavior\n";
echo "============================================\n\n";

// First, let's clear any existing sessions to start fresh
echo "1. Clearing existing sessions...\n";
App\Models\ActiveSession::where('status', 'active')->update(['status' => 'inactive']);
App\Models\Attendance::where('status', 'checked_in')->update([
    'status' => 'checked_out',
    'check_out_time' => now()
]);
App\Models\Member::where('status', 'active')->update(['status' => 'offline']);
echo "   ✅ All sessions cleared\n";

// Set Hans status to active
echo "\n2. Setting Hans status to active...\n";
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
$hans->status = 'active';
$hans->save();
echo "   ✅ Hans status set to 'active'\n";

// Simulate first card tap (should be check-in)
echo "\n3. Simulating first card tap (should be check-in)...\n";
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

// Check Hans status after first tap
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
echo "   Hans status after first tap: '{$hans->status}'\n";

// Check if there's an active session
$activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

if ($activeSession) {
    echo "   ✅ Active session found (ID: {$activeSession->id})\n";
} else {
    echo "   ❌ No active session found\n";
}

// Simulate second card tap (should be check-out)
echo "\n4. Simulating second card tap (should be check-out)...\n";
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

// Check Hans status after second tap
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
echo "   Hans status after second tap: '{$hans->status}'\n";

// Check if there's still an active session
$activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
    ->where('status', 'active')
    ->first();

if ($activeSession) {
    echo "   ⚠️  Active session still exists (ID: {$activeSession->id})\n";
} else {
    echo "   ✅ No active session (checkout successful)\n";
}

echo "\n🎯 Test Complete!\n";
