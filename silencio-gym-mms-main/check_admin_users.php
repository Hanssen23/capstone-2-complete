<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CHECKING ADMIN USERS ===\n\n";

// Get all admin users
$adminUsers = User::where('role', 'admin')->get();

if ($adminUsers->count() == 0) {
    echo "❌ No admin users found!\n\n";
} else {
    echo "✅ Found " . $adminUsers->count() . " admin user(s):\n\n";
    
    foreach ($adminUsers as $user) {
        echo "ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Role: {$user->role}\n";
        echo "Email Verified: " . ($user->email_verified_at ? 'Yes (' . $user->email_verified_at . ')' : 'No') . "\n";
        echo "Created: {$user->created_at}\n";
        echo "Updated: {$user->updated_at}\n";
        echo "---\n";
    }
}

// Check for specific emails
echo "\n=== CHECKING SPECIFIC EMAILS ===\n\n";

$emails_to_check = [
    'admin@silencio.gym',
    'admin@admin.com',
    'admin@silencio-gym.com'
];

foreach ($emails_to_check as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "✅ Found user with email: {$email}\n";
        echo "   Name: {$user->name}\n";
        echo "   Role: {$user->role}\n";
        echo "   Verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    } else {
        echo "❌ No user found with email: {$email}\n";
    }
}

// Test password for admin@silencio.gym
echo "\n=== TESTING PASSWORD FOR admin@silencio.gym ===\n\n";

$admin = User::where('email', 'admin@silencio.gym')->first();
if ($admin) {
    $passwordTest = Hash::check('admin123', $admin->password);
    echo "Password 'admin123' test: " . ($passwordTest ? '✅ CORRECT' : '❌ INCORRECT') . "\n";
    
    if (!$passwordTest) {
        echo "\n🔧 Fixing password for admin@silencio.gym...\n";
        $admin->password = Hash::make('admin123');
        $admin->save();
        echo "✅ Password updated to 'admin123'\n";
    }
} else {
    echo "❌ admin@silencio.gym user not found\n";
}

echo "\n=== SUMMARY ===\n";
echo "You should be able to login with:\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n";
echo "URL: http://156.67.221.184/login\n";
