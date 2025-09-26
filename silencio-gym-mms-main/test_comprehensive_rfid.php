<?php
/**
 * Comprehensive RFID Test Script
 * Simulates real card tapping scenarios
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Comprehensive RFID Test\n";
echo "==========================\n\n";

// Test data
$testCards = [
    'A69D194E' => 'Hans Timothy Samson',
    '1' => 'John Doe',
    'E6415F5F' => 'Unknown Card',
];

foreach ($testCards as $uid => $expectedName) {
    echo "Testing card: {$uid} (Expected: {$expectedName})\n";
    echo str_repeat('-', 50) . "\n";
    
    $startTime = microtime(true);
    
    try {
        // Simulate API call
        $response = app('App\Http\Controllers\RfidController')->handleCardTap(
            new \Illuminate\Http\Request([
                'card_uid' => $uid,
                'device_id' => 'test_device'
            ])
        );
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        $data = json_decode($response->getContent(), true);
        
        echo "✅ Response Time: " . number_format($responseTime, 2) . "ms\n";
        echo "📱 Status: " . ($data['success'] ? 'SUCCESS' : 'FAILED') . "\n";
        echo "💬 Message: " . $data['message'] . "\n";
        echo "🎯 Action: " . $data['action'] . "\n";
        
        if (isset($data['member'])) {
            echo "👤 Member: " . $data['member']['name'] . "\n";
            echo "📧 Email: " . $data['member']['email'] . "\n";
            echo "🏷️  Plan: " . $data['member']['membership_plan'] . "\n";
        }
        
        if (isset($data['feedback'])) {
            echo "🔊 Feedback: " . $data['feedback']['message'] . "\n";
        }
        
    } catch (Exception $e) {
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "⏱️  Response Time: " . number_format($responseTime, 2) . "ms\n";
    }
    
    echo "\n";
    
    // Small delay between tests
    usleep(500000); // 500ms
}

// Check final state
echo "📊 Final Status Check\n";
echo "====================\n";

$activeMembers = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "Currently Active Members: " . $activeMembers->count() . "\n";

foreach ($activeMembers as $session) {
    $member = $session->member;
    echo "👤 {$member->first_name} {$member->last_name} (UID: {$member->uid})\n";
    echo "   Checked in: {$session->check_in_time}\n";
    echo "   Session duration: {$session->currentDuration}\n";
}

echo "\n📝 Recent RFID Logs (Last 5):\n";
$recentLogs = App\Models\RfidLog::orderBy('timestamp', 'desc')->limit(5)->get();
foreach ($recentLogs as $log) {
    $statusIcon = $log->status === 'success' ? '✅' : '❌';
    echo "{$statusIcon} {$log->action} - {$log->status} - {$log->timestamp->format('H:i:s')}\n";
    echo "   Card: {$log->card_uid} - {$log->message}\n";
}

echo "\n🎯 Test completed!\n";
echo "If all tests show response times under 500ms, the system is working optimally.\n";
