@echo off
echo ========================================
echo   UPLOAD AUTO-DELETION FILES TO VPS
echo ========================================
echo.
echo This script will help you upload files to your VPS at 156.67.221.184
echo.
echo UPLOAD OPTIONS:
echo 1. Use SCP (if you have SSH access)
echo 2. Use SFTP (if you have SFTP client)
echo 3. Use FTP (if you have FTP access)
echo 4. Manual upload instructions
echo.
set /p choice="Choose option (1-4): "

if "%choice%"=="1" goto scp_upload
if "%choice%"=="2" goto sftp_upload
if "%choice%"=="3" goto ftp_upload
if "%choice%"=="4" goto manual_instructions
goto invalid_choice

:scp_upload
echo.
echo SCP Upload Method:
echo ------------------
set /p username="Enter your VPS username: "
set /p remote_path="Enter Laravel project path on VPS (e.g., /var/www/html/silencio-gym): "
echo.
echo Running SCP command...
echo Command: scp -r auto_deletion_deployment/* %username%@156.67.221.184:%remote_path%/
echo.
scp -r auto_deletion_deployment/* %username%@156.67.221.184:%remote_path%/
if %errorlevel%==0 (
    echo ✓ Files uploaded successfully!
    goto post_upload_commands
) else (
    echo ✗ Upload failed. Please check your credentials and try again.
    pause
    exit /b 1
)

:sftp_upload
echo.
echo SFTP Upload Method:
echo -------------------
set /p username="Enter your VPS username: "
echo.
echo Opening SFTP connection to %username%@156.67.221.184
echo.
echo SFTP Commands to run:
echo 1. cd /path/to/your/laravel/project
echo 2. put -r auto_deletion_deployment/*
echo 3. quit
echo.
sftp %username%@156.67.221.184
goto post_upload_commands

:ftp_upload
echo.
echo FTP Upload Method:
echo ------------------
echo Please use your FTP client (FileZilla, WinSCP, etc.) to upload files
echo.
echo Source: auto_deletion_deployment folder
echo Destination: Your Laravel project root on 156.67.221.184
echo.
echo Make sure to maintain the directory structure!
echo.
pause
goto post_upload_commands

:manual_instructions
echo.
echo MANUAL UPLOAD INSTRUCTIONS:
echo ===========================
echo.
echo 1. Use your preferred FTP/SFTP client (FileZilla, WinSCP, etc.)
echo 2. Connect to: 156.67.221.184
echo 3. Navigate to your Laravel project root directory
echo 4. Upload ALL files from 'auto_deletion_deployment' folder
echo 5. Maintain the exact directory structure
echo.
echo FILES TO UPLOAD:
echo ----------------
dir /s /b auto_deletion_deployment\*.php
echo.
echo IMPORTANT: Keep the folder structure intact!
echo.
pause
goto post_upload_commands

:post_upload_commands
echo.
echo ========================================
echo   POST-UPLOAD COMMANDS
echo ========================================
echo.
echo After uploading files, SSH into your VPS and run:
echo.
echo cd /path/to/your/laravel/project
echo php artisan migrate
echo php artisan config:clear
echo php artisan route:clear
echo php artisan view:clear
echo php artisan members:process-inactive-deletion --dry-run --force -v
echo.
echo Then visit: http://156.67.221.184/auto-deletion
echo.
pause
exit /b 0

:invalid_choice
echo Invalid choice. Please run the script again.
pause
exit /b 1
