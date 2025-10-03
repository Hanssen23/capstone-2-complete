<?php
// Debug log inconsistencies and member sessions
echo "=== DEBUGGING LOG INCONSISTENCIES ===\n";

try {
    // Get all RFID logs
    $response = file_get_contents('http://localhost:8007/api/rfid/logs');
    $data = json_decode($response, true);
    $logs = $data['logs']['data'];
    
    echo "Total logs: " . count($logs) . "\n\n";
    
    // Check specific problem cards
    $problem_cards = ['A69D194E', '56438A5F', 'E6415F5F'];
    
    foreach ($problem_cards as $card_uid) {
        echo "=== CARD UID: $card_uid ===\n";
        
        $card_logs = array_filter($logs, function($log) use ($card_uid) {
            return $log['card_uid'] === $card_uid;
        });
        
        if (empty($card_logs)) {
            echo "No logs found for this card UID\n";
            continue;
        }
        
        foreach ($card_logs as $log) {
            echo "  Action: " . $log['action'] . "\n";
            echo "  Status: " . $log['status'] . "\n";
            echo "  Message: " . $log['message'] . "\n";
            echo "  Timestamp: " . $log['timestamp'] . "\n";
            echo "  Member Name: " . ($log['member_name'] ?? 'NULL') . "\n\n";
        }
    }
    
    // Check database for card registration
    echo "=== DATABASE CHECK ===\n";
    require_once __DIR__ . '/vendor/autoload.php';
    $pdo = new PDO("sqlite:" . __DIR__ . "/database/database.sqlite");
    
    foreach ($problem_cards as $card_uid) {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, uid, membership_status, created_at FROM members WHERE uid = ?");
        $stmt->execute([$card_uid]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($member) {
            echo "CARD $card_uid:\n";
            echo "  Member: " . $member['first_name'] . " " . $member['last_name'] . "\n";
            echo "  Status: " . $member['membership_status'] . "\n";
            echo "  ID: " . $member['id'] . "\n";
        } else {
            echo "CARD $card_uid: NOT REGISTERED IN DATABASE\n";
        }
        echo "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
