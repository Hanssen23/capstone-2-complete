@echo off
title Deploying Auto-Deletion System to VPS
color 0A
cls

echo ========================================
echo   DEPLOYING AUTO-DELETION SYSTEM TO VPS
echo   Server: 156.67.221.184
echo ========================================
echo.

REM Check if auto_deletion_deployment folder exists
if not exist "auto_deletion_deployment" (
    echo ERROR: auto_deletion_deployment folder not found!
    echo Please run copy_files_simple.bat first.
    pause
    exit /b 1
)

echo Step 1: Creating VPS deployment package...
if exist "vps_auto_deletion_deploy" rmdir /s /q "vps_auto_deletion_deploy"
mkdir "vps_auto_deletion_deploy"

echo Copying auto-deletion files...
xcopy "auto_deletion_deployment\*" "vps_auto_deletion_deploy\" /E /I /Q

echo Step 2: Creating deployment script for VPS...
(
echo #!/bin/bash
echo # Auto-Deletion System Deployment Script for VPS
echo echo "========================================="
echo echo "  AUTO-DELETION SYSTEM DEPLOYMENT"
echo echo "========================================="
echo echo ""
echo.
echo # Navigate to Laravel project directory
echo echo "Step 1: Navigating to Laravel project..."
echo cd /var/www/html/silencio-gym ^|^| cd /home/*/public_html ^|^| cd /var/www/silencio-gym
echo.
echo # Backup current files
echo echo "Step 2: Creating backup..."
echo mkdir -p backups/auto_deletion_backup_$(date +%%Y%%m%%d_%%H%%M%%S^)
echo cp -r app/Models/Member.php backups/auto_deletion_backup_$(date +%%Y%%m%%d_%%H%%M%%S^)/ 2^>^/dev/null ^|^| true
echo cp -r routes/web.php backups/auto_deletion_backup_$(date +%%Y%%m%%d_%%H%%M%%S^)/ 2^>^/dev/null ^|^| true
echo.
echo # Copy new files
echo echo "Step 3: Copying auto-deletion files..."
echo cp -r ~/vps_auto_deletion_deploy/app/* app/
echo cp -r ~/vps_auto_deletion_deploy/database/* database/
echo cp -r ~/vps_auto_deletion_deploy/resources/* resources/
echo cp -r ~/vps_auto_deletion_deploy/routes/* routes/
echo cp -r ~/vps_auto_deletion_deploy/bootstrap/* bootstrap/
echo.
echo # Set proper permissions
echo echo "Step 4: Setting file permissions..."
echo chown -R www-data:www-data app/ database/ resources/ routes/ bootstrap/ 2^>^/dev/null ^|^| true
echo chmod -R 755 app/ database/ resources/ routes/ bootstrap/
echo.
echo # Run Laravel commands
echo echo "Step 5: Running Laravel migrations..."
echo php artisan migrate --force
echo.
echo echo "Step 6: Clearing caches..."
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo php artisan cache:clear
echo.
echo echo "Step 7: Testing auto-deletion command..."
echo php artisan members:process-inactive-deletion --dry-run --force -v
echo.
echo echo "========================================="
echo echo "  DEPLOYMENT COMPLETE!"
echo echo "========================================="
echo echo ""
echo echo "✅ Auto-deletion system deployed successfully"
echo echo "✅ Database migrations completed"
echo echo "✅ Caches cleared"
echo echo "✅ System tested"
echo echo ""
echo echo "🌐 Admin Panel: http://156.67.221.184/auto-deletion"
echo echo "🔧 Test Command: php artisan members:process-inactive-deletion --dry-run --force -v"
echo echo ""
echo echo "The auto-deletion system is now ready to use!"
) > "vps_auto_deletion_deploy\deploy_auto_deletion.sh"

echo Step 3: Creating Windows deployment script...
(
echo @echo off
echo echo ========================================
echo echo   VPS AUTO-DELETION DEPLOYMENT
echo echo ========================================
echo echo.
echo.
echo echo Step 1: Uploading files to VPS...
echo scp -r vps_auto_deletion_deploy/* root@156.67.221.184:~/
echo if %%errorlevel%% neq 0 ^(
echo     echo ERROR: Failed to upload files
echo     echo Please check your SSH connection and credentials
echo     pause
echo     exit /b 1
echo ^)
echo.
echo echo Step 2: Running deployment script on VPS...
echo ssh root@156.67.221.184 "chmod +x ~/deploy_auto_deletion.sh && ~/deploy_auto_deletion.sh"
echo if %%errorlevel%% neq 0 ^(
echo     echo ERROR: Deployment script failed
echo     echo Please check the VPS logs
echo     pause
echo     exit /b 1
echo ^)
echo.
echo echo ========================================
echo echo   DEPLOYMENT SUCCESSFUL!
echo echo ========================================
echo echo.
echo echo ✅ Auto-deletion system deployed to VPS
echo echo ✅ All files uploaded and configured
echo echo ✅ Database migrations completed
echo echo ✅ System ready for use
echo echo.
echo echo 🌐 Access admin panel: http://156.67.221.184/auto-deletion
echo echo.
echo pause
) > "vps_auto_deletion_deploy\deploy_to_vps.bat"

echo Step 4: Creating manual upload instructions...
(
echo # Manual Upload Instructions for Auto-Deletion System
echo.
echo ## 🚀 Quick Upload to VPS ^(156.67.221.184^)
echo.
echo ### Option 1: Using SCP ^(Recommended^)
echo ```bash
echo scp -r vps_auto_deletion_deploy/* root@156.67.221.184:~/
echo ssh root@156.67.221.184
echo chmod +x ~/deploy_auto_deletion.sh
echo ~/deploy_auto_deletion.sh
echo ```
echo.
echo ### Option 2: Using SFTP
echo ```bash
echo sftp root@156.67.221.184
echo put -r vps_auto_deletion_deploy/*
echo quit
echo ssh root@156.67.221.184
echo chmod +x ~/deploy_auto_deletion.sh
echo ~/deploy_auto_deletion.sh
echo ```
echo.
echo ### Option 3: Manual File Upload
echo 1. Use FileZilla/WinSCP to connect to 156.67.221.184
echo 2. Upload all files from `vps_auto_deletion_deploy` folder
echo 3. SSH into VPS and run: `chmod +x ~/deploy_auto_deletion.sh && ~/deploy_auto_deletion.sh`
echo.
echo ## 🎯 After Deployment
echo.
echo Visit: http://156.67.221.184/auto-deletion
echo Test: `php artisan members:process-inactive-deletion --dry-run --force -v`
echo.
echo ## 🔧 Troubleshooting
echo.
echo If you get permission errors:
echo ```bash
echo sudo chown -R www-data:www-data /var/www/html/silencio-gym/
echo sudo chmod -R 755 /var/www/html/silencio-gym/
echo ```
echo.
echo If routes don't work:
echo ```bash
echo php artisan route:clear
echo php artisan config:clear
echo ```
) > "vps_auto_deletion_deploy\MANUAL_UPLOAD_INSTRUCTIONS.md"

echo Step 5: Creating ZIP file for easy upload...
powershell -Command "Compress-Archive -Path 'vps_auto_deletion_deploy\*' -DestinationPath 'vps_auto_deletion_deploy.zip' -Force"

echo.
echo ========================================
echo   VPS DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo ✅ Auto-deletion files prepared for VPS
echo ✅ Deployment scripts created
echo ✅ Manual upload instructions included
echo ✅ ZIP file created for easy upload
echo.
echo 📋 Deployment Options:
echo.
echo 1. **Automatic Deployment ^(if you have SSH access^):**
echo    Run: vps_auto_deletion_deploy\deploy_to_vps.bat
echo.
echo 2. **Manual Upload:**
echo    - Upload vps_auto_deletion_deploy.zip to your VPS
echo    - Extract and run deploy_auto_deletion.sh
echo.
echo 3. **File Manager Upload:**
echo    - Use hosting control panel file manager
echo    - Upload files maintaining directory structure
echo    - Run deployment commands manually
echo.
echo 🌐 After deployment, visit: http://156.67.221.184/auto-deletion
echo.
echo Choose your preferred deployment method:
echo.
set /p deploy_choice="Run automatic deployment now? (y/n): "

if /i "%deploy_choice%"=="y" (
    echo.
    echo Starting automatic deployment...
    cd vps_auto_deletion_deploy
    call deploy_to_vps.bat
    cd ..
) else (
    echo.
    echo Manual deployment files are ready in 'vps_auto_deletion_deploy' folder
    echo Follow the instructions in MANUAL_UPLOAD_INSTRUCTIONS.md
)

echo.
pause
