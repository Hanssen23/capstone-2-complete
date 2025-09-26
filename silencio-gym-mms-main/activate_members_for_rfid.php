<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Activating members for RFID testing...\n";

$john = App\Models\Member::where('uid', 'A69D194E')->first();
if ($john) {
    echo "John Doe status: {$john->status}\n";
    $john->update(['status' => 'active']);
    echo "John Doe activated!\n";
}

$hans = App\Models\Member::where('uid', 'E6415F5F')->first();
if ($hans) {
    echo "Hans status: {$hans->status}\n";
    $hans->update(['status' => 'active']);
    echo "Hans activated!\n";
}

echo "\nNow test the RFID card tapping!\n";
