<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing RFID System After Admin Logout/Login\n";
echo "==============================================\n\n";

// Check if RFID reader process is running
echo "1. Checking RFID reader process...\n";
$processes = shell_exec('tasklist | findstr python');
if ($processes) {
    echo "   ✅ Python processes found:\n";
    echo "   {$processes}\n";
} else {
    echo "   ❌ No Python processes found - RFID reader not running\n";
}

// Test RFID API endpoint
echo "\n2. Testing RFID API endpoint...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ RFID API: SUCCESS\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ RFID API: FAILED\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ RFID API Error: " . $e->getMessage() . "\n";
}

// Test active members API
echo "\n3. Testing getActiveMembers API...\n";
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
        } else {
            echo "   📭 No active members\n";
        }
    } else {
        echo "   ❌ getActiveMembers API: FAILED\n";
    }
} catch (Exception $e) {
    echo "   ❌ getActiveMembers Error: " . $e->getMessage() . "\n";
}

// Test RFID logs API
echo "\n4. Testing getRfidLogs API...\n";
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

// Test HTTP endpoint that RFID reader uses
echo "\n5. Testing HTTP endpoint...\n";
$apiUrl = 'http://silencio-gym-mms-main.test/rfid/tap';
$testData = [
    'card_uid' => 'A69D194E',
    'device_id' => 'main_reader'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ❌ HTTP Error: {$error}\n";
} else {
    echo "   📊 HTTP Status Code: {$httpCode}\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "   ✅ HTTP Response: SUCCESS\n";
            echo "   📝 Message: {$data['message']}\n";
            echo "   🎯 Action: {$data['action']}\n";
        } else {
            echo "   ❌ HTTP Response: FAILED\n";
            echo "   📝 Response: {$response}\n";
        }
    } else {
        echo "   ❌ HTTP Error: {$httpCode}\n";
        echo "   📝 Response: {$response}\n";
    }
}

// Check database state
echo "\n6. Checking database state...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Session ID: {$session->id}\n";
    echo "      Status: {$session->status}\n";
    echo "      Check-out time: " . ($session->check_out_time ? $session->check_out_time : 'NULL') . "\n";
}

echo "\n🎯 RFID System Check Complete!\n";
