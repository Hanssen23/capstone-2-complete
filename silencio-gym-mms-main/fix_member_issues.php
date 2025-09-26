<?php
/**
 * Fix member status and database issues
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Member Status and Database Issues\n";
echo "==========================================\n\n";

// Check database connection
echo "1. Testing database connection...\n";
try {
    DB::connection()->getPdo();
    echo "   ✅ Database connection successful\n";
} catch (Exception $e) {
    echo "   ❌ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Check members with issues
echo "\n2. Checking members with issues...\n";
$members = App\Models\Member::where('first_name', 'Hans')
    ->orWhere('first_name', 'John')
    ->get();

foreach ($members as $member) {
    echo "   👤 {$member->first_name} {$member->last_name}\n";
    echo "      - UID: {$member->uid}\n";
    echo "      - Status: " . ($member->is_active ? 'Active' : 'Inactive') . "\n";
    echo "      - Member Status: {$member->status}\n";
    
    // Fix Hans Timothy Samson - activate him
    if ($member->first_name === 'Hans' && $member->last_name === 'Samson') {
        echo "      🔧 Fixing Hans Timothy Samson status...\n";
        $member->update([
            'is_active' => true,
            'status' => 'active'
        ]);
        echo "      ✅ Hans Timothy Samson activated\n";
    }
}

// Check active sessions
echo "\n3. Checking active sessions...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->get();
echo "   📊 Currently active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} - Checked in: {$session->check_in_time}\n";
}

// Clear any problematic sessions
echo "\n4. Cleaning up problematic sessions...\n";
$problematicSessions = App\Models\ActiveSession::where('status', 'active')
    ->where('check_in_time', '<', now()->subHours(24))
    ->get();

if ($problematicSessions->count() > 0) {
    echo "   🧹 Found {$problematicSessions->count()} old sessions, cleaning up...\n";
    foreach ($problematicSessions as $session) {
        $session->update(['status' => 'inactive']);
        echo "   ✅ Cleaned session for {$session->member->first_name} {$session->member->last_name}\n";
    }
} else {
    echo "   ✅ No problematic sessions found\n";
}

// Test RFID logging
echo "\n5. Testing RFID logging...\n";
try {
    $testLog = App\Models\RfidLog::create([
        'card_uid' => 'TEST123',
        'action' => 'check_in',
        'status' => 'success',
        'message' => 'Database test log',
        'timestamp' => now(),
        'device_id' => 'test_device'
    ]);
    
    echo "   ✅ RFID logging test successful\n";
    
    // Clean up test log
    $testLog->delete();
    echo "   🧹 Test log cleaned up\n";
    
} catch (Exception $e) {
    echo "   ❌ RFID logging test failed: " . $e->getMessage() . "\n";
}

echo "\n🎯 Fix completed!\n";
echo "Members should now be able to tap in/out without issues.\n";
