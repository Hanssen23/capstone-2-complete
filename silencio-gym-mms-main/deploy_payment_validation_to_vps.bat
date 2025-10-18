@echo off
echo ========================================
echo   DEPLOYING PAYMENT VALIDATION TO VPS
echo   Server: 156.67.221.184
echo ========================================

echo Step 1: Creating VPS deployment package...

REM Create deployment directory
if not exist "vps_payment_validation_deploy" mkdir vps_payment_validation_deploy

echo Copying payment validation files...

REM Copy updated controllers
if not exist "vps_payment_validation_deploy\app\Http\Controllers" mkdir vps_payment_validation_deploy\app\Http\Controllers
copy "app\Http\Controllers\MembershipController.php" "vps_payment_validation_deploy\app\Http\Controllers\"
copy "app\Http\Controllers\EmployeeController.php" "vps_payment_validation_deploy\app\Http\Controllers\"

REM Copy updated routes
copy "routes\web.php" "vps_payment_validation_deploy\"

REM Copy new modal component
if not exist "vps_payment_validation_deploy\resources\views\components" mkdir vps_payment_validation_deploy\resources\views\components
copy "resources\views\components\payment-validation-modals.blade.php" "vps_payment_validation_deploy\resources\views\components\"

REM Copy updated manage-member view
if not exist "vps_payment_validation_deploy\resources\views\membership" mkdir vps_payment_validation_deploy\resources\views\membership
copy "resources\views\membership\manage-member.blade.php" "vps_payment_validation_deploy\resources\views\membership\"

echo Step 2: Creating deployment script for VPS...

REM Create deployment script for VPS
echo #!/bin/bash > vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "========================================" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "  PAYMENT VALIDATION DEPLOYMENT" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "========================================" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "Step 1: Backing up existing files..." >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo mkdir -p /var/www/silencio-gym/backup/payment_validation_$(date +%%Y%%m%%d_%%H%%M%%S) >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp /var/www/silencio-gym/app/Http/Controllers/MembershipController.php /var/www/silencio-gym/backup/payment_validation_$(date +%%Y%%m%%d_%%H%%M%%S)/ 2^>/dev/null ^|^| true >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp /var/www/silencio-gym/app/Http/Controllers/EmployeeController.php /var/www/silencio-gym/backup/payment_validation_$(date +%%Y%%m%%d_%%H%%M%%S)/ 2^>/dev/null ^|^| true >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp /var/www/silencio-gym/routes/web.php /var/www/silencio-gym/backup/payment_validation_$(date +%%Y%%m%%d_%%H%%M%%S)/ 2^>/dev/null ^|^| true >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "Step 2: Copying new files..." >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp app/Http/Controllers/MembershipController.php /var/www/silencio-gym/app/Http/Controllers/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp app/Http/Controllers/EmployeeController.php /var/www/silencio-gym/app/Http/Controllers/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp web.php /var/www/silencio-gym/routes/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo mkdir -p /var/www/silencio-gym/resources/views/components >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp resources/views/components/payment-validation-modals.blade.php /var/www/silencio-gym/resources/views/components/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cp resources/views/membership/manage-member.blade.php /var/www/silencio-gym/resources/views/membership/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "Step 3: Setting file permissions..." >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chown -R www-data:www-data /var/www/silencio-gym/app/Http/Controllers/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chown -R www-data:www-data /var/www/silencio-gym/routes/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chown -R www-data:www-data /var/www/silencio-gym/resources/views/ >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chmod 644 /var/www/silencio-gym/app/Http/Controllers/*.php >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chmod 644 /var/www/silencio-gym/routes/web.php >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chmod 644 /var/www/silencio-gym/resources/views/components/*.php >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo chmod 644 /var/www/silencio-gym/resources/views/membership/*.php >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "Step 4: Clearing Laravel caches..." >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo cd /var/www/silencio-gym >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo php artisan config:clear >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo php artisan route:clear >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo php artisan view:clear >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo php artisan cache:clear >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "Step 5: Testing routes..." >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo php artisan route:list --name=membership.check-active-membership >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "========================================" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "  PAYMENT VALIDATION DEPLOYMENT COMPLETE!" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "========================================" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "✅ Payment validation system deployed successfully" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "✅ Employee blocking system active" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "✅ Admin override system with countdown active" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "✅ Audit logging enabled" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "🎯 Test the system at: http://156.67.221.184/membership/manage-member" >> vps_payment_validation_deploy\deploy_payment_validation.sh
echo echo "" >> vps_payment_validation_deploy\deploy_payment_validation.sh

