<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing second card tap (Hans Timothy Samson)...\n";

try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'E6415F5F',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ SUCCESS: {$data['message']}\n";
        echo "🎯 Action: {$data['action']}\n";
    } else {
        echo "❌ FAILED: {$data['message']}\n";
        echo "🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

// Check all active sessions
echo "\nChecking all active sessions...\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "📊 Total active sessions: " . $activeSessions->count() . "\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "   Checked in: {$session->check_in_time}\n";
    echo "   Session duration: {$session->currentDuration}\n";
}

echo "\n🎯 Both cards should now appear in Currently Active Members!\n";
