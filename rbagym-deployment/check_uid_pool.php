<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UID Pool Status ===\n";
$uids = DB::table('uid_pool')->orderBy('id')->get();
foreach($uids as $uid) {
    echo $uid->uid . " - " . $uid->status . " - " . ($uid->assigned_at ?? 'null') . "\n";
}

echo "\n=== Members with UID A69D194E ===\n";
$members = DB::table('members')->where('uid', 'A69D194E')->get(['id', 'first_name', 'last_name', 'email', 'created_at']);
foreach($members as $member) {
    echo $member->id . " - " . $member->first_name . " " . $member->last_name . " - " . $member->email . " - " . $member->created_at . "\n";
}

echo "\n=== Available UIDs Count ===\n";
$availableCount = DB::table('uid_pool')->where('status', 'available')->count();
echo "Available UIDs: " . $availableCount . "\n";

$assignedCount = DB::table('uid_pool')->where('status', 'assigned')->count();
echo "Assigned UIDs: " . $assignedCount . "\n";
