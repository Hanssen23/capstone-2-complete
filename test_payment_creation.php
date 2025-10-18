<?php
// Test payment creation and display
echo "Testing payment creation and display...\n";

// Test 1: Check if we can create a payment
echo "1. Testing payment creation...\n";
$testData = [
    'member_id' => 1,
    'plan_type' => 'basic',
    'duration_type' => 'monthly',
    'amount' => 100.00,
    'start_date' => date('Y-m-d'),
    'notes' => 'Test payment',
    'admin_override' => false,
    'override_reason' => null,
    '_token' => 'test'
];

$jsonData = json_encode($testData);
echo "Test data: " . $jsonData . "\n";

// Test 2: Check current payment count
echo "\n2. Current payment count in database...\n";
echo "Run this on VPS: php artisan tinker --execute='echo App\\Models\\Payment::count();'\n";

// Test 3: Check if payments page loads
echo "\n3. Test payments page access...\n";
echo "URL: http://156.67.221.184/membership/payments\n";
echo "Should show list of payments if logged in\n";

// Test 4: Check payment processing route
echo "\n4. Test payment processing route...\n";
echo "Route: POST /membership/process-payment\n";
echo "Should create payment and return JSON response\n";

echo "\nTest completed. Check each step manually.\n";
?>
