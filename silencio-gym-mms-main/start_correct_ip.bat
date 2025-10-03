@echo off
echo ============================================
echo   Starting Silencio Gym Server (Correct IP)
echo ============================================
echo.

echo Detecting your IP address...
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do (
    set MY_IP=%%a
    goto :found_ip
)
:found_ip
set MY_IP=%MY_IP: =%

echo Your IP address: %MY_IP%
echo.

echo Updating .env file with correct IP...
powershell -Command "(Get-Content .env) -replace 'APP_URL=http://[^:]+:8001', 'APP_URL=http://%MY_IP%:8001' -replace 'RFID_API_URL=http://[^:]+:8001', 'RFID_API_URL=http://%MY_IP%:8001' | Set-Content .env"

echo Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
echo.

echo Starting Laravel server...
echo Server will be available at:
echo   http://localhost:8001
echo   http://127.0.0.1:8001  
echo   http://%MY_IP%:8001
echo.
echo Default admin login:
echo   Email: admin@admin.com
echo   Password: admin123
echo.

php artisan serve --host=0.0.0.0 --port=8001

pause
