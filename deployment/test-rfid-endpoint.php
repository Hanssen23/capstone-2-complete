<?php
// Simple test endpoint for RFID without authentication
// Place this in public/rfid-test.php on the VPS

header('Content-Type: application/json');

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Log the request
$logFile = __DIR__ . '/../storage/logs/rfid-test.log';
$logEntry = date('Y-m-d H:i:s') . ' - ' . json_encode($data) . PHP_EOL;
file_put_contents($logFile, $logEntry, FILE_APPEND);

// Return success response
echo json_encode([
    'success' => true,
    'message' => 'RFID tap received',
    'data' => $data,
    'timestamp' => date('Y-m-d H:i:s')
]);

