<?php
// Debug payments page
require_once 'vendor/autoload.php';

use App\Models\Payment;

echo "=== PAYMENT DEBUG TEST ===\n";

// Test 1: Check total payments
echo "1. Total payments in database: ";
try {
    $count = Payment::count();
    echo $count . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test 2: Check payments with member relationship
echo "\n2. Payments with member data:\n";
try {
    $payments = Payment::with('member')->latest()->take(3)->get();
    foreach ($payments as $payment) {
        echo "Payment ID: {$payment->id}, Amount: {$payment->amount}, Member: ";
        if ($payment->member) {
            echo "{$payment->member->first_name} {$payment->member->last_name}";
        } else {
            echo "NO MEMBER FOUND";
        }
        echo "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test 3: Check if PaymentController can be instantiated
echo "\n3. Testing PaymentController:\n";
try {
    $controller = new App\Http\Controllers\PaymentController();
    echo "PaymentController instantiated successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG TEST ===\n";
?>
