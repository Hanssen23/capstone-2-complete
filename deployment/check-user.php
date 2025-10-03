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

echo "User found:\n";
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Email Verified At: " . ($user->email_verified_at ? $user->email_verified_at : 'NULL') . "\n";
echo "Role: " . $user->role . "\n";
echo "Created At: " . $user->created_at . "\n";

if (!$user->email_verified_at) {
    echo "\n⚠️ User is NOT activated (email_verified_at is NULL)\n";
    echo "Activating user now...\n";
    
    $user->email_verified_at = now();
    $user->save();
    
    echo "✅ User activated successfully!\n";
    echo "Email Verified At: " . $user->email_verified_at . "\n";
} else {
    echo "\n✅ User is already activated!\n";
}

