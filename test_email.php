<?php
// Comprehensive email delivery test script
require_once '/var/www/silencio-gym/vendor/autoload.php';

$app = require_once '/var/www/silencio-gym/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use App\Models\Member;

echo "=== COMPREHENSIVE EMAIL DELIVERY TEST ===\n\n";

try {
    // 1. Check current configuration
    echo "1. CHECKING EMAIL CONFIGURATION:\n";
    echo "   Mail Driver: " . Config::get('mail.default') . "\n";
    echo "   SMTP Host: " . Config::get('mail.mailers.smtp.host') . "\n";
    echo "   SMTP Port: " . Config::get('mail.mailers.smtp.port') . "\n";
    echo "   SMTP Username: " . Config::get('mail.mailers.smtp.username') . "\n";
    echo "   SMTP Encryption: " . Config::get('mail.mailers.smtp.encryption') . "\n";
    echo "   Queue Connection: " . Config::get('queue.default') . "\n";
    echo "   From Address: " . Config::get('mail.from.address') . "\n\n";

    // 2. Test basic SMTP connection
    echo "2. TESTING SMTP CONNECTION:\n";
    try {
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            Config::get('mail.mailers.smtp.host'),
            Config::get('mail.mailers.smtp.port'),
            Config::get('mail.mailers.smtp.encryption') === 'tls'
        );
        $transport->setUsername(Config::get('mail.mailers.smtp.username'));
        $transport->setPassword(Config::get('mail.mailers.smtp.password'));

        echo "   ✅ SMTP transport created successfully\n";
    } catch (Exception $e) {
        echo "   ❌ SMTP transport failed: " . $e->getMessage() . "\n";
    }

    // 3. Check queue status
    echo "\n3. CHECKING QUEUE STATUS:\n";
    $pendingJobs = \DB::table('jobs')->count();
    echo "   Pending jobs in queue: " . $pendingJobs . "\n";

    // 4. Test direct email sending (bypass queue)
    echo "\n4. TESTING DIRECT EMAIL SENDING:\n";
    try {
        Mail::raw('This is a direct test email from Silencio Gym password reset system.', function ($message) {
            $message->to('rbagym@rbagym.com')
                    ->subject('Direct Test Email - Silencio Gym')
                    ->from(Config::get('mail.from.address'), Config::get('mail.from.name'));
        });
        echo "   ✅ Direct email sent successfully!\n";
    } catch (Exception $e) {
        echo "   ❌ Direct email failed: " . $e->getMessage() . "\n";
    }

    // 5. Check if member exists
    echo "\n5. CHECKING MEMBER DATA:\n";
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
    }

    // 6. Test password reset with sync queue
    echo "\n6. TESTING PASSWORD RESET (SYNC QUEUE):\n";
    Config::set('queue.default', 'sync'); // Force synchronous sending

    $status = Password::broker('members')->sendResetLink(['email' => 'rbagym@rbagym.com']);

    if ($status == Password::RESET_LINK_SENT) {
        echo "   ✅ Password reset email queued successfully!\n";
    } else {
        echo "   ❌ Password reset failed: " . $status . "\n";
    }

    // 7. Check queue again
    echo "\n7. FINAL QUEUE CHECK:\n";
    $finalJobs = \DB::table('jobs')->count();
    echo "   Jobs added to queue: " . ($finalJobs - $pendingJobs) . "\n";

    if ($finalJobs > $pendingJobs) {
        echo "   ⚠️  Emails are being queued, not sent immediately!\n";
        echo "   Running queue worker to process emails...\n";

        // Process the queue
        \Artisan::call('queue:work', ['--once' => true, '--timeout' => 30]);
        echo "   ✅ Queue processed\n";
    }

} catch (Exception $e) {
    echo "❌ CRITICAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "Check your email inbox: rbagym@rbagym.com\n";
echo "If no emails received, check spam/junk folders.\n";
?>
