@echo off
echo ============================================
echo   Fixed Server Startup for Silencio Gym
echo ============================================
echo.

echo [1] Setting working directory...
cd /d "%~dp0"

echo [2] Checking if we're in the right directory...
if not exist "artisan" (
    echo ERROR: artisan file not found!
    echo Please run this script from your Laravel project root directory
    echo Current directory: %CD%
    pause
    exit /b 1
)
echo ✓ Found artisan file

echo [3] Detecting your IP address...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do (
    set MY_IP=%%a
    goto :found_ip
)
:found_ip
set MY_IP=%MY_IP: =%
echo Your IP: %MY_IP%

echo [4] Updating configuration...
powershell -Command "(Get-Content .env) -replace 'APP_URL=http://[^:]+:8001', 'APP_URL=http://127.0.0.1:8001' | Set-Content .env"
echo ✓ Updated APP_URL to localhost

echo [5] Clearing cache...
php artisan config:clear
php artisan cache:clear

echo [6] Installing dependencies...
composer install --quiet

echo [7] Starting server...
echo ✓ Server starting on http://127.0.0.1:8001
echo ✓ Also accessible via: http://%MY_IP%:8001
echo.
echo Default admin login:
echo   Email: admin@admin.com  
echo   Password: admin123
echo.
echo Press Ctrl+C to stop the server
echo ============================================

php artisan serve --host=127.0.0.1 --port=8001

echo.
echo Server stopped.
pause
