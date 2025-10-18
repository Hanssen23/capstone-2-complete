# Deploy Email Verification and Password Reset Features to VPS
# Server: 156.67.221.184
# Path: /var/www/silencio-gym/

Write-Host "🚀 Deploying Email Verification and Password Reset Features to VPS..." -ForegroundColor Green

# VPS connection details
$VPS_HOST = "156.67.221.184"
$VPS_USER = "root"
$VPS_PATH = "/var/www/silencio-gym"

# Files to upload
$FILES_TO_UPLOAD = @(
    "database/migrations/2025_01_15_000002_add_email_verification_to_members_table.php",
    "database/migrations/2025_01_15_000003_create_member_password_reset_tokens_table.php",
    "app/Models/Member.php",
    "app/Notifications/MemberEmailVerification.php",
    "app/Notifications/MemberPasswordReset.php",
    "app/Http/Controllers/MemberEmailVerificationController.php",
    "app/Http/Controllers/MemberPasswordResetController.php",
    "app/Http/Controllers/MemberAuthController.php",
    "app/Http/Controllers/AuthController.php",
    "config/auth.php",
    "resources/views/auth/member-verify-email.blade.php",
    "resources/views/auth/member-forgot-password.blade.php",
    "resources/views/auth/member-reset-password.blade.php",
    "resources/views/login.blade.php",
    "routes/web_server.php"
)

Write-Host "📁 Uploading files to VPS..." -ForegroundColor Yellow

# Upload each file using SCP
foreach ($file in $FILES_TO_UPLOAD) {
    Write-Host "  📤 Uploading $file..." -ForegroundColor Cyan
    
    # Create directory structure on VPS if it doesn't exist
    $dir_path = Split-Path $file -Parent
    $dir_path = $dir_path -replace '\\', '/'
    
    # Create directory on VPS
    ssh "$VPS_USER@$VPS_HOST" "mkdir -p $VPS_PATH/$dir_path"
    
    # Upload the file
    scp $file "$VPS_USER@$VPS_HOST`:$VPS_PATH/$file"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "    ✅ Successfully uploaded $file" -ForegroundColor Green
    } else {
        Write-Host "    ❌ Failed to upload $file" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "🔧 Running post-deployment commands on VPS..." -ForegroundColor Yellow

# Run commands on VPS
$commands = @"
cd /var/www/silencio-gym

echo "📦 Installing/updating dependencies..."
composer install --no-dev --optimize-autoloader

echo "🗃️ Running database migrations..."
php artisan migrate --force

echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "🔧 Setting proper permissions..."
chown -R www-data:www-data /var/www/silencio-gym
chmod -R 755 /var/www/silencio-gym
chmod -R 775 /var/www/silencio-gym/storage
chmod -R 775 /var/www/silencio-gym/bootstrap/cache

echo "✅ Deployment completed successfully!"
"@

ssh "$VPS_USER@$VPS_HOST" $commands

Write-Host ""
Write-Host "🎉 Email Verification and Password Reset Features Deployed!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Update .env file on VPS with SMTP settings:"
Write-Host "   MAIL_MAILER=smtp"
Write-Host "   MAIL_HOST=smtp.hostinger.com"
Write-Host "   MAIL_PORT=587"
Write-Host "   MAIL_USERNAME=noreply@rbagym.com"
Write-Host "   MAIL_PASSWORD=your_actual_password"
Write-Host "   MAIL_ENCRYPTION=tls"
Write-Host "   MAIL_FROM_ADDRESS=noreply@rbagym.com"
Write-Host "   MAIL_FROM_NAME=`"Silencio Gym`""
Write-Host ""
Write-Host "2. Test the features:"
Write-Host "   - Registration: http://156.67.221.184/register"
Write-Host "   - Login: http://156.67.221.184/login"
Write-Host "   - Forgot Password: http://156.67.221.184/member/forgot-password"
Write-Host ""
Write-Host "3. Features implemented:"
Write-Host "   ✅ Email verification for new registrations"
Write-Host "   ✅ Password reset functionality"
Write-Host "   ✅ Secure token-based system"
Write-Host "   ✅ Professional email templates"
Write-Host "   ✅ User-friendly interfaces"
Write-Host ""
Write-Host "🔗 Access your system: http://156.67.221.184/" -ForegroundColor Cyan
