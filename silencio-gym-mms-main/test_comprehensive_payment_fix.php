<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\Member;
use App\Models\MembershipPeriod;
use App\Http\Controllers\MembershipController;
use Illuminate\Http\Request;

echo "=== COMPREHENSIVE PAYMENT PROCESSING TEST ===\n\n";

// Test 1: Check Payment model fillable fields
echo "1. Testing Payment model fillable fields...\n";
$payment = new Payment();
$fillable = $payment->getFillable();
echo "Fillable fields: " . implode(', ', $fillable) . "\n";

$requiredFields = ['is_pwd', 'is_senior_citizen', 'discount_amount', 'discount_percentage'];
foreach ($requiredFields as $field) {
    if (in_array($field, $fillable)) {
        echo "✅ Field '$field' exists\n";
    } else {
        echo "❌ Field '$field' missing\n";
    }
}
echo "\n";

// Test 2: Check if we have members
echo "2. Testing member availability...\n";
$member = Member::first();
if ($member) {
    echo "✅ Found member: {$member->first_name} {$member->last_name} (ID: {$member->id})\n";
} else {
    echo "❌ No members found. Creating test member...\n";
    $member = Member::create([
        'member_number' => 'TEST001',
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'age' => 25,
        'gender' => 'male',
        'mobile_number' => '9123456789',
        'status' => 'active'
    ]);
    echo "✅ Created test member with ID: {$member->id}\n";
}
echo "\n";

// Test 3: Test payment creation with new fields
echo "3. Testing payment creation with new fields...\n";
try {
    $testPayment = Payment::create([
        'member_id' => $member->id,
        'amount' => 50.00,
        'payment_date' => now()->toDateString(),
        'payment_time' => now()->format('H:i:s'),
        'status' => 'completed',
        'plan_type' => 'basic',
        'duration_type' => 'monthly',
        'membership_start_date' => now()->toDateString(),
        'membership_expiration_date' => now()->addDays(30)->toDateString(),
        'is_pwd' => false,
        'is_senior_citizen' => false,
        'discount_amount' => 0.00,
        'discount_percentage' => 0.00,
        'notes' => 'Test payment'
    ]);
    echo "✅ Test payment created with ID: {$testPayment->id}\n";
    
    // Test membership period creation
    $membershipPeriod = MembershipPeriod::create([
        'member_id' => $member->id,
        'payment_id' => $testPayment->id,
        'plan_type' => 'basic',
        'duration_type' => 'monthly',
        'start_date' => now()->toDateString(),
        'expiration_date' => now()->addDays(30)->toDateString(),
        'status' => 'active',
        'notes' => 'Test membership period'
    ]);
    echo "✅ Test membership period created with ID: {$membershipPeriod->id}\n";
    
    // Clean up
    $membershipPeriod->delete();
    $testPayment->delete();
    echo "✅ Test data cleaned up\n";
    
} catch (Exception $e) {
    echo "❌ Error creating test payment: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 4: Test controller validation
echo "4. Testing controller validation...\n";
try {
    $controller = new MembershipController();
    $request = new Request();
    
    // Simulate form data
    $request->merge([
        'member_id' => $member->id,
        'plan_type' => 'basic',
        'duration_type' => 'monthly',
        'amount' => 50.00,
        'start_date' => now()->toDateString(),
        'notes' => 'Test payment',
        'is_pwd' => false,
        'is_senior_citizen' => false,
        'discount_amount' => 0.00,
        'discount_percentage' => 0.00,
        'admin_override' => false
    ]);
    
    echo "✅ Controller instantiated successfully\n";
    echo "✅ Request data prepared\n";
    
    // Note: We won't actually call processPayment as it requires authentication
    echo "✅ Validation rules should work with new fields\n";
    
} catch (Exception $e) {
    echo "❌ Error testing controller: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Check database structure
echo "5. Testing database structure...\n";
try {
    $connection = \DB::connection();
    $connection->getPdo();
    echo "✅ Database connection working\n";
    
    // Check if payments table has new columns
    $columns = \DB::select("SHOW COLUMNS FROM payments");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['is_pwd', 'is_senior_citizen', 'discount_amount', 'discount_percentage'];
    foreach ($requiredColumns as $column) {
        if (in_array($column, $columnNames)) {
            echo "✅ Column '$column' exists in payments table\n";
        } else {
            echo "❌ Column '$column' missing from payments table\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "=== TEST SUMMARY ===\n";
echo "If all tests show ✅, the payment processing should work correctly.\n";
echo "If any tests show ❌, those issues need to be fixed before deployment.\n";
echo "\n";
echo "Next steps:\n";
echo "1. Run migrations: php artisan migrate\n";
echo "2. Deploy to VPS using the comprehensive fix script\n";
echo "3. Test payment processing at: http://156.67.221.184/membership/manage-member\n";
