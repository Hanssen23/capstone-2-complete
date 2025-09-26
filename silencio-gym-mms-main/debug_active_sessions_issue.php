<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Checking Current Active Sessions Issue\n";
echo "========================================\n\n";

// Check current active sessions
echo "1. Current active sessions in database:\n";
$activeSessions = App\Models\ActiveSession::where('status', 'active')->with('member')->get();
echo "   📊 Total active sessions: " . $activeSessions->count() . "\n\n";

foreach ($activeSessions as $session) {
    $member = $session->member;
    echo "   👤 Member: {$member->first_name} {$member->last_name}\n";
    echo "      🆔 UID: {$member->uid}\n";
    echo "      📊 Status: {$member->status}\n";
    echo "      🆔 Session ID: {$session->id}\n";
    echo "      📊 Session Status: {$session->status}\n";
    echo "      🕐 Check-in time: {$session->check_in_time}\n";
    echo "      🕐 Check-out time: " . ($session->check_out_time ? $session->check_out_time : 'NULL') . "\n";
    echo "      ⏱️  Session duration: {$session->currentDuration}\n";
    echo "      📊 Is Active: " . ($session->is_active ? 'YES' : 'NO') . "\n";
    echo "\n";
}

// Check John Doe specifically
echo "2. John Doe details:\n";
$john = App\Models\Member::where('uid', 'A69D194E')->first();
if ($john) {
    echo "   👤 Name: {$john->first_name} {$john->last_name}\n";
    echo "   🆔 UID: {$john->uid}\n";
    echo "   📊 Status: {$john->status}\n";
    
    $activeSession = App\Models\ActiveSession::where('member_id', $john->id)
        ->where('status', 'active')
        ->first();
    
    if ($activeSession) {
        echo "   ⚠️  Has active session (ID: {$activeSession->id})\n";
        echo "   📊 Session status: {$activeSession->status}\n";
        echo "   🕐 Check-in: {$activeSession->check_in_time}\n";
        echo "   🕐 Check-out: " . ($activeSession->check_out_time ? $activeSession->check_out_time : 'NULL') . "\n";
    } else {
        echo "   ✅ No active session\n";
    }
}

// Check Hans Timothy Samson
echo "\n3. Hans Timothy Samson details:\n";
$hans = App\Models\Member::where('uid', '1234567890')->first();
if ($hans) {
    echo "   👤 Name: {$hans->first_name} {$hans->last_name}\n";
    echo "   🆔 UID: {$hans->uid}\n";
    echo "   📊 Status: {$hans->status}\n";
    
    $activeSession = App\Models\ActiveSession::where('member_id', $hans->id)
        ->where('status', 'active')
        ->first();
    
    if ($activeSession) {
        echo "   ⚠️  Has active session (ID: {$activeSession->id})\n";
        echo "   📊 Session status: {$activeSession->status}\n";
        echo "   🕐 Check-in: {$activeSession->check_in_time}\n";
        echo "   🕐 Check-out: " . ($activeSession->check_out_time ? $activeSession->check_out_time : 'NULL') . "\n";
    } else {
        echo "   ✅ No active session\n";
    }
}

// Check recent RFID logs
echo "\n4. Recent RFID logs (last 10):\n";
$recentLogs = App\Models\RfidLog::orderBy('timestamp', 'desc')->limit(10)->get();
foreach ($recentLogs as $log) {
    $statusIcon = $log->status === 'success' ? '✅' : '❌';
    echo "   {$statusIcon} {$log->action} - {$log->message}\n";
    echo "      Time: {$log->timestamp} | Card: {$log->card_uid} | Status: {$log->status}\n";
}

echo "\n🎯 Investigation Complete!\n";
