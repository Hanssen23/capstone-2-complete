<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = new Application(realpath(__DIR__));
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing UID Pool Issues...\n";

// Reset the problematic UID B688164E
try {
    DB::table('uid_pool')
        ->where('uid', 'B688164E')
        ->update([
            'status' => 'available',
            'assigned_at' => null,
            'returned_at' => now(),
            'updated_at' => now()
        ]);
    
    echo "✅ Reset UID B688164E to available status\n";
} catch (Exception $e) {
    echo "❌ Error resetting UID: " . $e->getMessage() . "\n";
}

// Check for any other UIDs that might be in inconsistent state
try {
    $inconsistentUids = DB::table('uid_pool')
        ->where('status', 'available')
        ->whereNotNull('assigned_at')
        ->get();
    
    if ($inconsistentUids->count() > 0) {
        echo "Found " . $inconsistentUids->count() . " UIDs in inconsistent state:\n";
        
        foreach ($inconsistentUids as $uid) {
            echo "- " . $uid->uid . " (status: " . $uid->status . ", assigned_at: " . $uid->assigned_at . ")\n";
            
            // Reset these UIDs
            DB::table('uid_pool')
                ->where('uid', $uid->uid)
                ->update([
                    'assigned_at' => null,
                    'returned_at' => now(),
                    'updated_at' => now()
                ]);
        }
        
        echo "✅ Fixed all inconsistent UIDs\n";
    } else {
        echo "✅ No inconsistent UIDs found\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking inconsistent UIDs: " . $e->getMessage() . "\n";
}

// Show current UID pool status
try {
    $availableCount = DB::table('uid_pool')->where('status', 'available')->count();
    $assignedCount = DB::table('uid_pool')->where('status', 'assigned')->count();
    
    echo "\nUID Pool Status:\n";
    echo "- Available UIDs: " . $availableCount . "\n";
    echo "- Assigned UIDs: " . $assignedCount . "\n";
    echo "- Total UIDs: " . ($availableCount + $assignedCount) . "\n";
} catch (Exception $e) {
    echo "❌ Error getting UID pool status: " . $e->getMessage() . "\n";
}

echo "\n✅ UID Pool fix completed!\n";
