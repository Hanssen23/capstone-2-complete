#!/usr/bin/env php
<?php

/**
 * Seed UID Pool Script
 * 
 * This script seeds the UID pool with initial UIDs for member registration.
 * Run this script on the VPS to ensure the UID pool is populated.
 * 
 * Usage: php seed_uid_pool.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== UID Pool Seeder ===\n\n";

try {
    // Check current UID pool status
    $totalUids = DB::table('uid_pool')->count();
    $availableUids = DB::table('uid_pool')->where('status', 'available')->count();
    $assignedUids = DB::table('uid_pool')->where('status', 'assigned')->count();
    
    echo "Current UID Pool Status:\n";
    echo "  • Total UIDs: {$totalUids}\n";
    echo "  • Available UIDs: {$availableUids}\n";
    echo "  • Assigned UIDs: {$assignedUids}\n\n";
    
    // Predefined UIDs (from physical RFID cards)
    $predefinedUids = [
        'E6415F5F',
        'A69D194E',
        '56438A5F',
        'B696735F',
        'E69F8F40',
        '2665004E',
        'F665785F',
        'E6258C40',
        'B688164E',
    ];
    
    // Additional generated UIDs to prevent pool exhaustion
    $additionalUids = [
        'C1234567',
        'D2345678',
        'E3456789',
        'F4567890',
        'A5678901',
        'B6789012',
        'C7890123',
        'D8901234',
        'E9012345',
        'F0123456',
        'A1111111',
        'B2222222',
        'C3333333',
        'D4444444',
        'E5555555',
        'F6666666',
        'A7777777',
        'B8888888',
        'C9999999',
        'D0000000',
    ];
    
    $allUids = array_merge($predefinedUids, $additionalUids);
    
    echo "Seeding UID pool with " . count($allUids) . " UIDs...\n\n";
    
    $addedCount = 0;
    $skippedCount = 0;
    
    foreach ($allUids as $uid) {
        // Check if UID already exists
        $exists = DB::table('uid_pool')->where('uid', $uid)->exists();
        
        if (!$exists) {
            DB::table('uid_pool')->insert([
                'uid' => $uid,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "  ✅ Added UID: {$uid}\n";
            $addedCount++;
        } else {
            echo "  ⏭️  Skipped UID (already exists): {$uid}\n";
            $skippedCount++;
        }
    }
    
    echo "\n";
    echo "=== Seeding Complete ===\n";
    echo "  • UIDs added: {$addedCount}\n";
    echo "  • UIDs skipped: {$skippedCount}\n\n";
    
    // Check final UID pool status
    $totalUids = DB::table('uid_pool')->count();
    $availableUids = DB::table('uid_pool')->where('status', 'available')->count();
    $assignedUids = DB::table('uid_pool')->where('status', 'assigned')->count();
    
    echo "Final UID Pool Status:\n";
    echo "  • Total UIDs: {$totalUids}\n";
    echo "  • Available UIDs: {$availableUids}\n";
    echo "  • Assigned UIDs: {$assignedUids}\n\n";
    
    if ($availableUids > 0) {
        echo "✅ UID pool is ready! Members can now register.\n";
    } else {
        echo "⚠️  Warning: No available UIDs in the pool. All UIDs are assigned.\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n";

