<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking member details...\n";

$members = App\Models\Member::whereIn('uid', ['A69D194E', 'E6415F5F'])->get();

foreach ($members as $member) {
    echo "Member: {$member->first_name} {$member->last_name}\n";
    echo "UID: {$member->uid}\n";
    echo "Status: '{$member->status}'\n";
    echo "Membership expires at: " . ($member->membership_expires_at ?? 'NULL') . "\n";
    
    if ($member->membership_expires_at) {
        $isExpired = $member->membership_expires_at < now();
        echo "Is expired: " . ($isExpired ? 'YES' : 'NO') . "\n";
        echo "Current time: " . now() . "\n";
    }
    
    echo "---\n";
}
