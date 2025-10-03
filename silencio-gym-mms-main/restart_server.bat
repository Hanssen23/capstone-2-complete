@echo off
echo ============================================
echo   Restarting Silencio Gym Server
echo ============================================
echo.

echo [1] Stopping any running processes on port 8001...
for /f "tokens=5" %%a in ('netstat -aon ^| find ":8001"') do taskkill /F /PID %%a 2>nul

echo [2] Checking database connection...
php artisan tinker --execute="echo 'Database: ' . config('database.default'); try { DB::connection()->getPdo(); echo ' - Connected!'; } catch(Exception \$e) { echo ' - Error: ' . \$e->getMessage(); }"

echo.
echo [3] Starting server on port 8001...
echo Server will be available at: http://156.67.221.184:8001
echo.

php artisan serve --host=156.67.221.184 --port=8001

pause
