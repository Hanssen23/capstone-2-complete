<?php
// Test password reset through web interface simulation
require_once '/var/www/silencio-gym/vendor/autoload.php';

$app = require_once '/var/www/silencio-gym/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Models\Member;

echo "=== TESTING PASSWORD RESET WEB FUNCTIONALITY ===\n\n";

try {
    // 1. Ensure test member exists
    echo "1. CHECKING TEST MEMBER:\n";
    $member = Member::where('email', 'rbagym@rbagym.com')->first();
    
    if (!$member) {
        echo "   Creating test member...\n";
        $member = Member::create([
            'uid' => 'TEST001',
            'member_number' => 'TEST001',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'rbagym@rbagym.com',
            'mobile_number' => '+639123456789',
            'password' => bcrypt('password123'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        echo "   ✅ Test member created\n";
    } else {
        echo "   ✅ Member exists: " . $member->first_name . " " . $member->last_name . "\n";
        echo "   Email: " . $member->email . "\n";
        echo "   Status: " . $member->status . "\n";
        echo "   Email verified: " . ($member->email_verified_at ? 'Yes' : 'No') . "\n";
    }
    
    // 2. Test password reset broker directly
    echo "\n2. TESTING PASSWORD RESET BROKER:\n";
    
    // Clear any existing tokens first
    \DB::table('member_password_reset_tokens')->where('email', 'rbagym@rbagym.com')->delete();
    echo "   Cleared existing reset tokens\n";
    
    // Send reset link
    $status = Password::broker('members')->sendResetLink(['email' => 'rbagym@rbagym.com']);
    
    echo "   Reset link status: " . $status . "\n";
    
    if ($status == Password::RESET_LINK_SENT) {
        echo "   ✅ Password reset link sent successfully!\n";
        
        // Check if token was created
        $token = \DB::table('member_password_reset_tokens')
                    ->where('email', 'rbagym@rbagym.com')
                    ->first();
        
        if ($token) {
            echo "   ✅ Reset token created in database\n";
            echo "   Token created at: " . $token->created_at . "\n";
        } else {
            echo "   ❌ No reset token found in database\n";
        }
        
    } else {
        echo "   ❌ Failed to send reset link: " . $status . "\n";
        
        // Check possible reasons
        if ($status == Password::INVALID_USER) {
            echo "   Reason: Invalid user (email not found)\n";
        } elseif ($status == Password::RESET_THROTTLED) {
            echo "   Reason: Reset throttled (too many attempts)\n";
        }
    }
    
    // 3. Test direct notification sending
    echo "\n3. TESTING DIRECT NOTIFICATION:\n";
    try {
        $token = app('auth.password.broker')->getRepository()->create($member);
        echo "   Generated token: " . substr($token, 0, 10) . "...\n";
        
        $member->sendPasswordResetNotification($token);
        echo "   ✅ Direct notification sent successfully!\n";
        
    } catch (Exception $e) {
        echo "   ❌ Direct notification failed: " . $e->getMessage() . "\n";
    }
    
    // 4. Check mail configuration one more time
    echo "\n4. FINAL CONFIGURATION CHECK:\n";
    echo "   Mail Driver: " . config('mail.default') . "\n";
    echo "   SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "   SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "   SMTP Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "   SMTP Encryption: " . config('mail.mailers.smtp.encryption') . "\n";
    echo "   Queue Driver: " . config('queue.default') . "\n";
    echo "   From Address: " . config('mail.from.address') . "\n";
    
    // 5. Test with a simple mail
    echo "\n5. SENDING SIMPLE TEST EMAIL:\n";
    try {
        Mail::raw('This is a final test email to verify SMTP delivery is working.', function ($message) {
            $message->to('rbagym@rbagym.com')
                    ->subject('Final SMTP Test - Silencio Gym')
                    ->from(config('mail.from.address'), config('mail.from.name'));
        });
        echo "   ✅ Simple test email sent!\n";
    } catch (Exception $e) {
        echo "   ❌ Simple test email failed: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "🔍 WHAT TO CHECK:\n";
echo "1. Check your email inbox: rbagym@rbagym.com\n";
echo "2. Check spam/junk folders\n";
echo "3. Check Hostinger email logs if available\n";
echo "4. Verify Hostinger email account is active and working\n";
echo "\n📧 You should have received 2-3 test emails if everything is working correctly.\n";
?>
