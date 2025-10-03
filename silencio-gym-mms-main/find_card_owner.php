<?php
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO("sqlite:" . __DIR__ . "/database/database.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Finding owner of card UID: 56438A5F\n";

try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, uid FROM members WHERE uid = ?");
    $stmt->execute(['56438A5F']);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($member) {
        echo "FOUND:\n";
        echo "  Member ID: " . $member['id'] . "\n";
        echo "  Name: " . $member['first_name'] . " " . $member['last_name'] . "\n";
        echo "  Card UID: " . $member['uid'] . "\n";
    } else {
        echo "CARD 56438A5F not found in database\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
