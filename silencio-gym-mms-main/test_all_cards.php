<?php
/**
 * Test All RFID Cards
 * Check why some cards can tap in and others can't
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;
use App\Models\ActiveSession;

echo "🎯 TESTING ALL RFID CARDS\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Get all members with UIDs
$members = Member::whereNotNull('uid')->get();

echo "📋 ALL REGISTERED MEMBERS:\n";
echo str_repeat("-", 40) . "\n";

foreach ($members as $index => $member) {
    echo ($index + 1) . ". {$member->member_number}: {$member->uid}\n";
    echo "   Name: {$member->first_name} {$member->last_name}\n";
    echo "   Status: {$member->status}\n";
    echo "   Role: {$member->role}\n";
    
    // Check if member has active session
    $activeSession = ActiveSession::where('member_id', $member->id)->first();
    if ($activeSession) {
        echo "   ⚠️ ACTIVE SESSION: Checked in at {$activeSession->check_in_time}\n";
    } else {
        echo "   ✅ No active session\n";
    }
    echo "\n";
}

echo "🧪 TESTING CARD TAPS:\n";
echo str_repeat("-", 25) . "\n";

// Clear all active sessions first
ActiveSession::truncate();
echo "✅ Cleared all active sessions\n";

foreach ($members as $member) {
    echo "Testing {$member->first_name} {$member->last_name} (UID: {$member->uid})...\n";
    
    // Simulate card tap via API
    try {
        $request = request()->merge([
            'uid' => $member->uid,
            'device_id' => 'main_reader'
        ]);
        
        $response = app(\App\Http\Controllers\RfidController::class)->handleCardTap($request);
        
        echo "   ✅ Response: " . $response->getStatusCode() . "\n";
        
        $responseData = json_decode($response->getContent(), true);
        echo "   🎯 Action: " . ($responseData['action'] ?? 'unknown') . "\n";
        echo "   📋 Message: " . ($responseData['message'] ?? 'no message') . "\n";
        
        // Check if session was created
        $session = ActiveSession::where('member_id', $member->id)->first();
        if ($session) {
            echo "   📌 Session created: {$session->id}\n";
        } else {
            echo "   ❌ No session created\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "📊 FINAL STATUS:\n";
echo str_repeat("-", 20) . "\n";

$activeSessions = ActiveSession::with('member')->get();
echo "Active Sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    echo "   • {$session->member->first_name} {$session->member->last_name} (UID: {$session->member->uid})\n";
}

echo "\n✅ CARD TESTING COMPLETE!\n";
echo "=" . str_repeat("=", 50) . "\n";
?>
