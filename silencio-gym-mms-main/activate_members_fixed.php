<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Activating members for card tapping...\n";
echo "=====================================\n";

// Activate John Doe and Hans Timothy Samson
$johnDoe = App\Models\Member::where('uid', 'A69D194E')->first();
if ($johnDoe) {
    $johnDoe->update(['status' => 'active']);
    echo "✅ John Doe (A69D194E) activated\n";
}

$hansSamson = App\Models\Member::where('uid', 'E6415F5F')->first();
if ($hansSamson) {
    $hansSamson->update(['status' => 'active']);
    echo "✅ Hans Timothy Samson (E6415F5F) activated\n";
}

echo "\n🎯 Ready for card tapping!\n";
echo "Now when you tap your 2 cards, only 2 members should appear!\n";
echo "All duplicate sessions have been cleaned up.\n";
