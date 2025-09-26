<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use Illuminate\Support\Facades\DB;

echo "🔍 Checking current members in database...\n\n";

try {
    $members = Member::all(['id', 'uid', 'first_name', 'last_name', 'member_number']);
    
    if ($members->isEmpty()) {
        echo "❌ No members found in database. Please run the seeder first:\n";
        echo "   php artisan db:seed --class=MemberSeeder\n\n";
        exit(1);
    }
    
    echo "📋 Current members:\n";
    foreach ($members as $member) {
        echo "  • ID: {$member->id} | UID: {$member->uid} | Name: {$member->first_name} {$member->last_name} | Number: {$member->member_number}\n";
    }
    
    echo "\n🔄 Updating UIDs with actual RFID card UIDs...\n\n";
    
    // Map of current UIDs to actual physical card UIDs
    $uidMapping = [
        'UID001' => 'E6415F5F',  // Replace with actual card UID from your tap
        'UID002' => 'A1B2C3D4',  // Replace with actual card UID
        'UID003' => 'F5E6D7C8',  // Replace with actual card UID
        'UID004' => 'B9A8C7D6',  // Replace with actual card UID
        'UID005' => 'E5F4A3B2',  // Replace with actual card UID
    ];
    
    DB::beginTransaction();
    
    foreach ($members as $member) {
        if (isset($uidMapping[$member->uid])) {
            $newUid = $uidMapping[$member->uid];
            
            echo "📝 Updating {$member->first_name} {$member->last_name} from {$member->uid} to {$newUid}\n";
            
            // Check if new UID already exists
            $existingMember = Member::where('uid', $newUid)->where('id', '!=', $member->id)->first();
            if ($existingMember) {
                echo "⚠️  Warning: UID {$newUid} already exists for member {$existingMember->first_name} {$existingMember->last_name}\n";
                continue;
            }
            
            $member->update(['uid' => $newUid]);
            echo "✅ Successfully updated to {$newUid}\n";
        } else {
            echo "ℹ️  Skipping {$member->first_name} {$member->last_name} (UID: {$member->uid}) - not in mapping\n";
        }
    }
    
    DB::commit();
    echo "\n🎉 UID updates completed!\n";
    
    echo "\n📋 Updated members:\n";
    $updatedMembers = Member::all(['id', 'uid', 'first_name', 'last_name']);
    foreach ($updatedMembers as $member) {
        echo "  • {$member->first_name} {$member->last_name}: {$member->uid}\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n💡 Next steps:\n";
echo "1. Test the RFID card with UID E6415F5F\n";
echo "2. If it works, update the other UIDs in this script\n";
echo "3. Run this script again to update remaining cards\n";
echo "4. The RFID system should now recognize your physical cards!\n";