echo Step 3: Creating Windows deployment script...

REM Create Windows deployment script
echo @echo off > vps_payment_validation_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo   VPS PAYMENT VALIDATION DEPLOYMENT >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo. >> vps_payment_validation_deploy\deploy_to_vps.bat
echo set VPS_HOST=156.67.221.184 >> vps_payment_validation_deploy\deploy_to_vps.bat
echo set VPS_USER=root >> vps_payment_validation_deploy\deploy_to_vps.bat
echo set VPS_PATH=/var/www/silencio-gym >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo. >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo Step 1: Uploading files to VPS... >> vps_payment_validation_deploy\deploy_to_vps.bat
echo scp "app/Http/Controllers/MembershipController.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/app/Http/Controllers/ >> vps_payment_validation_deploy\deploy_to_vps.bat
echo scp "app/Http/Controllers/EmployeeController.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/app/Http/Controllers/ >> vps_payment_validation_deploy\deploy_to_vps.bat
echo scp "web.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/routes/ >> vps_payment_validation_deploy\deploy_to_vps.bat
echo ssh %%VPS_USER%%@%%VPS_HOST%% "mkdir -p %%VPS_PATH%%/resources/views/components" >> vps_payment_validation_deploy\deploy_to_vps.bat
echo scp "resources/views/components/payment-validation-modals.blade.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/resources/views/components/ >> vps_payment_validation_deploy\deploy_to_vps.bat
echo scp "resources/views/membership/manage-member.blade.php" %%VPS_USER%%@%%VPS_HOST%%:%%VPS_PATH%%/resources/views/membership/ >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo. >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo Step 2: Running deployment script on VPS... >> vps_payment_validation_deploy\deploy_to_vps.bat
echo ssh %%VPS_USER%%@%%VPS_HOST%% "cd ~ && chmod +x deploy_payment_validation.sh && ./deploy_payment_validation.sh" >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo. >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo   DEPLOYMENT COMPLETE! >> vps_payment_validation_deploy\deploy_to_vps.bat
echo echo ======================================== >> vps_payment_validation_deploy\deploy_to_vps.bat
echo pause >> vps_payment_validation_deploy\deploy_to_vps.bat

echo Step 4: Creating manual upload instructions...

echo # Payment Validation System - Manual Upload Instructions > vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ## Files to Upload: >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 1. **app/Http/Controllers/MembershipController.php** → `/var/www/silencio-gym/app/Http/Controllers/` >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 2. **app/Http/Controllers/EmployeeController.php** → `/var/www/silencio-gym/app/Http/Controllers/` >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 3. **web.php** → `/var/www/silencio-gym/routes/` >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 4. **resources/views/components/payment-validation-modals.blade.php** → `/var/www/silencio-gym/resources/views/components/` >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo 5. **resources/views/membership/manage-member.blade.php** → `/var/www/silencio-gym/resources/views/membership/` >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ## After Upload Commands: >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ```bash >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # SSH into your VPS >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ssh root@156.67.221.184 >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Navigate to Laravel directory >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo cd /var/www/silencio-gym >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Clear caches >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan config:clear >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan route:clear >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan view:clear >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan cache:clear >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo. >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo # Test the routes >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo php artisan route:list --name=membership.check-active-membership >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md
echo ``` >> vps_payment_validation_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md

echo Step 5: Creating ZIP file for easy upload...

REM Create ZIP file using PowerShell
powershell -command "Compress-Archive -Path 'vps_payment_validation_deploy\*' -DestinationPath 'vps_payment_validation_deploy.zip' -Force"

echo.
echo ========================================
echo   VPS DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo ✅ Payment validation files prepared for VPS
echo ✅ Deployment scripts created  
echo ✅ Manual upload instructions included
echo ✅ ZIP file created for easy upload
echo.
echo 🚀 Deployment Options:
echo.
echo 1. **Automatic Deployment (if you have SSH access):**
echo    Run: vps_payment_validation_deploy\deploy_to_vps.bat
echo.
echo 2. **Manual Upload:**
echo    - Upload vps_payment_validation_deploy.zip to your VPS
echo    - Extract and run deploy_payment_validation.sh
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
    cd vps_payment_validation_deploy
    call deploy_to_vps.bat
) else (
    echo.
    echo Deployment package ready in: vps_payment_validation_deploy\
    echo ZIP file ready: vps_payment_validation_deploy.zip
)

pause
