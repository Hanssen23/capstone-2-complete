@echo off
echo 🚀 Deploying Forced Logout Fix to VPS Server...
echo.

REM VPS connection details
set VPS_HOST=156.67.221.184
set VPS_USER=root
set VPS_PATH=/var/www/silencio-gym

echo 📁 Uploading fixed routes file to VPS...
echo.

REM Upload routes file with middleware fixes
echo Uploading routes/web.php with route restructuring...
scp "routes/web.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/routes/

echo.
echo 🔧 Running post-deployment commands on VPS...

REM Run commands on VPS to clear caches
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php artisan route:clear && php artisan config:clear && php artisan cache:clear && php artisan view:clear && chown -R www-data:www-data %VPS_PATH% && chmod -R 755 %VPS_PATH% && chmod -R 775 %VPS_PATH%/storage && chmod -R 775 %VPS_PATH%/bootstrap/cache"

echo.
echo ✅ Deployment completed!
echo.
echo 📋 Changes Applied:
echo 1. Moved shared routes (members, membership plans, accounts, payments) outside admin-only middleware
echo 2. Both admin and employee can now access shared routes without forced logout
echo 3. Admin-only routes (RFID, auto-deletion) remain protected
echo 4. Employee-only routes remain protected
echo.
echo 🧪 CRITICAL TESTS:
echo 1. Admin login ^> navigate to /membership/plans ^> should NOT logout
echo 2. Employee login ^> navigate to /membership/plans ^> should NOT logout
echo 3. Employee login ^> navigate to /accounts ^> should NOT logout
echo 4. Admin cannot access /employee/dashboard (redirected)
echo 5. Employee cannot access /rfid-monitor (redirected)
echo.
pause

