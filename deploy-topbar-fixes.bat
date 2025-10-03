@echo off
echo ========================================
echo Deploying Topbar and Name Validation Fixes
echo ========================================
echo.

echo Uploading topbar.blade.php...
scp -o StrictHostKeyChecking=no "silencio-gym-mms-main\resources\views\components\topbar.blade.php" root@156.67.221.184:/var/www/silencio-gym/resources/views/components/topbar.blade.php
if %ERRORLEVEL% EQU 0 (
    echo [OK] topbar.blade.php uploaded successfully
) else (
    echo [ERROR] Failed to upload topbar.blade.php
)
echo.

echo Uploading accounts.blade.php...
scp -o StrictHostKeyChecking=no "silencio-gym-mms-main\resources\views\accounts.blade.php" root@156.67.221.184:/var/www/silencio-gym/resources/views/accounts.blade.php
if %ERRORLEVEL% EQU 0 (
    echo [OK] accounts.blade.php uploaded successfully
) else (
    echo [ERROR] Failed to upload accounts.blade.php
)
echo.

echo ========================================
echo Deployment Complete!
echo ========================================
echo.
echo Please test the changes:
echo 1. Go to http://156.67.221.184/dashboard
echo 2. Check that the top right has NO user info
echo 3. Check that the left sidebar shows your name and role
echo 4. Go to http://156.67.221.184/accounts
echo 5. Try typing numbers in First Name or Last Name fields
echo 6. Numbers and special characters should be blocked
echo.
pause

