<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🌐 Testing HTTP API Endpoint\n";
echo "============================\n\n";

// Test the HTTP endpoint that the RFID reader uses
$apiUrl = 'http://silencio-gym-mms-main.test/rfid/tap';

echo "1. Testing HTTP endpoint: {$apiUrl}\n";

// Test with John Doe's card
$testData = [
    'card_uid' => 'A69D194E',
    'device_id' => 'main_reader'
];

echo "   📡 Testing with John Doe (UID: A69D194E)...\n";

// Simulate the HTTP request that the RFID reader makes
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
    echo "   ❌ cURL Error: {$error}\n";
} else {
    echo "   📊 HTTP Status Code: {$httpCode}\n";
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data) {
            if ($data['success']) {
                echo "   ✅ HTTP Response: SUCCESS\n";
                echo "   📝 Message: {$data['message']}\n";
                echo "   🎯 Action: {$data['action']}\n";
            } else {
                echo "   ❌ HTTP Response: FAILED\n";
                echo "   📝 Message: {$data['message']}\n";
                echo "   🎯 Action: {$data['action']}\n";
            }
        } else {
            echo "   ❌ Invalid JSON response\n";
        }
    } else {
        echo "   ❌ HTTP Error: {$httpCode}\n";
        echo "   📝 Response: {$response}\n";
    }
}

// Check if the change was reflected in the database
echo "\n2. Checking database after HTTP request...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "      Session ID: {$session->id}\n";
    echo "      Status: {$session->status}\n";
    echo "      Check-out time: " . ($session->check_out_time ? $session->check_out_time : 'NULL') . "\n";
}

echo "\n🎯 HTTP API Test Complete!\n";
