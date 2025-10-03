<?php
require_once 'vendor/autoload.php';

$pdo = new PDO("sqlite:database/database.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== MEMBERS TABLE STRUCTURE ===\n";
$stmt = $pdo->query("PRAGMA table_info(members)");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($columns as $column) {
    echo "  " . $column['name'] . " (" . $column['type'] . ")\n";
}

echo "\n=== MEMBERS DATA ===\n";
$stmt = $pdo->prepare("SELECT id, first_name, last_name, uid, created_at FROM members WHERE uid IN ('A69D194E', '56438A5F')");
$stmt->execute();
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($members as $member) {
    echo "ID: " . $member['id'] . "\n";
    echo "Name: " . $member['first_name'] . " " . $member['last_name'] . "\n";
    echo "UID: " . $member['uid'] . "\n";
    echo "Created: " . $member['created_at'] . "\n\n";
}
?>
