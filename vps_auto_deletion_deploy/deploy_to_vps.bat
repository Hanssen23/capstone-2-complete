@echo off
echo ========================================
echo   VPS AUTO-DELETION DEPLOYMENT
echo ========================================
echo.

echo Step 1: Uploading files to VPS...
scp -r vps_auto_deletion_deploy/* root@156.67.221.184:~/
if %errorlevel% neq 0 (
    echo ERROR: Failed to upload files
    echo Please check your SSH connection and credentials
    pause
    exit /b 1
)

echo Step 2: Running deployment script on VPS...
ssh root@156.67.221.184 "chmod +x ~/deploy_auto_deletion.sh && ~/deploy_auto_deletion.sh"
if %errorlevel% neq 0 (
    echo ERROR: Deployment script failed
    echo Please check the VPS logs
    pause
    exit /b 1
)

echo ========================================
echo   DEPLOYMENT SUCCESSFUL!
echo ========================================
echo.
echo ✅ Auto-deletion system deployed to VPS
echo ✅ All files uploaded and configured
echo ✅ Database migrations completed
echo ✅ System ready for use
echo.
echo 🌐 Access admin panel: http://156.67.221.184/auto-deletion
echo.
pause
