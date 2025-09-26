<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking member statuses...\n";

$members = App\Models\Member::whereIn('uid', ['A69D194E', 'E6415F5F'])->get();

foreach ($members as $member) {
    echo "Member: {$member->first_name} {$member->last_name}\n";
    echo "UID: {$member->uid}\n";
    echo "Status: '{$member->status}'\n";
    echo "Status type: " . gettype($member->status) . "\n";
    echo "Status length: " . strlen($member->status) . "\n";
    echo "Is active check: " . ($member->status === 'active' ? 'TRUE' : 'FALSE') . "\n";
    echo "Not equal check: " . ($member->status !== 'active' ? 'TRUE' : 'FALSE') . "\n";
    echo "---\n";
}
