<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\Member;
use App\Models\MembershipPeriod;

echo "=== DEBUGGING EMPLOYEE PAYMENT DETAILS ERROR ===\n\n";

// Get all payments
$payments = Payment::with(['member', 'membershipPeriod'])->get();

if ($payments->isEmpty()) {
    echo "❌ No payments found. The error might be due to no payments existing.\n";
    echo "Let me check if we have any payments at all...\n\n";
    
    $allPayments = Payment::all();
    echo "Total payments in database: " . $allPayments->count() . "\n\n";
    
    if ($allPayments->isEmpty()) {
        echo "Creating a test payment to debug the issue...\n";
        
        $member = Member::first();
        if (!$member) {
            echo "❌ No members found. Cannot create test payment.\n";
            exit;
        }
        
        $payment = Payment::create([
            'member_id' => $member->id,
            'amount' => 50.00,
            'payment_date' => now(),
            'payment_time' => now()->format('H:i:s'),
            'status' => 'completed',
            'plan_type' => 'basic',
            'duration_type' => 'monthly',
            'membership_start_date' => now(),
            'membership_expiration_date' => now()->addDays(30),
            'payment_method' => 'cash'
        ]);
        
        echo "✅ Created test payment ID: {$payment->id}\n\n";
        $payments = collect([$payment]);
    }
}

echo "Found " . $payments->count() . " payment(s) to test:\n\n";

foreach($payments as $payment) {
    echo "=== TESTING PAYMENT ID: {$payment->id} ===\n";
    echo "Member ID: {$payment->member_id}\n";
    echo "Amount: ₱{$payment->amount}\n";
    echo "Status: {$payment->status}\n";
    
    // Test member relationship
    echo "\n--- Member Relationship ---\n";
    if ($payment->member) {
        echo "✅ Member found: {$payment->member->full_name}\n";
        echo "   Email: {$payment->member->email}\n";
        echo "   UID: {$payment->member->uid}\n";
    } else {
        echo "❌ No member found for this payment\n";
    }
    
    // Test membershipPeriod relationship
    echo "\n--- MembershipPeriod Relationship ---\n";
    if ($payment->membershipPeriod) {
        echo "✅ MembershipPeriod found:\n";
        echo "   ID: {$payment->membershipPeriod->id}\n";
        echo "   Plan: {$payment->membershipPeriod->plan_type}\n";
        echo "   Duration: {$payment->membershipPeriod->duration_type}\n";
        echo "   Start: {$payment->membershipPeriod->start_date}\n";
        echo "   End: {$payment->membershipPeriod->expiration_date}\n";
        echo "   Status: {$payment->membershipPeriod->status}\n";
    } else {
        echo "❌ No membershipPeriod found for this payment\n";
        echo "   This might be the cause of the error!\n";
        
        // Check if there are any membership periods for this payment
        $periods = MembershipPeriod::where('payment_id', $payment->id)->get();
        echo "   Membership periods with payment_id {$payment->id}: " . $periods->count() . "\n";
        
        if ($periods->isEmpty()) {
            echo "   Creating a membership period for this payment...\n";
            
            $period = MembershipPeriod::create([
                'member_id' => $payment->member_id,
                'payment_id' => $payment->id,
                'plan_type' => $payment->plan_type,
                'duration_type' => $payment->duration_type,
                'start_date' => $payment->membership_start_date ?? now(),
                'expiration_date' => $payment->membership_expiration_date ?? now()->addDays(30),
                'status' => 'active'
            ]);
            
            echo "   ✅ Created membership period ID: {$period->id}\n";
        }
    }
    
    echo "\n";
}

echo "=== TESTING API ENDPOINT ===\n\n";

// Test the actual API endpoint
$testPayment = $payments->first();
if ($testPayment) {
    echo "Testing API endpoint for Payment ID: {$testPayment->id}\n";
    
    try {
        // Simulate the controller method
        $payment = Payment::with(['member', 'membershipPeriod'])->find($testPayment->id);
        
        if ($payment) {
            $response = [
                'success' => true,
                'payment' => $payment,
                'membership_period' => $payment->membershipPeriod
            ];
            
            echo "✅ API Response Structure:\n";
            echo "   success: " . ($response['success'] ? 'true' : 'false') . "\n";
            echo "   payment: " . ($response['payment'] ? 'object' : 'null') . "\n";
            echo "   membership_period: " . ($response['membership_period'] ? 'object' : 'null') . "\n";
            
            if (!$response['membership_period']) {
                echo "❌ membership_period is null - this is likely causing the JavaScript error!\n";
            }
            
        } else {
            echo "❌ Payment not found\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error testing API: " . $e->getMessage() . "\n";
    }
}

echo "\n=== RECOMMENDATIONS ===\n\n";
echo "1. Ensure all payments have corresponding membership periods\n";
echo "2. Check that the membershipPeriod relationship is working\n";
echo "3. Verify the JavaScript can handle null membership_period\n";
echo "4. Test the actual API endpoint in browser\n";
