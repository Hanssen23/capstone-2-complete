<?php
/**
 * Diagnose Connection Issues for Silencio Gym System
 * Run this script to check server status and configuration
 */

echo "=== SILENCIO GYM CONNECTION DIAGNOSTIC ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Check PHP Version
echo "[1] PHP Version: " . PHP_VERSION . "\n";

// Check if Laravel is properly set up
echo "\n[2] Laravel Check:\n";
if (file_exists('artisan')) {
    echo "✓ artisan file found\n";
} else {
    echo "✗ artisan file missing\n";
}

if (file_exists('.env')) {
    echo "✓ .env file found\n";
} else {
    echo "✗ .env file missing - This is likely the issue!\n";
}

if (file_exists('composer.json')) {
    echo "✓ composer.json found\n";
} else {
    echo "✗ composer.json missing\n";
}

// Check key directories
echo "\n[3] Directory Check:\n";
$dirs = ['app', 'config', 'storage', 'public', 'database'];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        echo "✓ {$dir}/ directory exists\n";
    } else {
        echo "✗ {$dir}/ directory missing\n";
    }
}

// Check storage permissions
echo "\n[4] Storage Permissions:\n";
if (is_writable('storage')) {
    echo "✓ storage directory is writable\n";
} else {
    echo "✗ storage directory is NOT writable\n";
}

if (is_writable('bootstrap/cache')) {
    echo "✓ bootstrap/cache directory is writable\n";
} else {
    echo "✗ bootstrap/cache directory is NOT writable\n";
}

// Try to load Laravel
echo "\n[5] Laravel Environment Check:\n";
try {
    require_once 'vendor/autoload.php';
    echo "✓ Composer autoload loaded\n";
    
    // Try to bootstrap Laravel
    $app = require_once 'bootstrap/app.php';
    echo "✓ Laravel application bootstrapped\n";
    
    // Check database connection
    echo "\n[6] Database Connection:\n";
    try {
        $config = $app['config']['database'];
        echo "Database Driver: " . $config['default'] . "\n";
        echo "Host: " . $config['connections'][$config['default']]['host'] . "\n";
        echo "Database: " . $config['connections'][$config['default']]['database'] . "\n";
        
        $pdo = $app['db']->connection()->getPdo();
        echo "✓ Database connection successful\n";
    } catch (Exception $e) {
        echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Laravel bootstrap failed: " . $e->getMessage() . "\n";
}

// Check if port 8001 is already in use
echo "\n[7] Port 8001 Check:\n";
$port_check = shell_exec('netstat -an | findstr :8001 2>nul');
if ($port_check) {
    echo "Port 8001 is currently in use:\n";
    echo $port_check . "\n";
} else {
    echo "Port 8001 is available\n";
}

// Suggested actions
echo "\n=== SUGGESTED ACTIONS ===\n";
if (!file_exists('.env')) {
    echo "1. Create .env file:\n";
    echo "   Copy hostinger.env.example to .env\n";
    echo "   Update database credentials\n";
    echo "   Generate APP_KEY with: php artisan key:generate\n\n";
}

echo "2. Start the server with:\n";
echo "   php artisan serve --host=156.67.221.184 --port=8001\n\n";

echo "3. Alternative - Use restart_server.bat script\n\n";

echo "=== DIAGNOSTIC COMPLETE ===\n";
?>
