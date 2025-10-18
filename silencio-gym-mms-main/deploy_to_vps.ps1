Write-Host "🚀 Deploying Email Features to VPS Server..." -ForegroundColor Green
Write-Host ""

# VPS connection details
$VPS_HOST = "156.67.221.184"
$VPS_USER = "root"
$VPS_PATH = "/var/www/silencio-gym"

Write-Host "📁 Uploading files to VPS..." -ForegroundColor Yellow
Write-Host ""

# Function to upload file with error handling
function Upload-File {
    param($LocalPath, $RemotePath)
    try {
        Write-Host "Uploading: $LocalPath" -ForegroundColor Cyan
        scp $LocalPath "${VPS_USER}@${VPS_HOST}:${RemotePath}"
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Success" -ForegroundColor Green
        } else {
            Write-Host "❌ Failed" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ Error uploading $LocalPath : $_" -ForegroundColor Red
    }
}

# Upload database migrations
Write-Host "📊 Uploading database migrations..." -ForegroundColor Yellow
Upload-File "database/migrations/2025_01_15_000002_add_email_verification_to_members_table.php" "$VPS_PATH/database/migrations/"
Upload-File "database/migrations/2025_01_15_000003_create_member_password_reset_tokens_table.php" "$VPS_PATH/database/migrations/"

# Upload models
Write-Host "🏗️ Uploading models..." -ForegroundColor Yellow
Upload-File "app/Models/Member.php" "$VPS_PATH/app/Models/"

# Upload notifications
Write-Host "📧 Uploading notifications..." -ForegroundColor Yellow
Upload-File "app/Notifications/MemberEmailVerification.php" "$VPS_PATH/app/Notifications/"
Upload-File "app/Notifications/MemberPasswordReset.php" "$VPS_PATH/app/Notifications/"

# Upload controllers
Write-Host "🎮 Uploading controllers..." -ForegroundColor Yellow
Upload-File "app/Http/Controllers/MemberEmailVerificationController.php" "$VPS_PATH/app/Http/Controllers/"
Upload-File "app/Http/Controllers/MemberPasswordResetController.php" "$VPS_PATH/app/Http/Controllers/"
Upload-File "app/Http/Controllers/MemberAuthController.php" "$VPS_PATH/app/Http/Controllers/"
Upload-File "app/Http/Controllers/AuthController.php" "$VPS_PATH/app/Http/Controllers/"

# Upload configuration
Write-Host "⚙️ Uploading configuration..." -ForegroundColor Yellow
Upload-File "config/auth.php" "$VPS_PATH/config/"

# Upload views
Write-Host "🎨 Uploading views..." -ForegroundColor Yellow
Upload-File "resources/views/auth/member-verify-email.blade.php" "$VPS_PATH/resources/views/auth/"
Upload-File "resources/views/auth/member-forgot-password.blade.php" "$VPS_PATH/resources/views/auth/"
Upload-File "resources/views/auth/member-reset-password.blade.php" "$VPS_PATH/resources/views/auth/"
Upload-File "resources/views/login.blade.php" "$VPS_PATH/resources/views/"

# Upload routes
Write-Host "🛣️ Uploading routes..." -ForegroundColor Yellow
Upload-File "routes/web_server.php" "$VPS_PATH/routes/"

Write-Host ""
Write-Host "🔧 Running post-deployment commands on VPS..." -ForegroundColor Yellow

# Run commands on VPS
try {
    ssh "${VPS_USER}@${VPS_HOST}" "cd $VPS_PATH && php artisan migrate --force && php artisan config:clear && php artisan route:clear && php artisan view:clear && chown -R www-data:www-data $VPS_PATH && chmod -R 755 $VPS_PATH && chmod -R 775 $VPS_PATH/storage && chmod -R 775 $VPS_PATH/bootstrap/cache"
    Write-Host "✅ Post-deployment commands completed successfully!" -ForegroundColor Green
} catch {
    Write-Host "❌ Error running post-deployment commands: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "✅ Deployment completed!" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Next Steps:" -ForegroundColor Cyan
Write-Host "1. Update .env file on VPS with SMTP settings" -ForegroundColor White
Write-Host "2. Test the forgot password feature at: http://156.67.221.184/member/forgot-password" -ForegroundColor White
Write-Host ""
Read-Host "Press Enter to continue"
