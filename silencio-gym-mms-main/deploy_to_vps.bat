@echo off
echo 🚀 Deploying Email Features to VPS Server...
echo.

REM VPS connection details
set VPS_HOST=156.67.221.184
set VPS_USER=root
set VPS_PATH=/var/www/silencio-gym

echo 📁 Uploading files to VPS...
echo.

REM Upload database migrations
echo Uploading database migrations...
scp "database/migrations/2025_01_15_000002_add_email_verification_to_members_table.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/database/migrations/
scp "database/migrations/2025_01_15_000003_create_member_password_reset_tokens_table.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/database/migrations/

REM Upload models
echo Uploading models...
scp "app/Models/Member.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Models/

REM Upload notifications
echo Uploading notifications...
scp "app/Notifications/MemberEmailVerification.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Notifications/
scp "app/Notifications/MemberPasswordReset.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Notifications/

REM Upload controllers
echo Uploading controllers...
scp "app/Http/Controllers/MemberEmailVerificationController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp "app/Http/Controllers/MemberPasswordResetController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp "app/Http/Controllers/MemberAuthController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
scp "app/Http/Controllers/AuthController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/

REM Upload configuration
echo Uploading configuration...
scp "config/auth.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/config/

REM Upload views
echo Uploading views...
scp "resources/views/auth/member-verify-email.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/auth/
scp "resources/views/auth/member-forgot-password.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/auth/
scp "resources/views/auth/member-reset-password.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/auth/
scp "resources/views/login.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/

REM Upload routes
echo Uploading routes...
scp "routes/web_server.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/routes/

echo.
echo 🔧 Running post-deployment commands on VPS...

REM Run commands on VPS
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php artisan migrate --force && php artisan config:clear && php artisan route:clear && php artisan view:clear && chown -R www-data:www-data %VPS_PATH% && chmod -R 755 %VPS_PATH% && chmod -R 775 %VPS_PATH%/storage && chmod -R 775 %VPS_PATH%/bootstrap/cache"

echo.
echo ✅ Deployment completed!
echo.
echo 📋 Next Steps:
echo 1. Update .env file on VPS with SMTP settings
echo 2. Test the forgot password feature at: http://156.67.221.184/member/forgot-password
echo.
pause
