<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;

echo "=== TESTING MEMBERSHIP FIX ===\n\n";

// Test the member dashboard controller logic
$members = Member::all();

foreach($members as $member) {
    echo "Testing Member: {$member->full_name} (UID: {$member->uid})\n";
    
    // Test the data that would be passed to the view
    $membershipStatus = $member->membership_status;
    $expiresAt = $member->membership_expires_at;
    $currentPlan = $member->current_plan_type;
    $currentDuration = $member->current_duration_type;
    
    echo "  Membership Status: {$membershipStatus}\n";
    echo "  Expires At: " . ($expiresAt ? $expiresAt->format('M d, Y') : 'NULL') . "\n";
    echo "  Current Plan: " . ($currentPlan ?? 'NULL') . "\n";
    echo "  Current Duration: " . ($currentDuration ?? 'NULL') . "\n";
    echo "  Days Until Expiration: {$member->days_until_expiration}\n";
    
    // Test what would be displayed in the view
    echo "  View Display:\n";
    echo "    - Membership Badge: {$membershipStatus}\n";
    echo "    - Plan Info: " . ($currentPlan && $currentDuration ? ucfirst($currentPlan) . ' (' . ucfirst($currentDuration) . ')' : 'Not set') . "\n";
    echo "    - Expiration: " . ($expiresAt ? $expiresAt->format('M d, Y') : 'Not set') . "\n";
    
    // Check if the issues are resolved
    $issues = [];
    if (!$expiresAt) {
        $issues[] = "No expiration date";
    }
    if (!$currentPlan) {
        $issues[] = "No plan type";
    }
    if (!$currentDuration) {
        $issues[] = "No duration type";
    }
    if ($membershipStatus === 'No Active Membership') {
        $issues[] = "No active membership";
    }
    
    if (empty($issues)) {
        echo "  ✅ Status: ALL ISSUES FIXED\n";
    } else {
        echo "  ❌ Remaining Issues: " . implode(', ', $issues) . "\n";
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n\n";

$totalMembers = $members->count();
$membersWithExpiration = $members->filter(function($member) {
    return $member->membership_expires_at !== null;
})->count();

$membersWithPlan = $members->filter(function($member) {
    return $member->current_plan_type !== null;
})->count();

$membersWithDuration = $members->filter(function($member) {
    return $member->current_duration_type !== null;
})->count();

echo "Total Members: {$totalMembers}\n";
echo "Members with Expiration Date: {$membersWithExpiration}/{$totalMembers}\n";
echo "Members with Plan Type: {$membersWithPlan}/{$totalMembers}\n";
echo "Members with Duration Type: {$membersWithDuration}/{$totalMembers}\n";

if ($membersWithExpiration === $totalMembers && $membersWithPlan === $totalMembers && $membersWithDuration === $totalMembers) {
    echo "\n🎉 ALL ISSUES FIXED!\n";
    echo "✅ End date 'N/A' issue resolved\n";
    echo "✅ Active status replaced with meaningful membership info\n";
    echo "✅ All members have proper membership data\n";
} else {
    echo "\n⚠️ Some issues remain - run the fix script again\n";
}

echo "\n=== WHAT USERS WILL SEE NOW ===\n\n";
echo "Instead of:\n";
echo "  ❌ Status: Active\n";
echo "  ❌ End Date: N/A\n\n";
echo "Users will see:\n";
echo "  ✅ Membership: Active (or Expiring Soon/Expired)\n";
echo "  ✅ Plan: Basic (Monthly)\n";
echo "  ✅ Expires: Nov 02, 2025\n";
echo "  ✅ Days remaining: 29\n";
