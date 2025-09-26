<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Direct database update for member status...\n";

// Use direct database update
DB::table('members')->where('uid', 'A69D194E')->update(['status' => 'active']);
DB::table('members')->where('uid', 'E6415F5F')->update(['status' => 'active']);

echo "Members updated via direct database query\n";

// Verify the updates
$john = App\Models\Member::where('uid', 'A69D194E')->first();
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();

echo "John Doe status: '{$john->status}'\n";
echo "Hans status: '{$hans->status}'\n";

echo "\nNow testing card tap...\n";

// Test card tap
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ SUCCESS: {$data['message']}\n";
        echo "🎯 Action: {$data['action']}\n";
        
        // Check active sessions
        $activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
        echo "📊 Active sessions: " . $activeSessions->count() . "\n";
        
        foreach ($activeSessions as $session) {
            $member = $session->member;
            echo "👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
        }
    } else {
        echo "❌ FAILED: {$data['message']}\n";
        echo "🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
