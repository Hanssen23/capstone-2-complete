<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\UidPool;

echo "📊 UID Pool Status Report\n";
echo "========================\n\n";

try {
    $availableCount = UidPool::available()->count();
    $assignedCount = UidPool::assigned()->count();
    $totalCount = $availableCount + $assignedCount;

    echo "📈 Summary:\n";
    echo "  • Total UIDs: {$totalCount}\n";
    echo "  • Available UIDs: {$availableCount}\n";
    echo "  • Assigned UIDs: {$assignedCount}\n\n";

    if ($availableCount > 0) {
        echo "✅ Available UIDs:\n";
        foreach (UidPool::available()->orderBy('created_at')->get() as $uid) {
            echo "  • {$uid->uid}\n";
        }
        echo "\n";
    }

    if ($assignedCount > 0) {
        echo "🔒 Assigned UIDs:\n";
        foreach (UidPool::assigned()->orderBy('assigned_at', 'desc')->get() as $uid) {
            echo "  • {$uid->uid} (assigned: {$uid->assigned_at->format('M d, Y H:i')})\n";
        }
        echo "\n";
    }

    if ($availableCount === 0) {
        echo "⚠️  Warning: No UIDs available in the pool!\n";
        echo "   New member registrations will fail until UIDs are returned.\n\n";
    }

    echo "💡 Management:\n";
    echo "  • Access UID Pool Management: /uid-pool\n";
    echo "  • Refresh pool (return all UIDs): /uid-pool/refresh\n";
    echo "  • API status endpoint: /uid-pool/status\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
