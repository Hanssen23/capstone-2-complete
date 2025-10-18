<?php
echo "Resetting OPcache...\n";

if (function_exists('opcache_reset')) {
    if (opcache_reset()) {
        echo "✅ OPcache reset successfully!\n";
    } else {
        echo "❌ Failed to reset OPcache\n";
    }
} else {
    echo "❌ OPcache not available\n";
}

if (function_exists('opcache_invalidate')) {
    $files = [
        '/var/www/html/app/Http/Controllers/AuthController.php',
        '/var/www/html/resources/views/login.blade.php',
        '/var/www/html/resources/views/login-new.blade.php'
    ];
    
    foreach ($files as $file) {
        if (file_exists($file)) {
            if (opcache_invalidate($file, true)) {
                echo "✅ Invalidated: $file\n";
            } else {
                echo "❌ Failed to invalidate: $file\n";
            }
        }
    }
}

echo "\nOPcache status:\n";
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status();
    echo "Enabled: " . ($status['opcache_enabled'] ? 'YES' : 'NO') . "\n";
    echo "Cache full: " . ($status['cache_full'] ? 'YES' : 'NO') . "\n";
    echo "Cached scripts: " . $status['opcache_statistics']['num_cached_scripts'] . "\n";
}
