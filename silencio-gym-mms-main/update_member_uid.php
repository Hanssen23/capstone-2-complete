<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Member;

echo "=== Member UID Updater ===\n\n";

// Get command line arguments
$memberId = $argv[1] ?? null;
$newUid = $argv[2] ?? null;

if (!$memberId || !$newUid) {
    echo "Usage: php update_member_uid.php <member_id> <new_uid>\n";
    echo "Example: php update_member_uid.php 1 E6415F5F\n\n";
    
    echo "Available members:\n";
    foreach (Member::all(['id', 'uid', 'full_name']) as $member) {
        echo "ID: {$member->id}, UID: {$member->uid}, Name: {$member->full_name}\n";
    }
    exit(1);
}

// Find the member
$member = Member::find($memberId);

if (!$member) {
    echo "❌ Member with ID {$memberId} not found!\n";
    exit(1);
}

echo "Found member: {$member->full_name} (Current UID: {$member->uid})\n";
echo "Updating UID to: {$newUid}\n";

// Update the UID
$member->uid = $newUid;
$member->save();

echo "✅ UID updated successfully!\n";
echo "Member: {$member->full_name}\n";
echo "New UID: {$member->uid}\n";

echo "\n=== Update Complete ===\n";
