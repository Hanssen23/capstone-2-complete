<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing Hans Timothy Samson...\n";

$hans = App\Models\Member::where('first_name', 'Hans')->first();
if ($hans) {
    echo "Found Hans: {$hans->first_name} {$hans->last_name} - UID: {$hans->uid}\n";
    $hans->update(['is_active' => true, 'status' => 'active']);
    echo "Hans activated!\n";
} else {
    echo "Hans not found, creating test member...\n";
    App\Models\Member::create([
        'uid' => 'A69D194E',
        'member_number' => 'M002',
        'membership' => 'basic',
        'full_name' => 'Hans Timothy Samson',
        'mobile_number' => '1234567890',
        'email' => 'hans@example.com',
        'first_name' => 'Hans',
        'last_name' => 'Samson',
        'is_active' => true,
        'status' => 'active'
    ]);
    echo "Hans created and activated!\n";
}

echo "Done!\n";
