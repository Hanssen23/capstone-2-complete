<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use App\Http\Controllers\RfidController;
use Illuminate\Http\Request;

echo "🧪 Testing RFID card recognition...\n\n";

// Test the RFID card tap
$request = new Request();
$request->merge(['card_uid' => 'E6415F5F', 'device_id' => 'test_reader']);

$controller = new RfidController();
$response = $controller->handleCardTap($request);
$data = json_decode($response->getContent(), true);

echo "📱 Testing card UID: E6415F5F\n";
echo "📊 Response:\n";
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

if ($data['success']) {
    echo "✅ SUCCESS: Card recognized!\n";
    echo "👤 Member: " . $data['member']['name'] . "\n";
    echo "🎯 Action: " . $data['action'] . "\n";
} else {
    echo "❌ FAILED: " . $data['message'] . "\n";
    echo "🔍 Action: " . $data['action'] . "\n";
}

echo "\n🔍 Checking member in database:\n";
$member = Member::where('uid', 'E6415F5F')->first();
if ($member) {
    echo "✅ Member found: {$member->first_name} {$member->last_name}\n";
    echo "📧 Email: {$member->email}\n";
    echo "📱 Mobile: {$member->mobile_number}\n";
    echo "🏷️  Status: {$member->status}\n";
    echo "💳 Membership: {$member->membership}\n";
} else {
    echo "❌ No member found with UID E6415F5F\n";
}
