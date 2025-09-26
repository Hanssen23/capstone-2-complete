<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Testing RFID System Online/Offline Functionality\n";
echo "==================================================\n\n";

// Test 1: Check if RFID reader is currently running
echo "1. Checking current RFID reader status...\n";
$rfidController = app('App\Http\Controllers\RfidController');
$reflection = new ReflectionClass($rfidController);
$isRfidReaderRunning = $reflection->getMethod('isRfidReaderRunning');
$isRfidReaderRunning->setAccessible(true);
$isRunning = $isRfidReaderRunning->invoke($rfidController);

echo "   📊 RFID Reader Status: " . ($isRunning ? "✅ RUNNING" : "❌ STOPPED") . "\n";

// Test 2: Try to tap a card when system is offline (if it's offline)
if (!$isRunning) {
    echo "\n2. Testing card tap when system is OFFLINE...\n";
    try {
        $response = $rfidController->handleCardTap(
            new \Illuminate\Http\Request([
                'card_uid' => 'A69D194E',
                'device_id' => 'main_reader'
            ])
        );
        
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "   ❌ ERROR: Card tap succeeded when system should be offline!\n";
        } else {
            echo "   ✅ SUCCESS: Card tap properly blocked when system is offline\n";
            echo "   📝 Message: {$data['message']}\n";
            echo "   🎯 Action: {$data['action']}\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    }
} else {
    echo "\n2. System is currently ONLINE - testing normal functionality...\n";
    try {
        $response = $rfidController->handleCardTap(
            new \Illuminate\Http\Request([
                'card_uid' => 'A69D194E',
                'device_id' => 'main_reader'
            ])
        );
        
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            echo "   ✅ SUCCESS: Card tap works when system is online\n";
            echo "   📝 Message: {$data['message']}\n";
            echo "   🎯 Action: {$data['action']}\n";
        } else {
            echo "   ❌ FAILED: Card tap failed when system should be online\n";
            echo "   📝 Message: {$data['message']}\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    }
}

// Test 3: Test Start RFID functionality
echo "\n3. Testing Start RFID functionality...\n";
try {
    $response = $rfidController->startRfidSystem();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ SUCCESS: RFID system start command executed\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Status: {$data['status']}\n";
        
        // Wait a moment and check if it's actually running
        sleep(3);
        $isRunningAfter = $isRfidReaderRunning->invoke($rfidController);
        echo "   📊 Status after start: " . ($isRunningAfter ? "✅ RUNNING" : "❌ STOPPED") . "\n";
        
    } else {
        echo "   ❌ FAILED: RFID system start failed\n";
        echo "   📝 Message: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 4: Test Stop RFID functionality
echo "\n4. Testing Stop RFID functionality...\n";
try {
    $response = $rfidController->stopRfidSystem();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ✅ SUCCESS: RFID system stop command executed\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Status: {$data['status']}\n";
        
        // Wait a moment and check if it's actually stopped
        sleep(2);
        $isRunningAfterStop = $isRfidReaderRunning->invoke($rfidController);
        echo "   📊 Status after stop: " . ($isRunningAfterStop ? "✅ RUNNING" : "❌ STOPPED") . "\n";
        
    } else {
        echo "   ❌ FAILED: RFID system stop failed\n";
        echo "   📝 Message: {$data['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

// Test 5: Test card tap after stopping
echo "\n5. Testing card tap after stopping RFID system...\n";
try {
    $response = $rfidController->handleCardTap(
        new \Illuminate\Http\Request([
            'card_uid' => 'A69D194E',
            'device_id' => 'main_reader'
        ])
    );
    
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "   ❌ ERROR: Card tap succeeded when system should be offline!\n";
    } else {
        echo "   ✅ SUCCESS: Card tap properly blocked when system is offline\n";
        echo "   📝 Message: {$data['message']}\n";
        echo "   🎯 Action: {$data['action']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n🎯 RFID System Online/Offline Test Complete!\n";
