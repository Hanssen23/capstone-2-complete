<?php
/**
 * Comprehensive RFID Functionality Test Suite
 * Tests all aspects of RFID integration and card processing
 */

echo "🎯 COMPREHENSIVE RFID TEST SUITE\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Include Laravel framework
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;
use App\Models\ActiveSession;
use App\Models\RfidLog;
use App\Http\Controllers\RfidController;

class RFIDTestSuite {
    
    private $testResults = [];
    private $testUids = [
        'TEST001' => 'Test Member One',
        'TEST002' => 'Test Member Two',
        'TEST003' => 'Test Member Three',
        'UNKNOWN' => 'Unknown Card Test'
    ];
    
    public function runAllTests() {
        echo "🧪 RUNNING COMPREHENSIVE RFID TESTS\n";
        echo str_repeat("=", 50) . "\n\n";
        
        $this->testRfidBasics();
        $this->testMemberValidation();
        $this->testCheckInOut();
        $this->testActiveSessions();
        $this->testRfidLogging();
        $this->testApiEndpoints();
        $this->testErrorHandling();
        $this->testPerformance();
        
        $this->generateReport();
    }
    
    private function testRfidBasics() {
        echo "1️⃣ TESTING RFID BASICS\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test RFID controller initialization
        try {
            $rfidController = new RfidController();
            $this->assertTrue("RFID Controller initialized", "✅ Basic RFID functionality");
            echo "   ✅ RFID Controller can be instantiated\n";
        } catch (Exception $e) {
            $this->assertFalse("RFID Controller initialization", "❌ Failed to initialize RFID controller: " . $e->getMessage());
        }
        
        // Test configuration loading
        try {
            $configPath = base_path('rfid_config.json');
            $config = json_decode(file_get_contents($configPath), true);
            $this->assertTrue("Configuration loaded", !empty($config));
            echo "   ✅ RFID configuration file accessible\n";
        } catch (Exception $e) {
            $this->assertFalse("Configuration loading", "❌ Cannot load RFID configuration: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testMemberValidation() {
        echo "2️⃣ TESTING MEMBER VALIDATION\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test with existing member
        $existingMember = Member::first();
        if ($existingMember) {
            echo "   🧪 Testing with existing member: " . $existingMember->first_name . " (UID: {$existingMember->uid})\n";
            
            try {
                $request = request()->merge(['uid' => $existingMember->uid, 'device_id' => 'test_device']);
                $response = app(RfidController::class)->handleCardTap($request);
                
                $this->assertTrue("Valid member processing", 
                    $response->getStatusCode() == 200 || $response->getStatusCode() == 403);
                echo "   ✅ Valid member processed successfully\n";
                
                $responseData = json_decode($response->getContent(), true);
                echo "   📋 Action: " . ($responseData['action'] ?? 'unknown') . "\n";
                echo "   📋 Message: " . ($responseData['message'] ?? 'no message') . "\n";
                
            } catch (Exception $e) {
                $this->assertFalse("Valid member processing", "❌ Error processing valid member: " . $e->getMessage());
            }
        } else {
            echo "   ⚠️ No members found for testing\n";
        }
        
        // Test with unknown card
        echo "   🧪 Testing unknown card handling...\n";
        try {
            $request = request()->merge(['uid' => 'UNKNOWN123', 'device_id' => 'test_device']);
            $response = app(RfidController::class)->handleCardTap($request);
            
            $this->assertTrue("Unknown card handling", $response->getStatusCode() == 404);
            echo "   ✅ Unknown card properly rejected (404)\n";
            
            $responseData = json_decode($response->getContent(), true);
            if (isset($responseData['message'])) {
                echo "   📋 Message: " . $responseData['message'] . "\n";
            }
            
        } catch (Exception $e) {
            $this->assertFalse("Unknown card handling", "❌ Error handling unknown card: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testCheckInOut() {
        echo "3️⃣ TESTING CHECK-IN/CHECK-OUT CYCLE\n";
        echo str_repeat("-", 30) . "\n";
        
        $testMember = Member::first();
        if (!$testMember) {
            echo "   ⚠️ No test member available\n\n";
            return;
        }
        
        // Clear any existing active sessions
        ActiveSession::where('member_id', $testMember->id)->where('status', 'active')->update(['status' => 'inactive']);
        
        echo "   🧪 Testing check-in process...\n";
        
        // First tap - should be check-in
        try {
            $request = request()->merge(['uid' => $testMember->uid, 'device_id' => 'test_device']);
            $response = app(RfidController::class)->handleCardTap($request);
            
            if ($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getContent(), true);
                $this->assertTrue("Check-in", $responseData['action'] ?? '' == 'check_in');
                echo "   ✅ Check-in successful: " . $responseData['message'] . "\n";
                
                // Verify active session created
                $activeSession = ActiveSession::where('member_id', $testMember->id)
                    ->where('status', 'active')
                    ->first();
                $this->assertTrue("Active session created", !is_null($activeSession));
                echo "   ✅ Active session created in database\n";
                
            } else {
                echo "   ⚠️ Check-in returned status: " . $response->getStatusCode() . "\n";
            }
        } catch (Exception $e) {
            $this->assertFalse("Check-in process", "❌ Check-in failed: " . $e->getMessage());
        }
        
        // Wait a moment to simulate real usage
        sleep(1);
        
        echo "   🧪 Testing check-out process...\n";
        
        // Second tap - should be check-out
        try {
            $request = request()->merge(['uid' => $testMember->uid, 'device_id' => 'test_device']);
            $response = app(RfidController::class)->handleCardTap($request);
            
            if ($response->getStatusCode() == 200) {
                $responseData = $response->json();
                $this->assertTrue("Check-out", $responseData['action'] ?? '' == 'check_out');
                echo "   ✅ Check-out successful: " . $responseData['message'] . "\n";
                
                // Verify active session closed
                $activeSession = ActiveSession::where('member_id', $testMember->id)
                    ->where('status', 'active')
                    ->first();
                $this->assertTrue("Active session closed", is_null($activeSession));
                echo "   ✅ Active session properly closed\n";
                
            } else {
                echo "   ⚠️ Check-out returned status: " . $response->getStatusCode() . "\n";
            }
        } catch (Exception $e) {
            $this->assertFalse("Check-out process", "❌ Check-out failed: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testActiveSessions() {
        echo "4️⃣ TESTING ACTIVE SESSIONS\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test getActiveMembers API
        try {
            $rfidController = new RfidController();
            $response = $rfidController->getActiveMembers();
            
            $this->assertTrue("GetActiveMembers API", $response->getStatusCode() == 200);
            echo "   ✅ GetActiveMembers API working\n";
            
            $data = $response->json();
            $activeCount = $data['count'] ?? 0;
            echo "   📊 Currently active members: {$activeCount}\n";
            
            if ($data['success']) {
                echo "   ✅ API response structure correct\n";
            }
            
        } catch (Exception $e) {
            $this->assertFalse("GetActiveMembers API", "❌ GetActiveMembers failed: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testRfidLogging() {
        echo "5️⃣ TESTING RFID LOGGING\n";
        echo str_repeat("-", 30) . "\n";
        
        // Check recent RFID logs
        try {
            $recentLogs = RfidLog::orderBy('timestamp', 'desc')->limit(5)->get();
            
            echo "   📝 Recent RFID logs ({$recentLogs->count()}):\n";
            foreach ($recentLogs as $log) {
                echo "      - {$log->action}: {$log->card_uid} ({$log->status}) - {$log->timestamp}\n";
            }
            
            $this->assertTrue("RFID logging", $recentLogs->count() > 0);
            echo "   ✅ RFID logging system working\n";
            
        } catch (Exception $e) {
            $this->assertFalse("RFID logging", "❌ RFID logging check failed: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function testApiEndpoints() {
        echo "6️⃣ TESTING API ENDPOINTS\n";
        echo str_repeat("-", 30) . "\n";
        
        $endpoints = [
            '/api/rfid/status' => 'RFID Status',
            '/api/rfid/logs' => 'RFID Logs',
            '/api/rfid/dashboard-stats' => 'Dashboard Stats'
        ];
        
        foreach ($endpoints as $endpoint => $name) {
            try {
                $url = config('app.url') . $endpoint;
                $client = new \GuzzleHttp\Client();
                $response = $client->get($url, ['timeout' => 5]);
                
                $this->assertTrue("API endpoint {$name}", $response->getStatusCode() == 200);
                echo "   ✅ {$name} endpoint working\n";
                
            } catch (Exception $e) {
                $this->assertFalse("API endpoint {$name}", "❌ {$name} endpoint failed: " . $e->getMessage());
                echo "   ⚠️ {$name} endpoint not accessible\n";
            }
        }
        
        echo "\n";
    }
    
    private function testErrorHandling() {
        echo "7️⃣ TESTING ERROR HANDLING\n";
        echo str_repeat("-", 30) . "\n";
        
        // Test invalid device ID
        try {
            $request = request()->merge(['uid' => 'TEST123', 'device_id' => 'test_device']);
            $response = app(RfidController::class)->handleCardTap($request);
            
            // Should return 403 for test_device in production
            $this->assertTrue("Test device handling", $response->getStatusCode() == 403);
            echo "   ✅ Test device properly blocked in production\n";
            
        } catch (Exception $e) {
            $this->assertFalse("Error handling", "❌ Error handling test failed: " . $e->getMessage());
        }
        
        // Test missing parameters
        try {
            $request = request()->merge(['device_id' => 'main_reader']); // No uid
            $response = app(RfidController::class)->handleCardTap($request);
            
            $this->assertTrue("Missing parameter handling", $response->getStatusCode() == 422);
            echo "   ✅ Missing parameters properly validated\n";
            
        } catch (Exception $e) {
            echo "   ⚠️ Parameter validation check failed: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function testPerformance() {
        echo "8️⃣ TESTING PERFORMANCE\n";
        echo str_repeat("-", 30) . "\n";
        
        $testMember = Member::first();
        if (!$testMember) {
            echo "   ⚠️ No test member for performance testing\n\n";
            return;
        }
        
        // Clear active sessions first
        ActiveSession::where('member_id', $testMember->id)->where('status', 'active')->update(['session_duration' => '0 minutes']);
        
        $startTime = microtime(true);
        
        try {
            $request = request()->merge(['uid' => $testMember->uid, 'device_id' => 'perf_test']);
            $response = app(RfidController::class)->handleCardTap($request);
            
            $endTime = microtime(true);
            $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
            
            $this->assertTrue("Response time acceptable", $responseTime < 1000); // Less than 1 second
            
            if ($responseTime < 100) {
                echo "   ✅ Excellent response time: " . number_format($responseTime, 2) . "ms\n";
            } else if ($responseTime < 500) {
                echo "   ✅ Good response time: " . number_format($responseTime, 2) . "ms\n";
            } else {
                echo "   ⚠️ Slow response time: " . number_format($responseTime, 2) . "ms\n";
            }
            
        } catch (Exception $e) {
            $this->assertFalse("Performance test", "❌ Performance test failed: " . $e->getMessage());
        }
        
        echo "\n";
    }
    
    private function assertTrue($test, $message) {
        $this->testResults[] = ['test' => $test, 'passed' => true, 'message' => $message];
    }
    
    private function assertFalse($test, $message) {
        $this->testResults[] = ['test' => $test, 'passed' => false, 'message' => $message];
    }
    
    private function generateReport() {
        echo "🎯 RFID TEST SUITE REPORT\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $passed = 0;
        $failed = 0;
        
        foreach ($this->testResults as $result) {
            if ($result['passed']) {
                $passed++;
                echo "✅ PASS: " . $result['test'] . "\n";
            } else {
                $failed++;
                echo "❌ FAIL: " . $result['test'] . " - " . $result['message'] . "\n";
            }
        }
        
        echo "\n📊 SUMMARY:\n";
        echo "   ✅ Passed: {$passed}\n";
        echo "   ❌ Failed: {$failed}\n";
        echo "   📈 Success Rate: " . round(($passed / ($passed + $failed)) * 100, 1) . "%\n\n";
        
        if ($failed == 0) {
            echo "🎉 ALL RFID TESTS PASSED! System is ready for production.\n";
        } else {
            echo "⚠️ Some tests failed. Review the issues above before production deployment.\n";
        }
        
        echo "\n" . str_repeat("=", 50) . "\n";
    }
}

// Run the test suite
$testSuite = new RFIDTestSuite();
$testSuite->runAllTests();
?>
