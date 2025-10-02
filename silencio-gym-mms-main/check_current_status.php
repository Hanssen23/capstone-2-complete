<?php
require_once 'vendor/autoload.php';

echo "=== CHECKING CURRENT RFID SYSTEM STATUS ===\n";

$pdo = new PDO("sqlite:database/database.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if card E69F8F40 is registered
$stmt = $pdo->prepare("SELECT id, uid, first_name, last_name, status FROM members WHERE uid = ?");
$stmt->execute(['E69F8F40']);
$member = $stmt->fetch(PDO::FETCH_ASSOC);

if ($member) {
    echo "✅ CARD E69F8F40 REGISTERED\n";
    echo "Member: " . $member['first_name'] . " " . $member['last_name'] . "\n";
    echo "Status: " . ($member['status'] ?? 'NULL') . "\n";
} else {
    echo "❌ CARD E69F8F40 NOT REGISTERED\n";
}

// Check all member cards
echo "\n=== ALL REGISTERED CARDS ===\n";
$stmt = $pdo->query("SELECT id, uid, first_name, last_name FROM members WHERE uid IS NOT NULL AND uid != ''");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($members as $m) {
    echo "  " . $m['uid'] . " -> " . $m['first_name'] . " " . $m['last_name'] . "\n";
}

// Check RFID API endpoint
echo "\n=== TESTING API ENDPOINT ===\n";
$url = "http://localhost:8007/api/rfid/logs";
$response = @file_get_contents($url);

if ($response) {
    echo "✅ API ENDPOINT WORKING\n";
    $data = json_decode($response, true);
    if ($data && isset($data['logs'])) {
        echo "Total logs: " . count($data['logs']['data']) . "\n";
    }
} else {
    echo "❌ API endpoint not responding\n";
}
?>
