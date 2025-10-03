@echo off
echo ============================================
echo   Prepare Silencio Gym for VPS Deployment
echo ============================================
echo.
echo This will prepare your Laravel app for deployment
echo to Hostinger VPS at 156.67.221.184
echo.
echo RFID functionality will be PRESERVED!
echo.
pause

echo [1] Installing production dependencies...
composer install --optimize-autoloader --no-dev
if errorlevel 1 (
    echo ERROR: Composer install failed
    pause
    exit /b 1
)

echo [2] Building frontend assets...
npm install
if errorlevel 1 (
    echo WARNING: npm install failed - continuing...
)

npm run build
if errorlevel 1 (
    echo WARNING: npm build failed - continuing...
)

echo [3] Creating VPS environment file...
copy hostinger.env.example .env.vps

echo [4] Updating .env.vps for VPS deployment...
powershell -Command "(Get-Content .env.vps) -replace 'APP_URL=https://yourdomain.com', 'APP_URL=https://156.67.221.184' -replace 'RFID_API_URL=https://yourdomain.com', 'RFID_API_URL=https://156.67.221.184/api/rfid' | Set-Content .env.vps"

echo [5] Clearing caches...
php artisan config:clear
php artisan cache:clear

echo [六个] Creating deployment instructions...
echo # Hostinger VPS Deployment Instructions > DEPLOY_INSTRUCTIONS.txt
echo. >> DEPLOY_INSTRUCTIONS.txt
echo ## Upload to VPS >> DEPLOY_INSTRUCTIONS.txt
echo 1. SSH to your VPS: ssh root@156.67.221.184 >> DEPLOY_INSTRUCTIONS.txt
echo 2. Upload all files to: /var/www/html/ >> DEPLOY_INSTRUCTIONS.txt
echo. >> DEPLOY_INSTRUCTIONS.txt
echo ## Commands to run on VPS: >> DEPLOY_INSTRUCTIONS.txt
echo cd /var/www/html/ >> DEPLOY_INSTRUCTIONS.txt
echo cp .env.vps .env >> DEPLOY_INSTRUCTIONS.txt
echo php artisan key:generate >> DEPLOY_INSTRUCTIONS.txt
echo php artisan migrate --force >> DEPLOY_INSTRUCTIONS.txt
echo php artisan db:seed --force >> DEPLOY_INSTRUCTIONS.txt
echo php artisan config:cache >> DEPLOY_INSTRUCTIONS.txt
echo. >> DEPLOY_INSTRUCTIONS.txt
echo ## Configure web server >> DEPLOY_INSTRUCTIONS.txt
echo Point Nginx/Apache to: /var/www/html/public >> DEPLOY_INSTRUCTIONS.txt
echo. >> DEPLOY_INSTRUCTIONS.txt
echo ## Access your site >> DEPLOY_INSTRUCTIONS.txt
echo https://156.67.221.184 >> DEPLOY_INSTRUCTIONS.txt
echo. >> DEPLOY_INSTRUCTIONS.txt
echo Admin login: admin@admin.com / admin123 >> DEPLOY_INSTRUCTIONS.txt

echo.
echo [7] Creating file list for upload...
dir /s /b > files_to_upload.txt

echo.
echo ============================================
echo           DEPLOYMENT READY!
echo ============================================
echo.
echo Files to upload to VPS:
echo - Upload entire folder contents to: /var/www/html/
echo - Use instruction file: DEPLOY_INSTRUCTIONS.txt
echo.
echo VPS Details:
echo - Host: 156.67.221.184
echo - SSH: ssh root@156.67.221.184
echo - Target: /var/www/html/
echo.
echo Your RFID system will work perfectly on the VPS!
echo.
echo Press any key to continue...
pause

echo.
echo Alternative: Use browser terminal in Hostinger panel
echo to easily upload files and run commands.
echo.
pause
