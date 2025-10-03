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

// Check if user has 'status' column
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('users');

if (in_array('status', $columns)) {
    $user->status = 'active';
    echo "Setting status to 'active'\n";
}

if (in_array('is_active', $columns)) {
    $user->is_active = true;
    echo "Setting is_active to true\n";
}

if (in_array('activated', $columns)) {
    $user->activated = true;
    echo "Setting activated to true\n";
}

if (in_array('email_verified_at', $columns)) {
    $user->email_verified_at = now();
    echo "Setting email_verified_at to now\n";
}

$user->save();

echo "\nAdmin user activated successfully!\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n";
echo "\nYou can now login at: http://156.67.221.184/login\n";

