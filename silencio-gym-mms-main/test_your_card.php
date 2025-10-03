<?php
// Test with your actual card UID: 56438A5F
echo "=== TESTING YOUR CARD (56438A5F) ===\n";

$url = "http://localhost:8007/api/rfid/tap";
$data = json_encode([
    'card_uid' => '56438A5F',
    'device_id' => 'acr122u_main'
]);

$options = [
    'http' => [
        'header' => "Content-type: application/json\r\n",
        'method' => 'POST',
        'content' => $data
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

echo "CURRENT TIME: " . date('Y-m-d H:i:s') . "\n";
echo "API RESPONSE:\n";
echo $response . "\n\n";

if ($response) {
    $result = json_decode($response, true);
    if ($result && $result['success']) {
        echo "✅ SUCCESS: Card processed!\n";
        echo "Action: " . ($result['action'] ?? 'N/A') . "\n";
        echo "Message: " . ($result['message'] ?? 'N/A') . "\n";
        echo "Member: " . ($result['member']['name'] ?? 'N/A') . "\n";
    } else {
        echo "❌ FAILED: " . ($result['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "❌ NO RESPONSE from API\n";
}
?>
