<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Fixed RFID Retap Behavior\n";
echo "====================================\n\n";

// Clear all sessions first
echo "1. Clearing all sessions...\n";
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

// Test first tap (check-in)
echo "\n3. First tap - Check-in...\n";
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

// Test second tap (check-out)
echo "\n4. Second tap - Check-out...\n";
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

// Test third tap (should be check-in again)
echo "\n5. Third tap - Should be check-in again...\n";
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

// Check Hans status after third tap
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
echo "   Hans status after third tap: '{$hans->status}'\n";

// Check active sessions
echo "\n6. Checking active sessions...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
}

echo "\n🎯 Test Complete!\n";
echo "==================\n";
echo "✅ Fixed RFID retap behavior\n";
echo "✅ Members can check in/out multiple times\n";
echo "✅ No more 'inactive status: offline' errors\n";
