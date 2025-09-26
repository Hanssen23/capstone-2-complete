<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Member;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔄 Updating member UIDs with actual RFID card UIDs...\n\n";

// Map of current fake UIDs to actual physical card UIDs
$uidMapping = [
    'UID001' => 'E6415F5F',  // Replace with actual card UID from your tap
    'UID002' => 'A1B2C3D4',  // Replace with actual card UID
    'UID003' => 'F5E6D7C8',  // Replace with actual card UID
    'UID004' => 'B9A8C7D6',  // Replace with actual card UID
    'UID005' => 'E5F4A3B2',  // Replace with actual card UID
];

try {
    DB::beginTransaction();
    
    foreach ($uidMapping as $oldUid => $newUid) {
        $member = Member::where('uid', $oldUid)->first();
        
        if ($member) {
            echo "📝 Updating {$member->first_name} {$member->last_name} from {$oldUid} to {$newUid}\n";
            
            // Check if new UID already exists
            $existingMember = Member::where('uid', $newUid)->first();
            if ($existingMember) {
                echo "⚠️  Warning: UID {$newUid} already exists for member {$existingMember->first_name} {$existingMember->last_name}\n";
                continue;
            }
            
            $member->update(['uid' => $newUid]);
            echo "✅ Successfully updated to {$newUid}\n";
        } else {
            echo "❌ Member with UID {$oldUid} not found\n";
        }
    }
    
    DB::commit();
    echo "\n🎉 All member UIDs updated successfully!\n";
    echo "\n📋 Updated UIDs:\n";
    
    // Display updated members
    $updatedMembers = Member::all();
    foreach ($updatedMembers as $member) {
        echo "  • {$member->first_name} {$member->last_name}: {$member->uid}\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error updating UIDs: " . $e->getMessage() . "\n";
}

echo "\n💡 Next steps:\n";
echo "1. Test each physical RFID card to verify the UIDs match\n";
echo "2. Update the UID mapping in this script if needed\n";
echo "3. Run this script again if you need to update more UIDs\n";
echo "4. The RFID system should now recognize your physical cards!\n";
