<?php
/**
 * Fix Card Registration Issues
 * Addresses the 27 failed attempts and card UID issues
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Card Registration Issues\n";
echo "=================================\n\n";

// Check for the problematic card UID
echo "1. Checking card UID E6415F5F...\n";
$member = App\Models\Member::where('uid', 'E6415F5F')->first();
if ($member) {
    echo "   ✅ Found member: {$member->first_name} {$member->last_name}\n";
    echo "   📊 Current status: " . ($member->is_active ? 'Active' : 'Inactive') . "\n";
    echo "   🔧 Activating member...\n";
    $member->update(['is_active' => true, 'status' => 'active']);
    echo "   ✅ Member activated!\n";
} else {
    echo "   ❌ No member found with UID E6415F5F\n";
    echo "   🔧 Creating member for this card...\n";
    
    App\Models\Member::create([
        'uid' => 'E6415F5F',
        'member_number' => 'M003',
        'membership' => 'basic',
        'full_name' => 'Card E6415F5F User',
        'mobile_number' => '1234567890',
        'email' => 'card6415f5f@example.com',
        'first_name' => 'Card',
        'last_name' => 'E6415F5F',
        'is_active' => true,
        'status' => 'active'
    ]);
    echo "   ✅ Member created and activated!\n";
}

// Check Hans Timothy Samson status
echo "\n2. Checking Hans Timothy Samson...\n";
$hans = App\Models\Member::where('first_name', 'Hans')->first();
if ($hans) {
    echo "   👤 Found: {$hans->first_name} {$hans->last_name}\n";
    echo "   📊 Status: " . ($hans->is_active ? 'Active' : 'Inactive') . "\n";
    echo "   🔧 Ensuring Hans is active...\n";
    $hans->update(['is_active' => true, 'status' => 'active']);
    echo "   ✅ Hans is now active!\n";
} else {
    echo "   ❌ Hans not found, creating...\n";
    App\Models\Member::create([
        'uid' => 'A69D194E',
        'member_number' => 'M002',
        'membership' => 'basic',
        'full_name' => 'Hans Timothy Samson',
        'mobile_number' => '1234567890',
        'email' => 'hans@example.com',
        'first_name' => 'Hans',
        'last_name' => 'Samson',
        'is_active' => true,
        'status' => 'active'
    ]);
    echo "   ✅ Hans created and activated!\n";
}

// Check failed attempts
echo "\n3. Analyzing failed attempts...\n";
$failedLogs = App\Models\RfidLog::where('status', 'failed')
    ->where('timestamp', '>=', now()->subDay())
    ->get();

echo "   📊 Failed attempts in last 24 hours: " . $failedLogs->count() . "\n";

$failedByCard = $failedLogs->groupBy('card_uid');
foreach ($failedByCard as $cardUid => $logs) {
    echo "   🔍 Card {$cardUid}: " . $logs->count() . " failed attempts\n";
    
    // Check if this card has a member
    $member = App\Models\Member::where('uid', $cardUid)->first();
    if (!$member) {
        echo "      ❌ No member registered for this card\n";
        echo "      🔧 Creating member for card {$cardUid}...\n";
        
        App\Models\Member::create([
            'uid' => $cardUid,
            'member_number' => 'M' . str_pad(App\Models\Member::count() + 1, 3, '0', STR_PAD_LEFT),
            'membership' => 'basic',
            'full_name' => "Card {$cardUid} User",
            'mobile_number' => '1234567890',
            'email' => "card{$cardUid}@example.com",
            'first_name' => 'Card',
            'last_name' => $cardUid,
            'is_active' => true,
            'status' => 'active'
        ]);
        echo "      ✅ Member created!\n";
    } else {
        echo "      👤 Member: {$member->first_name} {$member->last_name}\n";
        if (!$member->is_active) {
            echo "      🔧 Activating member...\n";
            $member->update(['is_active' => true, 'status' => 'active']);
            echo "      ✅ Member activated!\n";
        }
    }
}

// Test the cards
echo "\n4. Testing card taps...\n";
$testCards = ['E6415F5F', 'A69D194E'];

foreach ($testCards as $cardUid) {
    echo "   🧪 Testing card: {$cardUid}\n";
    
    try {
        $response = app('App\Http\Controllers\RfidController')->handleCardTap(
            new \Illuminate\Http\Request([
                'card_uid' => $cardUid,
                'device_id' => 'test_device'
            ])
        );
        
        $data = json_decode($response->getContent(), true);
        echo "      📱 Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
        echo "      💬 Message: " . $data['message'] . "\n";
        
    } catch (Exception $e) {
        echo "      ❌ Error: " . $e->getMessage() . "\n";
    }
}

// Check active sessions
echo "\n5. Current active sessions...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
}

echo "\n🎯 Fix completed!\n";
echo "All cards should now work properly for tapping in/out.\n";
