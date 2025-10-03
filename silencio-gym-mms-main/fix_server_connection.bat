@echo off
echo ============================================
echo   Silencio Gym - Server Connection Fix
echo ============================================
echo.

echo [1/6] Checking if you're on the server...
echo IP Address: 156.67.221.184
echo Port: 8001
echo.

echo [2/6] Generate application key...
if not exist .env (
    echo ERROR: .env file not found!
    echo Please create .env file from hostinger.env.example
    echo Update database credentials and generate APP_KEY
    pause
    exit /b 1
)

echo Generating application key...
php artisan key:generate --force
echo.

echo [3/6] Setting proper file permissions...
echo Setting storage permissions...
icacls storage /grant "Everyone:(OI)(CI)F" /T
icacls bootstrap\cache /grant "Everyone:(OI)(CI)F" /T
echo.

echo [4/6] Clear and optimize application...
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo.

echo [5/6] Running database migrations...
php artisan migrate --force
php artisan db:seed --force
echo.

echo [6/6] Starting development server...
echo Starting Laravel development server on port 8001...
echo You can now access: http://156.67.221.184:8001
echo.
echo Press Ctrl+C to stop the server
php artisan serve --host=156.67.221.184 --port=8001

echo.
echo ============================================
echo   Server Setup Complete!
echo ============================================
pause
