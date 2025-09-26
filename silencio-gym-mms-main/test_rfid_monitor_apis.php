<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing RFID Monitor API Endpoints\n";
echo "====================================\n\n";

// Test getActiveMembers API
echo "1. Testing getActiveMembers API...\n";
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

// Test getRfidLogs API
echo "\n2. Testing getRfidLogs API...\n";
try {
    $request = new \Illuminate\Http\Request();
    $response = app('App\Http\Controllers\RfidController')->getRfidLogs($request);
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ API Response: SUCCESS\n";
        echo "   📊 Logs count: " . $data['logs']['total'] . "\n";
        echo "   📄 Current page: " . $data['logs']['current_page'] . "\n";
        
        if ($data['logs']['data']) {
            echo "   📋 Recent logs:\n";
            foreach (array_slice($data['logs']['data'], 0, 3) as $log) {
                echo "      📝 {$log['action']} - {$log['status']} - {$log['message']}\n";
                echo "         Time: {$log['timestamp']} | Card: {$log['card_uid']}\n";
            }
        }
    } else {
        echo "   ❌ API Response: FAILED\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ API Error: " . $e->getMessage() . "\n";
}

// Test dashboard stats API
echo "\n3. Testing dashboard stats API...\n";
try {
    $response = app('App\Http\Controllers\DashboardController')->getDashboardStats();
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['current_active_members'])) {
        echo "   ✅ API Response: SUCCESS\n";
        echo "   📊 Current active members: {$data['current_active_members']}\n";
        echo "   📊 Today check-ins: {$data['today_attendance']}\n";
        echo "   📊 Failed attempts: {$data['failed_rfid_today']}\n";
    } else {
        echo "   ❌ API Response: FAILED\n";
        echo "   Response: " . json_encode($data) . "\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ API Error: " . $e->getMessage() . "\n";
}

// Check if there are any JavaScript errors by testing the routes
echo "\n4. Testing route accessibility...\n";
$routes = [
    'rfid.active-members' => route('rfid.active-members'),
    'rfid.logs' => route('rfid.logs'),
    'dashboard.stats' => route('dashboard.stats'),
];

foreach ($routes as $name => $url) {
    echo "   🔗 {$name}: {$url}\n";
}

echo "\n🎯 API Endpoint Test Complete!\n";
