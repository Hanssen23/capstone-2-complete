<?php
$response = file_get_contents('http://localhost:8007/api/rfid/logs');
$data = json_decode($response, true);
$logs = $data['logs']['data'];

echo "Current time: " . date('Y-m-d H:i:s') . "\n";
echo "Total logs: " . count($logs) . "\n";

if (count($logs) > 0) {
    $last_log = end($logs);
    echo "Latest log:\n";
    echo "  Timestamp: " . $last_log['timestamp'] . "\n";
    echo "  Card UID: " . $last_log['card_uid'] . "\n";
    echo "  Action: " . $last_log['action'] . "\n";
    echo "  Status: " . $last_log['status'] . "\n";
    echo "  Message: " . $last_log['message'] . "\n";
} else {
    echo "No logs found\n";
}
?>
