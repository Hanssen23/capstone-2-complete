<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use App\Models\UidPool;
use Illuminate\Support\Facades\DB;

echo "🔄 Setting up UID Pool System...\n\n";

try {
    DB::beginTransaction();
    
    // Step 1: Clear all existing members
    echo "🗑️  Clearing all existing members...\n";
    $memberCount = Member::count();
    echo "Found {$memberCount} members to delete.\n";
    
    // Delete all members (this will cascade to related records)
    Member::truncate();
    echo "✅ All members deleted successfully.\n\n";
    
    // Step 2: Clear existing UID pool
    echo "🗑️  Clearing existing UID pool...\n";
    UidPool::truncate();
    echo "✅ UID pool cleared.\n\n";
    
    // Step 3: Seed the UID pool with provided UIDs
    echo "📝 Seeding UID pool with provided UIDs...\n";
    $uids = [
        'E6415F5F',
        'A69D194E',
        '56438A5F',
        'B696735F',
        'E69F8F40',
        '2665004E',
        'F665785F',
        'E6258C40',
        'B688164E',
    ];
    
    foreach ($uids as $uid) {
        UidPool::create([
            'uid' => $uid,
            'status' => 'available',
        ]);
        echo "  ✅ Added UID: {$uid}\n";
    }
    
    echo "\n📊 UID Pool Status:\n";
    echo "  • Total UIDs: " . UidPool::count() . "\n";
    echo "  • Available UIDs: " . UidPool::available()->count() . "\n";
    echo "  • Assigned UIDs: " . UidPool::assigned()->count() . "\n";
    
    echo "\n📋 Available UIDs:\n";
    foreach (UidPool::available()->get() as $uidPool) {
        echo "  • {$uidPool->uid}\n";
    }
    
    DB::commit();
    echo "\n🎉 UID Pool system setup completed successfully!\n";
    echo "\n💡 Next steps:\n";
    echo "  1. Members can now register and will automatically get UIDs from the pool\n";
    echo "  2. When members are deleted, their UIDs will be returned to the pool\n";
    echo "  3. The system will prevent duplicate UID assignments\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ Error setting up UID pool system: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
