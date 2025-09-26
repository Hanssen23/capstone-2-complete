<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Actual RFID API Integration\n";
echo "=====================================\n\n";

// Test the actual RFID API endpoint
echo "1. Testing RFID API endpoint directly...\n";

// Test with John Doe's card
$testData = [
    'card_uid' => 'A69D194E',
    'device_id' => 'main_reader'
];

echo "   📡 Testing with John Doe (UID: A69D194E)...\n";

try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request($testData)
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

// Check active members API
echo "\n2. Testing getActiveMembers API...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->getActiveMembers();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ getActiveMembers API: SUCCESS\n";
        echo "   📊 Active members count: {$data['count']}\n";
        
        if ($data['count'] > 0) {
            foreach ($data['active_members'] as $member) {
                echo "   👤 {$member['name']} (UID: {$member['uid']})\n";
                echo "      Check-in: {$member['check_in_time']}\n";
                echo "      Duration: {$member['session_duration']}\n";
            }
        }
    } else {
        echo "   ❌ getActiveMembers API: FAILED\n";
    }
} catch (Exception $e) {
    echo "   ❌ getActiveMembers Error: " . $e->getMessage() . "\n";
}

// Test RFID logs API
echo "\n3. Testing getRfidLogs API...\n";
try {
    $request = new \Illuminate\Http\Request();
    $response = app('App\Http\Controllers\RfidController')->getRfidLogs($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ getRfidLogs API: SUCCESS\n";
        echo "   📋 Recent logs:\n";
        foreach (array_slice($data['logs']['data'], 0, 3) as $log) {
            $statusIcon = $log['status'] === 'success' ? '✅' : '❌';
            echo "      {$statusIcon} {$log['action']} - {$log['message']}\n";
            echo "         Time: {$log['timestamp']} | Card: {$log['card_uid']}\n";
        }
    } else {
        echo "   ❌ getRfidLogs API: FAILED\n";
    }
} catch (Exception $e) {
    echo "   ❌ getRfidLogs Error: " . $e->getMessage() . "\n";
}

// Test dashboard stats API
echo "\n4. Testing getDashboardStats API...\n";
try {
    $response = app('App\Http\Controllers\DashboardController')->getDashboardStats();
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['current_active_members'])) {
        echo "   ✅ getDashboardStats API: SUCCESS\n";
        echo "   📊 Current active members: {$data['current_active_members']}\n";
        echo "   📊 Today check-ins: {$data['today_attendance']}\n";
        echo "   📊 Failed attempts: {$data['failed_rfid_today']}\n";
    } else {
        echo "   ❌ getDashboardStats API: FAILED\n";
    }
} catch (Exception $e) {
    echo "   ❌ getDashboardStats Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 RFID API Integration Test Complete!\n";
