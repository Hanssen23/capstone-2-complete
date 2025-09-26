<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing member statuses...\n";

// Update John Doe
$john = App\Models\Member::where('uid', 'A69D194E')->first();
if ($john) {
    echo "Updating John Doe from '{$john->status}' to 'active'...\n";
    $john->status = 'active';
    $john->save();
    echo "John Doe updated!\n";
}

// Update Hans
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
if ($hans) {
    echo "Updating Hans from '{$hans->status}' to 'active'...\n";
    $hans->status = 'active';
    $hans->save();
    echo "Hans updated!\n";
}

// Verify the updates
echo "\nVerifying updates...\n";
$john = App\Models\Member::where('uid', 'A69D194E')->first();
$hans = App\Models\Member::where('uid', 'E6415F5F')->first();

echo "John Doe status: '{$john->status}'\n";
echo "Hans status: '{$hans->status}'\n";

echo "\nMembers are now ready for RFID testing!\n";
