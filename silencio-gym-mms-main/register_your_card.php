<?php
// Register your actual card UID: 56438A5F
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO("sqlite:" . __DIR__ . "/database/database.sqlite");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Registering your actual card UID: 56438A5F\n";

try {
    $stmt = $pdo->prepare("UPDATE members SET uid = ? WHERE id = 1");
    $stmt->execute(['56438A5F']);
    
    echo "SUCCESS: Your card 56438A5F registered to admin member!\n";
    
    // Verify
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, uid FROM members WHERE id = 1");
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "MEMBER INFO:\n";
    echo "  Name: " . $member['first_name'] . " " . $member['last_name'] . "\n";
    echo "  Card UID: " . $member['uid'] . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
