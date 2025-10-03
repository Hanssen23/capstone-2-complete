@echo off
echo ============================================
echo   Starting Silencio Gym Server Locally
echo ============================================
echo.

echo Checking if PHP is available...
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP or add it to your PATH
    pause
    exit /b 1
)

echo Checking Laravel installation...
if not exist artisan (
    echo ERROR: artisan file not found. Please run from Laravel root directory
    pause
    exit /b 1
)

echo Checking .env file...
if not exist .env (
    echo ERROR: .env file not found. Please copy hostinger.env.example to .env
    pause
    exit /b 1
)

echo.
echo Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
echo.

echo Starting Laravel development server...
echo.
echo Server will be available at:
echo    http://127.0.0.1:8000 (localhost only)
echo    http://156.67.221.184:8001 (external access)
echo.
echo Default admin login:
echo    Email: admin@admin.com
echo    Password: admin123
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve --host=156.67.221.184 --port=8001

echo.
echo Server stopped.
pause
