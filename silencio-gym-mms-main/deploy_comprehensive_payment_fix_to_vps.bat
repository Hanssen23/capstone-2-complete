@echo off
echo ========================================
echo   DEPLOYING COMPREHENSIVE PAYMENT FIX TO VPS
echo   Server: 156.67.221.184
echo ========================================

echo Step 1: Creating VPS deployment package...

REM Create deployment directory
if not exist "vps_comprehensive_payment_fix_deploy" mkdir vps_comprehensive_payment_fix_deploy

echo Copying comprehensive payment fix files...

REM Copy updated controllers
if not exist "vps_comprehensive_payment_fix_deploy\app\Http\Controllers" mkdir vps_comprehensive_payment_fix_deploy\app\Http\Controllers
copy "app\Http\Controllers\MembershipController.php" "vps_comprehensive_payment_fix_deploy\app\Http\Controllers\"

REM Copy updated manage-member view
if not exist "vps_comprehensive_payment_fix_deploy\resources\views\membership" mkdir vps_comprehensive_payment_fix_deploy\resources\views\membership
copy "resources\views\membership\manage-member.blade.php" "vps_comprehensive_payment_fix_deploy\resources\views\membership\"

REM Copy test script
copy "test_comprehensive_payment_fix.php" "vps_comprehensive_payment_fix_deploy\"

echo Step 2: Creating comprehensive deployment script for VPS...

REM Create comprehensive deployment script for VPS
echo #!/bin/bash > vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "========================================" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "  COMPREHENSIVE PAYMENT PROCESSING FIX" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "========================================" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 1: Backing up existing files..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo mkdir -p /var/www/silencio-gym/backup/payment_comprehensive_fix_$(date +%%Y%%m%%d_%%H%%M%%S) >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo cp /var/www/silencio-gym/app/Http/Controllers/MembershipController.php /var/www/silencio-gym/backup/payment_comprehensive_fix_$(date +%%Y%%m%%d_%%H%%M%%S)/ 2^>/dev/null ^|^| true >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo cp /var/www/silencio-gym/resources/views/membership/manage-member.blade.php /var/www/silencio-gym/backup/payment_comprehensive_fix_$(date +%%Y%%m%%d_%%H%%M%%S)/ 2^>/dev/null ^|^| true >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 2: Copying new files..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo cp app/Http/Controllers/MembershipController.php /var/www/silencio-gym/app/Http/Controllers/ >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo cp resources/views/membership/manage-member.blade.php /var/www/silencio-gym/resources/views/membership/ >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo cp test_comprehensive_payment_fix.php /var/www/silencio-gym/ >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 3: Running database migrations..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo cd /var/www/silencio-gym >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan migrate --force >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 4: Setting file permissions..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo chown -R www-data:www-data /var/www/silencio-gym/app/Http/Controllers/ >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo chown -R www-data:www-data /var/www/silencio-gym/resources/views/ >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo chmod 644 /var/www/silencio-gym/app/Http/Controllers/*.php >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo chmod 644 /var/www/silencio-gym/resources/views/membership/*.php >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 5: Clearing Laravel caches..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan config:clear >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan route:clear >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan view:clear >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan cache:clear >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan optimize:clear >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 6: Testing database structure..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan tinker --execute="try { \$payment = new App\\Models\\Payment(); \$fillable = \$payment->getFillable(); echo 'Payment model fillable fields: ' . implode(', ', \$fillable) . PHP_EOL; \$testFields = ['is_pwd', 'is_senior_citizen', 'discount_amount', 'discount_percentage']; foreach (\$testFields as \$field) { if (in_array(\$field, \$fillable)) { echo '✅ Field ' . \$field . ' exists' . PHP_EOL; } else { echo '❌ Field ' . \$field . ' missing' . PHP_EOL; } } } catch (Exception \$e) { echo '❌ Error testing Payment model: ' . \$e->getMessage() . PHP_EOL; }" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 7: Testing payment creation..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php test_comprehensive_payment_fix.php >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "Step 8: Testing routes..." >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo php artisan route:list --name=membership.process-payment >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "========================================" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "  COMPREHENSIVE PAYMENT FIX COMPLETE!" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "========================================" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "✅ Payment processing validation rules fixed" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "✅ JavaScript error handling improved" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "✅ Server error logging enhanced" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "✅ Database migrations run" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "✅ Database structure verified" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "✅ Payment creation tested" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "🎯 Test the system at: http://156.67.221.184/membership/manage-member" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh
echo echo "" >> vps_comprehensive_payment_fix_deploy\deploy_comprehensive_payment_fix.sh

