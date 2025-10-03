<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Check if user already exists
$existingUser = User::where('email', 'admin@silencio.gym')->first();

if ($existingUser) {
    echo "Admin user already exists!\n";
    echo "Email: admin@silencio.gym\n";
    exit(0);
}

// Create new admin user
$user = new User();
$user->name = 'Admin';
$user->email = 'admin@silencio.gym';
$user->password = Hash::make('admin123');
$user->save();

echo "Admin user created successfully!\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n";
echo "\nYou can now login at: http://156.67.221.184/login\n";

