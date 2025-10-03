<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Checking users table schema...\n";

// Get all columns in users table
$columns = Schema::getColumnListing('users');
echo "Current columns: " . implode(', ', $columns) . "\n\n";

// Check if role column exists
if (!in_array('role', $columns)) {
    echo "⚠️ 'role' column is missing. Adding it now...\n";
    
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('admin')->after('password');
    });
    
    echo "✅ 'role' column added successfully!\n";
} else {
    echo "✅ 'role' column already exists.\n";
}

// Update the admin user
echo "\nUpdating admin user...\n";

DB::table('users')
    ->where('email', 'admin@silencio.gym')
    ->update([
        'role' => 'admin',
        'email_verified_at' => now(),
        'updated_at' => now()
    ]);

echo "✅ Admin user updated successfully!\n";

// Verify the update
$user = DB::table('users')->where('email', 'admin@silencio.gym')->first();
echo "\nUser details:\n";
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
echo "Email Verified At: " . $user->email_verified_at . "\n";

echo "\n✅ All done! You can now login at: http://156.67.221.184/login\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n";

