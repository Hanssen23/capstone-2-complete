<?php
/**
 * UID Management System for Silencio Gym
 * Prevents duplicate UID errors and provides UID validation
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Member;

class UidManagementSystem {
    
    public static function isUidAvailable($uid) {
        /** Check if a UID is available for use */
        return !Member::where('uid', strtoupper($uid))->exists();
    }
    
    public static function generateUniqueUid() {
        /** Generate a new unique UID */
        do {
            $uid = strtoupper(str_pad(decHex(mt_rand(0x10000000, 0xFFFFFFFF)), 8, '0', STR_PAD_LEFT));
        } while (!self::isUidAvailable($uid));
        
        return $uid;
    }
    
    public static function validateUidFormat($uid) {
        /** Validate UID format (8 characters, alphanumeric) */
        return preg_match('/^[A-F0-9]{8}$/i', $uid);
    }
    
    public static function findAvailableUids($count = 10) {
        /** Find multiple available UIDs */
        $availableUids = [];
        $attempts = 0;
        $maxAttempts = $count * 5; // Prevent infinite loops
        
        while (count($availableUids) < $count && $attempts < $maxAttempts) {
            $uid = strtoupper(str_pad(decHex(mt_rand(0x10000000, 0xFFFFFFFF)), 8, '0', STR_PAD_LEFT));
            
            if (self::isUidAvailable($uid) && !in_array($uid, $availableUids)) {
                $availableUids[] = $uid;
            }
            $attempts++;
        }
        
        return $availableUids;
    }
    
    public static function getUidUsageReport() {
        /** Generate a comprehensive UID usage report */
        $allMembers = Member::select('id', 'uid', 'first_name', 'last_name', 'member_number', 'status', 'created_at')
            ->orderBy('uid')
            ->get();
        
        $report = [];
        $report['total_used_uids'] = $allMembers->count();
        $report['members_by_status'] = $allMembers->groupBy('status');
        $report['recent_members'] = $allMembers->take(10);
        
        // Check for potential duplicates (shouldn't happen with proper constraints)
        $uidGroups = $allMembers->groupBy('uid');
        $duplicates = $uidGroups->filter(function($group) {
            return $group->count() > 1;
        });
        
        $report['duplicates'] = $duplicates;
        
        return $report;
    }
}

echo "🎯 UID MANAGEMENT SYSTEM\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test UID availability
echo "🔍 TESTING UID AVAILABILITY\n";
echo str_repeat("-", 30) . "\n";

$testUids = ['A69D194E', 'NEW12345', 'ABC12345', '99999999'];
foreach ($testUids as $uid) {
    $available = UidManagementSystem::isUidAvailable($uid);
    $status = $available ? '✅ Available' : '❌ Taken';
    echo "UID {$uid}: {$status}\n";
}

// Generate some unique UIDs
echo "\n🆕 GENERATING UNIQUE UIDS\n";
echo str_repeat("-", 25) . "\n";

$newUids = UidManagementSystem::findAvailableUids(5);
foreach ($newUids as $i => $uid) {
    echo ($i + 1) . ". {$uid}\n";
}

// Show UID usage report
echo "\n📊 UID USAGE REPORT\n";
echo str_repeat("-", 20) . "\n";

$report = UidManagementSystem::getUidUsageReport();
echo "Total Members: {$report['total_used_uids']}\n";

if ($report['duplicates']->count() > 0) {
    echo "⚠️ WARNING: {$report['duplicates']->count()} duplicate UIDs found!\n";
    foreach ($report['duplicates'] as $uid => $members) {
        echo "  {$uid}: " . $members->pluck('member_number')->join(', ') . "\n";
    }
} else {
    echo "✅ No duplicate UIDs found\n";
}

// Show status distribution
echo "\n📈 Members by Status:\n";
foreach ($report['members_by_status'] as $status => $members) {
    echo "  {$status}: {$members->count()}\n";
}

echo "\n✅ UID Management System Ready!\n";
echo "=" . str_repeat("=", 50) . "\n";
?>
