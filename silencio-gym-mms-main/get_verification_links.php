<?php
/**
 * Extract Email Verification Links from Laravel Log
 * 
 * This script extracts verification links from the laravel.log file
 * when MAIL_MAILER=log is configured.
 * 
 * Usage: php get_verification_links.php
 */

$logFile = __DIR__ . '/storage/logs/laravel.log';

if (!file_exists($logFile)) {
    echo "❌ Log file not found: $logFile\n";
    exit(1);
}

echo "📧 Email Verification Link Extractor\n";
echo "=====================================\n\n";

// Read the log file
$logContent = file_get_contents($logFile);

// Extract verification emails
preg_match_all('/To: ([^\n]+)\nSubject: Verify Your Email Address.*?Verify Email Address: (http[^\s]+)/s', $logContent, $matches, PREG_SET_ORDER);

if (empty($matches)) {
    echo "❌ No verification emails found in the log.\n";
    echo "\nℹ️  This could mean:\n";
    echo "   1. No member has registered yet\n";
    echo "   2. The log file has been cleared\n";
    echo "   3. Email verification is not being triggered\n\n";
    exit(0);
}

echo "✅ Found " . count($matches) . " verification email(s):\n\n";

foreach ($matches as $index => $match) {
    $email = trim($match[1]);
    $verificationUrl = trim($match[2]);
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📨 Email #" . ($index + 1) . "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "To: $email\n";
    echo "Verification Link:\n";
    echo "$verificationUrl\n\n";
    
    // Check if link has expired
    if (preg_match('/expires=(\d+)/', $verificationUrl, $expiryMatch)) {
        $expiryTimestamp = (int)$expiryMatch[1];
        $currentTimestamp = time();
        
        if ($currentTimestamp > $expiryTimestamp) {
            $expiredAgo = $currentTimestamp - $expiryTimestamp;
            $hours = floor($expiredAgo / 3600);
            $minutes = floor(($expiredAgo % 3600) / 60);
            echo "⚠️  Status: EXPIRED ($hours hours, $minutes minutes ago)\n";
        } else {
            $expiresIn = $expiryTimestamp - $currentTimestamp;
            $hours = floor($expiresIn / 3600);
            $minutes = floor(($expiresIn % 3600) / 60);
            echo "✅ Status: VALID (expires in $hours hours, $minutes minutes)\n";
        }
    }
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📋 How to Use:\n";
echo "   1. Copy the verification link above\n";
echo "   2. Paste it into your browser\n";
echo "   3. The member's email will be verified\n\n";

echo "⚠️  Note: Links expire after 60 minutes\n";
echo "   If a link is expired, the member needs to request a new one\n\n";

echo "💡 Tip: To send real emails instead of logging them:\n";
echo "   1. Update .env file: MAIL_MAILER=smtp\n";
echo "   2. Configure SMTP settings (see EMAIL_CONFIGURATION_GUIDE.md)\n";
echo "   3. Run: php artisan config:clear\n\n";

