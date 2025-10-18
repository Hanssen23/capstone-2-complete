@echo off
REM Deployment script for VPS fixes (Windows version)
REM This script deploys all the fixes to the VPS at 156.67.221.184

setlocal

set VPS_HOST=156.67.221.184
set VPS_USER=root
set VPS_PATH=/var/www/silencio-gym

echo =========================================
echo Deploying Fixes to VPS
echo =========================================
echo.
echo VPS Host: %VPS_HOST%
echo VPS Path: %VPS_PATH%
echo.

REM Check if we have scp and ssh available
where scp >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Error: scp command not found. Please install OpenSSH or use WSL.
    echo.
    echo You can install OpenSSH on Windows 10/11:
    echo   Settings ^> Apps ^> Optional Features ^> Add a feature ^> OpenSSH Client
    pause
    exit /b 1
)

echo Testing VPS connection...
ssh -o ConnectTimeout=5 %VPS_USER%@%VPS_HOST% "echo Connection successful" >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo Error: Cannot connect to VPS. Please check your SSH connection.
    pause
    exit /b 1
)
echo [OK] VPS connection successful
echo.

REM Upload AuthController
echo Uploading AuthController.php...
scp app\Http\Controllers\AuthController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
if %ERRORLEVEL% NEQ 0 (
    echo Error uploading AuthController.php
    pause
    exit /b 1
)
echo [OK] AuthController.php uploaded

REM Upload MemberAuthController
echo Uploading MemberAuthController.php...
scp app\Http\Controllers\MemberAuthController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
if %ERRORLEVEL% NEQ 0 (
    echo Error uploading MemberAuthController.php
    pause
    exit /b 1
)
echo [OK] MemberAuthController.php uploaded

REM Upload MembershipController
echo Uploading MembershipController.php...
scp app\Http\Controllers\MembershipController.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/
if %ERRORLEVEL% NEQ 0 (
    echo Error uploading MembershipController.php
    pause
    exit /b 1
)
echo [OK] MembershipController.php uploaded

REM Upload UID pool seeder
echo Uploading seed_uid_pool.php...
scp seed_uid_pool.php %VPS_USER%@%VPS_HOST%:%VPS_PATH%/
if %ERRORLEVEL% NEQ 0 (
    echo Error uploading seed_uid_pool.php
    pause
    exit /b 1
)
echo [OK] seed_uid_pool.php uploaded

echo.
echo =========================================
echo Running Post-Deployment Tasks
echo =========================================
echo.

REM Run UID pool seeder
echo Seeding UID pool...
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php seed_uid_pool.php"
echo.

REM Clear Laravel caches
echo Clearing Laravel caches...
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php artisan cache:clear"
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php artisan config:clear"
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php artisan route:clear"
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && php artisan view:clear"
echo [OK] Caches cleared

REM Set proper permissions
echo Setting proper permissions...
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && chmod -R 755 storage bootstrap/cache"
ssh %VPS_USER%@%VPS_HOST% "cd %VPS_PATH% && chown -R www-data:www-data storage bootstrap/cache"
echo [OK] Permissions set

echo.
echo =========================================
echo Deployment Complete!
echo =========================================
echo.
echo [OK] All fixes have been deployed successfully!
echo.
echo Please test the following:
echo   1. Employee logout (should not show 500 error)
echo   2. Member registration (should work with valid data)
echo   3. Payment confirmation (should process successfully)
echo.
echo For detailed testing instructions, see DEPLOYMENT_FIXES.md
echo.
pause

