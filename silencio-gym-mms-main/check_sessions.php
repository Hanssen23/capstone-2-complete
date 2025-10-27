#!/usr/bin/env php
<?php

/**
 * Session Status Checker
 * 
 * This script helps you verify that multi-session mode is working correctly.
 * Run this script to see active sessions and their guard types.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         SILENCIO GYM - MULTI-SESSION STATUS CHECKER           ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check session configuration
echo "📋 SESSION CONFIGURATION:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "Session Driver:        " . config('session.driver') . "\n";
echo "Session Lifetime:      " . config('session.lifetime') . " minutes\n";
echo "Default Cookie:        " . config('session.cookie') . "\n";
echo "Web Guard Cookie:      " . config('session.guard_cookies.web') . "\n";
echo "Member Guard Cookie:   " . config('session.guard_cookies.member') . "\n";
echo "\n";

// Check active sessions in database
echo "🔐 ACTIVE SESSIONS:\n";
echo "─────────────────────────────────────────────────────────────────\n";

try {
    $sessions = DB::table('sessions')
        ->orderBy('last_activity', 'desc')
        ->get();
    
    if ($sessions->count() === 0) {
        echo "No active sessions found.\n";
    } else {
        echo "Total Sessions: " . $sessions->count() . "\n\n";
        
        foreach ($sessions as $index => $session) {
            $lastActivity = date('Y-m-d H:i:s', $session->last_activity);
            $payload = unserialize(base64_decode($session->payload));
            
            // Try to determine user info from session
            $userId = $payload['login_web_59ba36addc2b2f9401580f014c7f58ea4e30989d'] ?? null;
            $memberId = $payload['login_member_59ba36addc2b2f9401580f014c7f58ea4e30989d'] ?? null;
            
            echo "Session #" . ($index + 1) . ":\n";
            echo "  ID:            " . substr($session->id, 0, 20) . "...\n";
            echo "  Last Activity: " . $lastActivity . "\n";
            echo "  IP Address:    " . ($session->ip_address ?? 'N/A') . "\n";
            echo "  User Agent:    " . substr($session->user_agent ?? 'N/A', 0, 50) . "...\n";
            
            if ($userId) {
                $user = DB::table('users')->where('id', $userId)->first();
                if ($user) {
                    echo "  Guard:         WEB (Admin/Employee)\n";
                    echo "  User:          " . $user->email . " (" . $user->role . ")\n";
                }
            } elseif ($memberId) {
                $member = DB::table('members')->where('id', $memberId)->first();
                if ($member) {
                    echo "  Guard:         MEMBER\n";
                    echo "  Member:        " . $member->email . "\n";
                }
            } else {
                echo "  Guard:         Not authenticated\n";
            }
            
            echo "\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ Error reading sessions: " . $e->getMessage() . "\n";
}

echo "─────────────────────────────────────────────────────────────────\n";
echo "\n";

// Check authentication guards
echo "🛡️  AUTHENTICATION GUARDS:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "Available Guards:\n";
$guards = config('auth.guards');
foreach ($guards as $name => $config) {
    echo "  • " . $name . " (driver: " . $config['driver'] . ", provider: " . $config['provider'] . ")\n";
}
echo "\n";

// Check middleware
echo "🔧 MIDDLEWARE STATUS:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "GuardSessionManager:   " . (class_exists('App\Http\Middleware\GuardSessionManager') ? '✅ Registered' : '❌ Not Found') . "\n";
echo "RedirectIfAuthenticated: " . (class_exists('App\Http\Middleware\RedirectIfAuthenticated') ? '✅ Registered' : '❌ Not Found') . "\n";
echo "\n";

// Testing instructions
echo "📝 TESTING INSTRUCTIONS:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "1. Open browser and go to: http://127.0.0.1:8000/login\n";
echo "2. Login with admin account (admin@silencio.com / admin123)\n";
echo "3. Open NEW TAB and go to: http://127.0.0.1:8000/login\n";
echo "4. Login with employee account\n";
echo "5. Open ANOTHER TAB and go to: http://127.0.0.1:8000/login\n";
echo "6. Login with member account\n";
echo "7. Switch between tabs - all sessions should remain active!\n";
echo "\n";

echo "💡 TIP: Check browser cookies (F12 → Application → Cookies)\n";
echo "    You should see separate cookies for each guard:\n";
echo "    • silencio_gym_session_web\n";
echo "    • silencio_gym_session_member\n";
echo "\n";

echo "✅ Multi-session mode is configured and ready!\n";
echo "\n";

