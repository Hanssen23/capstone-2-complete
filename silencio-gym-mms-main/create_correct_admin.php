<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREATING CORRECT ADMIN USER ===\n\n";

// Check if admin@silencio.gym already exists
$existingAdmin = User::where('email', 'admin@silencio.gym')->first();

if ($existingAdmin) {
    echo "✅ Admin user admin@silencio.gym already exists!\n";
    echo "Updating password and ensuring activation...\n\n";
    
    $existingAdmin->update([
        'name' => 'Administrator',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Admin user updated successfully!\n";
} else {
    echo "Creating new admin user: admin@silencio.gym\n\n";
    
    $admin = User::create([
        'name' => 'Administrator',
        'email' => 'admin@silencio.gym',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Admin user created successfully!\n";
    echo "ID: {$admin->id}\n";
}

// Verify the credentials work
echo "\n=== VERIFYING CREDENTIALS ===\n\n";

$testUser = User::where('email', 'admin@silencio.gym')->first();

if ($testUser && Hash::check('admin123', $testUser->password)) {
    echo "✅ Password verification successful!\n";
    echo "✅ Email verification: " . ($testUser->email_verified_at ? 'Activated' : 'Not activated') . "\n";
    echo "✅ Role: {$testUser->role}\n\n";
} else {
    echo "❌ Password verification failed!\n\n";
}

echo "=== LOGIN CREDENTIALS ===\n\n";
echo "🌐 URL: http://156.67.221.184/login\n";
echo "📧 Email: admin@silencio.gym\n";
echo "🔑 Password: admin123\n\n";

echo "=== TESTING LOGIN PROCESS ===\n\n";

// Simulate the login process
$credentials = [
    'email' => 'admin@silencio.gym',
    'password' => 'admin123'
];

$user = User::where('email', $credentials['email'])->first();

if ($user) {
    echo "✅ User found in database\n";
    
    if (Hash::check($credentials['password'], $user->password)) {
        echo "✅ Password matches\n";
        
        if ($user->email_verified_at) {
            echo "✅ Account is activated\n";
            
            if ($user->role === 'admin') {
                echo "✅ User has admin role\n";
                echo "\n🎉 LOGIN SHOULD WORK!\n";
            } else {
                echo "❌ User does not have admin role: {$user->role}\n";
            }
        } else {
            echo "❌ Account is not activated\n";
        }
    } else {
        echo "❌ Password does not match\n";
    }
} else {
    echo "❌ User not found\n";
}

echo "\n=== SUMMARY ===\n";
echo "The admin user admin@silencio.gym is now properly set up.\n";
echo "You can share these credentials with others:\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n";