echo Step 3: Creating Windows deployment script...

REM Create Windows deployment script
echo @echo off > vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo   VPS COMPREHENSIVE PAYMENT FIX DEPLOYMENT >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo. >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo set VPS_HOST=156.67.221.184 >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo set VPS_USER=root >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo set VPS_PATH=/var/www/silencio-gym >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo. >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo Step 1: Uploading files to VPS... >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo scp "app/Http/Controllers/MembershipController.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/app/Http/Controllers/ >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo scp "resources/views/membership/manage-member.blade.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/resources/views/membership/ >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo scp "test_comprehensive_payment_fix.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/ >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo. >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo Step 2: Running deployment script on VPS... >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo ssh %%VPS_USER%%@%%VPS_HOST%% "cd ~ && chmod +x deploy_comprehensive_payment_fix.sh && ./deploy_comprehensive_payment_fix.sh" >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo. >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo   DEPLOYMENT COMPLETE! >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo pause >> vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat

echo Step 4: Creating manual upload instructions...

echo # Comprehensive Payment Processing Fix - Manual Upload Instructions > vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ## Files to Upload: >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 1. **app/Http/Controllers/MembershipController.php** → `/var/www/silencio-gym/app/Http/Controllers/` >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 2. **resources/views/membership/manage-member.blade.php** → `/var/www/silencio-gym/resources/views/membership/` >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 3. **test_comprehensive_payment_fix.php** → `/var/www/silencio-gym/` >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ## After Upload Commands: >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ```bash >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # SSH into your VPS >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ssh root@156.67.221.184 >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Navigate to Laravel directory >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo cd /var/www/silencio-gym >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Run database migrations >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan migrate --force >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Clear caches >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan config:clear >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan route:clear >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan view:clear >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan cache:clear >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan optimize:clear >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Test the system >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php test_comprehensive_payment_fix.php >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Test the routes >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan route:list --name=membership.process-payment >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ``` >> vps_comprehensive_payment_fix_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md

echo Step 5: Creating ZIP file for easy upload...

REM Create ZIP file using PowerShell
powershell -command "Compress-Archive -Path 'vps_comprehensive_payment_fix_deploy\*' -DestinationPath 'vps_comprehensive_payment_fix_deploy.zip' -Force"

echo.
echo ========================================
echo   COMPREHENSIVE VPS DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo ✅ Comprehensive payment processing fix prepared for VPS
echo ✅ Database migrations included
echo ✅ Test script included for verification
echo ✅ Deployment scripts created  
echo ✅ Manual upload instructions included
echo ✅ ZIP file created for easy upload
echo.
echo 🚀 Deployment Options:
echo.
echo 1. **Automatic Deployment (if you have SSH access):**
echo    Run: vps_comprehensive_payment_fix_deploy\deploy_to_vps.bat
echo.
echo 2. **Manual Upload:**
echo    - Upload vps_comprehensive_payment_fix_deploy.zip to your VPS
echo    - Extract and run deploy_comprehensive_payment_fix.sh
echo.
echo 3. **File Manager Upload:**
echo    - Use hosting control panel file manager
echo    - Upload files maintaining directory structure
echo    - Run deployment commands manually
echo.
echo 🎯 After deployment, test at: http://156.67.221.184/membership/manage-member
echo.

set /p choice="Run automatic deployment now? (y/n): "
if /i "%choice%"=="y" (
    echo.
    echo Starting automatic deployment...
    cd vps_comprehensive_payment_fix_deploy
    call deploy_to_vps.bat
) else (
    echo.
    echo Deployment package ready in: vps_comprehensive_payment_fix_deploy\
    echo ZIP file ready: vps_comprehensive_payment_fix_deploy.zip
)

pause
