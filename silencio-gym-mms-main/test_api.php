<?php

echo "=== RFID API Test ===\n\n";

// Test data
$data = [
    'card_uid' => 'E6415F5F',
    'device_id' => 'test_device'
];

$url = 'http://localhost:9000/rfid/tap';

echo "Testing API endpoint: $url\n";
echo "Card UID: {$data['card_uid']}\n\n";

// Make the request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status Code: $httpCode\n";
echo "Response: $response\n";

if ($error) {
    echo "cURL Error: $error\n";
}

echo "\n=== Test Complete ===\n";
