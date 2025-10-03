<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Find the admin user
$user = User::where('email', 'admin@silencio.gym')->first();

if (!$user) {
    echo "Admin user not found!\n";
    exit(1);
}

echo "Current user details:\n";
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . ($user->role ?: 'NULL/EMPTY') . "\n";
echo "Email Verified At: " . ($user->email_verified_at ? $user->email_verified_at : 'NULL') . "\n";

// Fix the role
if (empty($user->role) || $user->role !== 'admin') {
    echo "\n⚠️ Role is not set to 'admin'. Fixing...\n";
    $user->role = 'admin';
}

// Ensure email is verified
if (!$user->email_verified_at) {
    echo "⚠️ Email not verified. Fixing...\n";
    $user->email_verified_at = now();
}

$user->save();

echo "\n✅ User updated successfully!\n";
echo "Role: " . $user->role . "\n";
echo "Email Verified At: " . $user->email_verified_at . "\n";
echo "\nYou can now login at: http://156.67.221.184/login\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n";

