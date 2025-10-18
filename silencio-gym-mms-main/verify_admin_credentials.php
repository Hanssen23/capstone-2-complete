<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== ADMIN CREDENTIALS VERIFICATION ===\n\n";

$email = 'admin@silencio.gym';
$password = 'admin123';

echo "Checking credentials:\n";
echo "📧 Email: {$email}\n";
echo "🔑 Password: {$password}\n";
echo "🌐 URL: http://156.67.221.184/login\n\n";

$user = User::where('email', $email)->first();

if ($user) {
    echo "✅ User exists in database\n";
    echo "   ID: {$user->id}\n";
    echo "   Name: {$user->name}\n";
    echo "   Role: {$user->role}\n";
    
    if (Hash::check($password, $user->password)) {
        echo "✅ Password is correct\n";
    } else {
        echo "❌ Password is incorrect\n";
        echo "🔧 Fixing password...\n";
        $user->password = Hash::make($password);
        $user->save();
        echo "✅ Password fixed!\n";
    }
    
    if ($user->email_verified_at) {
        echo "✅ Account is activated\n";
    } else {
        echo "❌ Account is not activated\n";
        echo "🔧 Activating account...\n";
        $user->email_verified_at = now();
        $user->save();
        echo "✅ Account activated!\n";
    }
    
    if ($user->role === 'admin') {
        echo "✅ User has admin role\n";
    } else {
        echo "❌ User does not have admin role\n";
        echo "🔧 Setting admin role...\n";
        $user->role = 'admin';
        $user->save();
        echo "✅ Admin role set!\n";
    }
    
    echo "\n🎉 CREDENTIALS ARE READY!\n\n";
    
} else {
    echo "❌ User does not exist\n";
    echo "🔧 Creating admin user...\n";
    
    $user = User::create([
        'name' => 'Administrator',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Admin user created!\n";
    echo "   ID: {$user->id}\n\n";
}

echo "=== SHARE THESE CREDENTIALS ===\n\n";
echo "🌐 Login URL: http://156.67.221.184/login\n";
echo "📧 Email: admin@silencio.gym\n";
echo "🔑 Password: admin123\n\n";

echo "=== COPY & PASTE MESSAGE ===\n\n";
echo "Here are the admin login credentials for the Silencio Gym system:\n\n";
echo "Login URL: http://156.67.221.184/login\n";
echo "Email: admin@silencio.gym\n";
echo "Password: admin123\n\n";
echo "Please use these exact credentials to access the admin dashboard.\n";
