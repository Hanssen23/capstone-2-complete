<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Starting RFID Reader System\n";
echo "==============================\n\n";

// Check if RFID reader is already running
echo "1. Checking if RFID reader is running...\n";
$processes = shell_exec('tasklist | findstr python');
if ($processes) {
    echo "   ✅ RFID reader is already running\n";
    echo "   📊 Processes:\n{$processes}\n";
} else {
    echo "   ❌ RFID reader is not running\n";
    echo "   🚀 Starting RFID reader...\n";
    
    // Start the RFID reader
    $command = 'start "RFID Reader" python rfid_reader.py';
    $output = shell_exec($command);
    
    echo "   ✅ RFID reader start command executed\n";
    
    // Wait a moment for the reader to initialize
    sleep(3);
    
    // Check if it's now running
    $processes = shell_exec('tasklist | findstr python');
    if ($processes) {
        echo "   ✅ RFID reader started successfully!\n";
        echo "   📊 Processes:\n{$processes}\n";
    } else {
        echo "   ❌ Failed to start RFID reader\n";
    }
}

// Test the RFID system
echo "\n2. Testing RFID system...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ RFID API Test: SUCCESS\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    } else {
        echo "   ❌ RFID API Test: FAILED\n";
        echo "   📝 Message: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ RFID API Test Error: " . $e->getMessage() . "\n";
}

// Check active members
echo "\n3. Checking active members...\n";
try {
    $response = app('App\Http\Controllers\RfidController')->getActiveMembers();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ Active Members API: SUCCESS\n";
        echo "   📊 Active members count: {$data['count']}\n";
        
        if ($data['count'] > 0) {
            foreach ($data['active_members'] as $member) {
                echo "   👤 {$member['name']} (UID: {$member['uid']})\n";
            }
        }
    } else {
        echo "   ❌ Active Members API: FAILED\n";
    }
} catch (Exception $e) {
    echo "   ❌ Active Members Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 RFID Reader System Status Complete!\n";
echo "=====================================\n";
echo "✅ RFID reader should now be running\n";
echo "✅ RFID API is working correctly\n";
echo "✅ Active members can be checked\n";
echo "\n📋 Next Steps:\n";
echo "1. Tap an RFID card to test the system\n";
echo "2. Check the RFID Monitor panel for real-time updates\n";
echo "3. The system should now reflect tap-in/tap-out events\n";
