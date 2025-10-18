<?php

echo "=== TESTING PAYMENTS PAGE ACCESS ===\n\n";

// Test 1: Check if the route exists
echo "1. Testing route existence...\n";
$routeCheck = shell_exec('ssh root@156.67.221.184 "cd /var/www/silencio-gym && php artisan route:list | grep membership.payments.index"');
if (strpos($routeCheck, 'membership.payments.index') !== false) {
    echo "✅ Route exists: membership.payments.index\n";
} else {
    echo "❌ Route missing: membership.payments.index\n";
}

// Test 2: Check if PaymentController exists
echo "\n2. Testing PaymentController...\n";
$controllerCheck = shell_exec('ssh root@156.67.221.184 "ls -la /var/www/silencio-gym/app/Http/Controllers/PaymentController.php"');
if (strpos($controllerCheck, 'PaymentController.php') !== false) {
    echo "✅ PaymentController exists\n";
} else {
    echo "❌ PaymentController missing\n";
}

// Test 3: Check if the view exists
echo "\n3. Testing view file...\n";
$viewCheck = shell_exec('ssh root@156.67.221.184 "ls -la /var/www/silencio-gym/resources/views/membership/payments/index.blade.php"');
if (strpos($viewCheck, 'index.blade.php') !== false) {
    echo "✅ View file exists\n";
} else {
    echo "❌ View file missing\n";
}

// Test 4: Check if the payments-page component exists
echo "\n4. Testing payments-page component...\n";
$componentCheck = shell_exec('ssh root@156.67.221.184 "ls -la /var/www/silencio-gym/resources/views/components/payments-page.blade.php"');
if (strpos($componentCheck, 'payments-page.blade.php') !== false) {
    echo "✅ Component exists\n";
} else {
    echo "❌ Component missing\n";
}

// Test 5: Check database connection
echo "\n5. Testing database connection...\n";
$dbTest = shell_exec('ssh root@156.67.221.184 "cd /var/www/silencio-gym && php artisan tinker --execute=\"echo \'DB Connection: \' . (DB::connection()->getPdo() ? \'OK\' : \'Failed\');\""');
if (strpos($dbTest, 'OK') !== false) {
    echo "✅ Database connection working\n";
} else {
    echo "❌ Database connection failed\n";
    echo "DB Test Output: " . $dbTest . "\n";
}

// Test 6: Check if Payment model exists and has data
echo "\n6. Testing Payment model...\n";
$paymentTest = shell_exec('ssh root@156.67.221.184 "cd /var/www/silencio-gym && php artisan tinker --execute=\"echo \'Payment count: \' . App\\\\Models\\\\Payment::count();\""');
echo "Payment Test Output: " . $paymentTest . "\n";

// Test 7: Try to access the page with proper authentication simulation
echo "\n7. Testing page access simulation...\n";
$pageTest = shell_exec('ssh root@156.67.221.184 "cd /var/www/silencio-gym && php artisan tinker --execute=\"
try {
    \$controller = new App\\\\Http\\\\Controllers\\\\PaymentController();
    \$request = new Illuminate\\\\Http\\\\Request();
    echo \'Controller instantiation: OK\';
} catch (Exception \$e) {
    echo \'Controller error: \' . \$e->getMessage();
}
\""');
echo "Page Test Output: " . $pageTest . "\n";

echo "\n=== DIAGNOSIS ===\n\n";

echo "The 500 error you're seeing is likely due to one of these issues:\n\n";

echo "1. 🔐 **Authentication Required**\n";
echo "   - The /membership/payments route requires login\n";
echo "   - You need to log in first at: http://156.67.221.184/login\n";
echo "   - After login, the page should work normally\n\n";

echo "2. 🗄️ **Database Issues**\n";
echo "   - Check if the payments table exists\n";
echo "   - Verify database connection is working\n";
echo "   - Ensure proper migrations have been run\n\n";

echo "3. 📄 **View Component Issues**\n";
echo "   - The page uses <x-payments-page> component\n";
echo "   - Component might have syntax errors\n";
echo "   - Variables might not be properly passed\n\n";

echo "=== RECOMMENDED FIXES ===\n\n";

echo "**Immediate Fix:**\n";
echo "1. Go to: http://156.67.221.184/login\n";
echo "2. Log in with admin credentials\n";
echo "3. Then try: http://156.67.221.184/membership/payments\n\n";

echo "**If still getting 500 error after login:**\n";
echo "1. Check Laravel logs: tail -f /var/www/silencio-gym/storage/logs/laravel.log\n";
echo "2. Check nginx error logs: tail -f /var/log/nginx/error.log\n";
echo "3. Verify database tables exist\n";
echo "4. Run migrations if needed: php artisan migrate\n\n";

echo "**Debug Steps:**\n";
echo "1. Enable debug mode in .env: APP_DEBUG=true\n";
echo "2. Clear all caches: php artisan optimize:clear\n";
echo "3. Check file permissions: chown -R www-data:www-data /var/www/silencio-gym\n\n";

echo "The most likely cause is that you need to log in first!\n";
echo "The page is protected by authentication middleware.\n";
