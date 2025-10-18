<?php
// Web-accessible OPcache reset script
header('Content-Type: text/plain');

echo "=== OPcache Reset Tool ===\n\n";

if (function_exists('opcache_reset')) {
    echo "OPcache is available\n";
    
    if (opcache_reset()) {
        echo "✅ OPcache reset successfully!\n";
    } else {
        echo "❌ Failed to reset OPcache\n";
    }
    
    // Also invalidate specific files
    $files = [
        '/var/www/html/app/Http/Controllers/AuthController.php',
        '/var/www/html/resources/views/login.blade.php',
        '/var/www/html/resources/views/login-new.blade.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            if (opcache_invalidate($file, true)) {
                echo "✅ Invalidated: " . basename($file) . "\n";
            } else {
                echo "❌ Failed to invalidate: " . basename($file) . "\n";
            }
        }
    }
} else {
    echo "❌ OPcache not available\n";
}

echo "\n=== Testing View System ===\n";

// Test if we can load the new view
try {
    if (file_exists('/var/www/html/resources/views/login-new.blade.php')) {
        echo "✅ login-new.blade.php exists\n";
        $content = file_get_contents('/var/www/html/resources/views/login-new.blade.php');
        if (strpos($content, 'NEW LOGIN VIEW') !== false) {
            echo "✅ login-new.blade.php contains NEW LOGIN VIEW marker\n";
        } else {
            echo "❌ login-new.blade.php does NOT contain NEW LOGIN VIEW marker\n";
        }
    } else {
        echo "❌ login-new.blade.php does NOT exist\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";
echo "Now test: http://156.67.221.184/login\n";
echo "Should show NEW LOGIN VIEW with working modal\n";
