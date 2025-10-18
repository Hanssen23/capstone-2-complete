<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;

echo "=== TESTING ACTUAL API RESPONSE STRUCTURE ===\n\n";

$payment = Payment::with(['member', 'membershipPeriod'])->first();

if (!$payment) {
    echo "❌ No payment found\n";
    exit;
}

echo "Testing Payment ID: {$payment->id}\n\n";

// Simulate the exact API response
$response = [
    'success' => true,
    'payment' => $payment,
    'membership_period' => $payment->membershipPeriod
];

echo "=== API RESPONSE STRUCTURE ===\n\n";

echo "SUCCESS: " . ($response['success'] ? 'true' : 'false') . "\n\n";

echo "PAYMENT OBJECT:\n";
echo "  id: {$payment->id}\n";
echo "  amount: {$payment->amount}\n";
echo "  plan_type: {$payment->plan_type}\n";
echo "  duration_type: {$payment->duration_type}\n";
echo "  status: {$payment->status}\n";
echo "  payment_date: {$payment->payment_date}\n";
echo "  payment_time: {$payment->payment_time}\n";
echo "  membership_start_date: " . ($payment->membership_start_date ?? 'NULL') . "\n";
echo "  membership_expiration_date: " . ($payment->membership_expiration_date ?? 'NULL') . "\n";

echo "\nMEMBER OBJECT:\n";
if ($payment->member) {
    echo "  id: {$payment->member->id}\n";
    echo "  first_name: {$payment->member->first_name}\n";
    echo "  last_name: {$payment->member->last_name}\n";
    echo "  email: {$payment->member->email}\n";
    echo "  mobile_number: " . ($payment->member->mobile_number ?? 'NULL') . "\n";
    echo "  uid: {$payment->member->uid}\n";
    echo "  member_number: " . ($payment->member->member_number ?? 'NULL') . "\n";
} else {
    echo "  NULL\n";
}

echo "\nMEMBERSHIP_PERIOD OBJECT:\n";
if ($payment->membershipPeriod) {
    echo "  id: {$payment->membershipPeriod->id}\n";
    echo "  plan_type: {$payment->membershipPeriod->plan_type}\n";
    echo "  duration_type: {$payment->membershipPeriod->duration_type}\n";
    echo "  start_date: {$payment->membershipPeriod->start_date}\n";
    echo "  expiration_date: {$payment->membershipPeriod->expiration_date}\n";
    echo "  status: {$payment->membershipPeriod->status}\n";
} else {
    echo "  NULL\n";
}

echo "\n=== JAVASCRIPT COMPATIBILITY CHECK ===\n\n";

// Check what the JavaScript is trying to access
$jsChecks = [
    'payment.id' => $payment->id ?? 'MISSING',
    'payment.amount' => $payment->amount ?? 'MISSING',
    'payment.plan_type' => $payment->plan_type ?? 'MISSING',
    'payment.duration_type' => $payment->duration_type ?? 'MISSING',
    'payment.payment_date' => $payment->payment_date ?? 'MISSING',
    'payment.payment_time' => $payment->payment_time ?? 'MISSING',
    'payment.membership_start_date' => $payment->membership_start_date ?? 'MISSING',
    'payment.membership_expiration_date' => $payment->membership_expiration_date ?? 'MISSING',
    'payment.member.first_name' => $payment->member->first_name ?? 'MISSING',
    'payment.member.last_name' => $payment->member->last_name ?? 'MISSING',
    'payment.member.email' => $payment->member->email ?? 'MISSING',
    'payment.member.mobile_number' => $payment->member->mobile_number ?? 'MISSING',
    'payment.member.member_number' => $payment->member->member_number ?? 'MISSING',
];

foreach ($jsChecks as $path => $value) {
    $status = ($value === 'MISSING' || $value === null) ? '❌' : '✅';
    echo "{$status} {$path}: " . ($value ?? 'NULL') . "\n";
}

echo "\n=== POTENTIAL ISSUES ===\n\n";

$issues = [];

if (!$payment->member->member_number) {
    $issues[] = "member_number is missing - JavaScript will show 'undefined'";
}

if (!$payment->payment_time) {
    $issues[] = "payment_time is missing - JavaScript will show 'undefined'";
}

if (!$payment->membership_start_date) {
    $issues[] = "membership_start_date is missing - JavaScript will show 'undefined'";
}

if (!$payment->membership_expiration_date) {
    $issues[] = "membership_expiration_date is missing - JavaScript will show 'undefined'";
}

if (empty($issues)) {
    echo "✅ No obvious issues found\n";
} else {
    echo "Found " . count($issues) . " potential issues:\n";
    foreach ($issues as $issue) {
        echo "❌ {$issue}\n";
    }
}

echo "\n=== TESTING JSON SERIALIZATION ===\n\n";

try {
    $jsonResponse = json_encode($response);
    echo "✅ JSON serialization successful\n";
    echo "Response size: " . strlen($jsonResponse) . " bytes\n";
} catch (Exception $e) {
    echo "❌ JSON serialization failed: " . $e->getMessage() . "\n";
}

echo "\n=== RECOMMENDATIONS ===\n\n";
echo "1. Check if member_number field exists in members table\n";
echo "2. Ensure payment_time is properly set\n";
echo "3. Verify membership dates are set\n";
echo "4. Test the actual API endpoint in browser\n";
echo "5. Check browser console for JavaScript errors\n";
