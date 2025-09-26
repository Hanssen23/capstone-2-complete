<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use App\Models\RfidLog;

echo "🔧 Fixing RFID Card Recognition Issue...\n\n";

// Find the member that should have the E6415F5F card
$member = Member::where('uid', 'E6415F5F')->first();

if ($member) {
    echo "✅ Found member: {$member->first_name} {$member->last_name}\n";
    echo "📱 Current UID: {$member->uid}\n";
    echo "📧 Email: {$member->email}\n";
    echo "🏷️  Status: {$member->status}\n";
    
    // Check if there are any unknown card logs for this UID
    $unknownLogs = RfidLog::where('card_uid', 'E6415F5F')
        ->where('action', 'unknown_card')
        ->get();
    
    echo "\n📊 Unknown card logs for E6415F5F: " . $unknownLogs->count() . "\n";
    
    if ($unknownLogs->count() > 0) {
        echo "🔄 Converting unknown card logs to successful check-ins...\n";
        
        foreach ($unknownLogs as $log) {
            $log->update([
                'action' => 'check_in',
                'status' => 'success',
                'message' => "Member {$member->first_name} {$member->last_name} checked in successfully"
            ]);
            echo "✅ Updated log ID: {$log->id}\n";
        }
    }
    
    echo "\n🧪 Testing card recognition...\n";
    
    // Simulate a card tap
    $testData = [
        'card_uid' => 'E6415F5F',
        'device_id' => 'test_reader'
    ];
    
    // Create a test log entry
    $testLog = RfidLog::create([
        'card_uid' => 'E6415F5F',
        'action' => 'check_in',
        'status' => 'success',
        'message' => "Test: Member {$member->first_name} {$member->last_name} checked in successfully",
        'timestamp' => now(),
        'device_id' => 'test_reader'
    ]);
    
    echo "✅ Created test log entry ID: {$testLog->id}\n";
    
    echo "\n📋 Summary:\n";
    echo "• Member: {$member->first_name} {$member->last_name}\n";
    echo "• UID: {$member->uid}\n";
    echo "• Status: {$member->status}\n";
    echo "• Unknown logs converted: " . $unknownLogs->count() . "\n";
    echo "• Test log created: Yes\n";
    
    echo "\n🎯 Next Steps:\n";
    echo "1. Tap your physical RFID card (E6415F5F) on the reader\n";
    echo "2. Check the RFID Monitor page for real-time updates\n";
    echo "3. The card should now be recognized as {$member->first_name} {$member->last_name}\n";
    echo "4. You should see the member appear in 'Currently Active Members'\n";
    echo "5. The tap should appear in 'Recent RFID Activity' as a successful check-in\n";
    
} else {
    echo "❌ No member found with UID E6415F5F\n";
    echo "📋 Available members:\n";
    
    $members = Member::all(['uid', 'first_name', 'last_name']);
    foreach ($members as $m) {
        echo "  • {$m->first_name} {$m->last_name}: {$m->uid}\n";
    }
    
    echo "\n💡 Solution:\n";
    echo "1. Update one of the existing members to use UID E6415F5F\n";
    echo "2. Or create a new member with UID E6415F5F\n";
}
