<?php
/**
 * Check Member UIDs and Ensure Proper Reading
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Member UIDs\n";
echo "======================\n\n";

// Get all members
$members = App\Models\Member::all();

echo "📊 Current Members in Database:\n";
echo str_repeat('-', 80) . "\n";
printf("%-5s %-15s %-25s %-10s %-8s\n", "ID", "UID", "Name", "Status", "Active");
echo str_repeat('-', 80) . "\n";

foreach ($members as $member) {
    printf("%-5s %-15s %-25s %-10s %-8s\n", 
        $member->id, 
        $member->uid, 
        $member->first_name . ' ' . $member->last_name,
        $member->status ?? 'N/A',
        $member->is_active ? 'Yes' : 'No'
    );
}

echo "\n🧪 Testing UID Reading:\n";
echo "======================\n";

// Test each UID
foreach ($members as $member) {
    echo "Testing UID: {$member->uid} ({$member->first_name} {$member->last_name})\n";
    
    try {
        $response = app('App\Http\Controllers\RfidController')->handleCardTap(
            new \Illuminate\Http\Request([
                'card_uid' => $member->uid,
                'device_id' => 'test_device'
            ])
        );
        
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "  ✅ SUCCESS: {$data['message']}\n";
            echo "  🎯 Action: {$data['action']}\n";
        } else {
            echo "  ❌ FAILED: {$data['message']}\n";
            echo "  🎯 Action: {$data['action']}\n";
        }
        
    } catch (Exception $e) {
        echo "  ❌ ERROR: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

// Check active sessions
echo "📊 Current Active Sessions:\n";
echo "===========================\n";

$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();

if ($activeSessions->count() > 0) {
    foreach ($activeSessions as $session) {
        $member = $session->member;
        echo "👤 {$member->first_name} {$member->last_name}\n";
        echo "   UID: {$member->uid}\n";
        echo "   Checked in: {$session->check_in_time}\n";
        echo "   Session duration: {$session->currentDuration}\n";
    }
} else {
    echo "No active sessions currently.\n";
}

echo "\n🔧 UID Validation Check:\n";
echo "========================\n";

// Check for any UID issues
$uidIssues = [];
foreach ($members as $member) {
    if (empty($member->uid)) {
        $uidIssues[] = "Member {$member->id} ({$member->first_name} {$member->last_name}) has empty UID";
    }
    
    if (strlen($member->uid) < 3) {
        $uidIssues[] = "Member {$member->id} ({$member->first_name} {$member->last_name}) has short UID: {$member->uid}";
    }
}

if (count($uidIssues) > 0) {
    echo "❌ UID Issues Found:\n";
    foreach ($uidIssues as $issue) {
        echo "   - {$issue}\n";
    }
} else {
    echo "✅ All UIDs are valid and properly formatted.\n";
}

echo "\n🎯 Summary:\n";
echo "===========\n";
echo "Total members: " . $members->count() . "\n";
echo "Active members: " . $members->where('is_active', true)->count() . "\n";
echo "Active sessions: " . $activeSessions->count() . "\n";
echo "UID issues: " . count($uidIssues) . "\n";

if (count($uidIssues) == 0) {
    echo "\n✅ All member UIDs are reading correctly!\n";
} else {
    echo "\n⚠️  Some UID issues need to be fixed.\n";
}
