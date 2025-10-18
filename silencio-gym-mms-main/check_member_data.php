<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;
use App\Models\Payment;
use App\Models\MembershipPeriod;

echo "=== CHECKING MEMBER DATA FOR END DATE ISSUES ===\n\n";

$members = Member::with(['currentMembershipPeriod', 'payments'])->get();

foreach($members as $member) {
    echo "Member: {$member->full_name}\n";
    echo "UID: {$member->uid}\n";
    echo "Status: {$member->status}\n";
    echo "Membership Expires At: " . ($member->membership_expires_at ? $member->membership_expires_at : 'NULL') . "\n";
    echo "Current Plan: " . ($member->current_plan_type ?? 'NULL') . "\n";
    echo "Current Duration: " . ($member->current_duration_type ?? 'NULL') . "\n";
    echo "Current Membership Period: " . ($member->currentMembershipPeriod ? 'YES' : 'NO') . "\n";
    
    if($member->currentMembershipPeriod) {
        echo "  - Plan: {$member->currentMembershipPeriod->plan_type}\n";
        echo "  - Duration: {$member->currentMembershipPeriod->duration_type}\n";
        echo "  - Start: {$member->currentMembershipPeriod->start_date}\n";
        echo "  - End: {$member->currentMembershipPeriod->expiration_date}\n";
        echo "  - Status: {$member->currentMembershipPeriod->status}\n";
    }
    
    echo "Latest Payment: ";
    $latestPayment = $member->payments()->latest()->first();
    if($latestPayment) {
        echo "YES\n";
        echo "  - Amount: {$latestPayment->amount}\n";
        echo "  - Status: {$latestPayment->status}\n";
        echo "  - Expiration: " . ($latestPayment->membership_expiration_date ?? 'NULL') . "\n";
    } else {
        echo "NO\n";
    }
    
    echo "---\n";
}

echo "\n=== IDENTIFYING ISSUES ===\n\n";

$membersWithIssues = [];

foreach($members as $member) {
    $issues = [];
    
    // Check if member has no membership expiration date
    if (!$member->membership_expires_at) {
        $issues[] = "No membership_expires_at";
    }
    
    // Check if member has no current membership period
    if (!$member->currentMembershipPeriod) {
        $issues[] = "No current membership period";
    }
    
    // Check if member has no payments
    if ($member->payments()->count() == 0) {
        $issues[] = "No payments";
    }
    
    // Check if member has status 'active' but no valid membership
    if ($member->status === 'active' && !$member->membership_expires_at && !$member->currentMembershipPeriod) {
        $issues[] = "Active status but no membership data";
    }
    
    if (!empty($issues)) {
        $membersWithIssues[] = [
            'member' => $member,
            'issues' => $issues
        ];
    }
}

if (empty($membersWithIssues)) {
    echo "✅ No issues found!\n";
} else {
    echo "❌ Found issues with " . count($membersWithIssues) . " member(s):\n\n";
    
    foreach($membersWithIssues as $item) {
        $member = $item['member'];
        $issues = $item['issues'];
        
        echo "Member: {$member->full_name} (UID: {$member->uid})\n";
        echo "Issues:\n";
        foreach($issues as $issue) {
            echo "  - {$issue}\n";
        }
        echo "\n";
    }
}

echo "=== RECOMMENDATIONS ===\n\n";
echo "To fix the 'N/A' end date issue:\n";
echo "1. Members need proper membership periods with expiration dates\n";
echo "2. Members need completed payments with expiration dates\n";
echo "3. Update member.membership_expires_at field\n";
echo "4. Remove 'active' status display if not needed\n";
