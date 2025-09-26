<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Member table structure:\n";
echo "======================\n";

$columns = DB::select('PRAGMA table_info(members)');
foreach ($columns as $col) {
    echo $col->name . ' - ' . $col->type . "\n";
}

echo "\nCurrent members:\n";
echo "===============\n";

$members = App\Models\Member::all();
foreach ($members as $member) {
    echo "ID: {$member->id} | UID: {$member->uid} | Name: {$member->first_name} {$member->last_name}\n";
}
