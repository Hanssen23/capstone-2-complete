<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING ADMIN LOGIN ===\n\n";

// Test credentials
$email = 'admin@silencio.gym';
$password = 'admin123';

echo "Testing login with:\n";
echo "Email: {$email}\n";
echo "Password: {$password}\n\n";

// Step 1: Check if user exists
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ FAIL: User not found\n";
    exit(1);
}

echo "✅ PASS: User found (ID: {$user->id})\n";

// Step 2: Check password
if (!Hash::check($password, $user->password)) {
    echo "❌ FAIL: Password incorrect\n";
    exit(1);
}

echo "✅ PASS: Password correct\n";

// Step 3: Check if account is activated
if (!$user->email_verified_at) {
    echo "❌ FAIL: Account not activated\n";
    exit(1);
}

echo "✅ PASS: Account activated\n";

// Step 4: Check role
if ($user->role !== 'admin') {
    echo "❌ FAIL: User is not admin (role: {$user->role})\n";
    exit(1);
}

echo "✅ PASS: User has admin role\n";

// Step 5: Test Laravel Auth attempt
$credentials = ['email' => $email, 'password' => $password];

if (Auth::guard('web')->attempt($credentials)) {
    echo "✅ PASS: Laravel Auth::attempt() successful\n";
    
    $authenticatedUser = Auth::guard('web')->user();
    echo "✅ PASS: Authenticated user: {$authenticatedUser->name} ({$authenticatedUser->email})\n";
    
    // Check if user is admin using the model method
    if ($authenticatedUser->isAdmin()) {
        echo "✅ PASS: User passes isAdmin() check\n";
    } else {
        echo "❌ FAIL: User fails isAdmin() check\n";
    }
    
    Auth::guard('web')->logout();
} else {
    echo "❌ FAIL: Laravel Auth::attempt() failed\n";
    exit(1);
}

echo "\n=== TEST RESULTS ===\n\n";
echo "🎉 ALL TESTS PASSED!\n\n";

echo "The login credentials are working correctly:\n";
echo "✅ URL: http://156.67.221.184/login\n";
echo "✅ Email: admin@silencio.gym\n";
echo "✅ Password: admin123\n\n";

echo "=== WHAT TO SHARE WITH OTHERS ===\n\n";
echo "Send these exact credentials to users:\n\n";
echo "🌐 Login URL: http://156.67.221.184/login\n";
echo "📧 Email: admin@silencio.gym\n";
echo "🔑 Password: admin123\n\n";

echo "=== TROUBLESHOOTING ===\n\n";
echo "If someone still can't login, check:\n";
echo "1. They're using the exact email: admin@silencio.gym (not admin@silencio-gym.com)\n";
echo "2. They're using the exact password: admin123\n";
echo "3. They're going to the correct URL: http://156.67.221.184/login\n";
echo "4. Their browser isn't auto-filling old credentials\n";
echo "5. They're not confusing it with member login\n";
