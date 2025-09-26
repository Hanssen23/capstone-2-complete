<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Activating members...\n";

App\Models\Member::where('uid', 'A69D194E')->update(['is_active' => true, 'status' => 'active']);
App\Models\Member::where('uid', 'E6415F5F')->update(['is_active' => true, 'status' => 'active']);

echo "Members activated!\n";
echo "Now you can tap your 2 cards and only 2 members should appear!\n";
