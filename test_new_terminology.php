<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Member;
use Illuminate\Support\Facades\Password;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing New Password Setup/Reset Terminology ===" . PHP_EOL;

// Find a test member
$member = Member::where('email', 'rbagym@rbagym.com')->first();
if ($member) {
    echo "Testing password setup/reset email with new terminology..." . PHP_EOL;
    $status = Password::broker('members')->sendResetLink(['email' => $member->email]);
    echo "Status: " . $status . PHP_EOL;
    echo "Email sent successfully with updated terminology!" . PHP_EOL;
    echo "Check your email inbox for the updated message." . PHP_EOL;
} else {
    echo "Test member not found." . PHP_EOL;
}

echo "=== Test Complete ===" . PHP_EOL;
