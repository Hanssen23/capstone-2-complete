@echo off
title RFID System - Local Setup
color 0A
cls

echo ============================================================
echo    STARTING LOCAL RFID SYSTEM
echo ============================================================
echo.

cd /d "C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main"

echo [1/4] Checking configuration...
echo.

REM Check if rfid_config.json has localhost
findstr /C:"localhost:8000" rfid_config.json >nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] RFID reader configured for localhost:8000
) else (
    echo [WARNING] RFID reader may not be configured for localhost
    echo Please check rfid_config.json
)
echo.

echo [2/4] Starting Laravel server...
echo.
start "Laravel Server" cmd /k "cd /d C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main && php artisan serve"
timeout /t 3 /nobreak >nul
echo [OK] Laravel server started on http://localhost:8000
echo.

echo [3/4] Starting RFID reader...
echo.
start "RFID Reader" cmd /k "cd /d C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main && python rfid_reader.py"
timeout /t 3 /nobreak >nul
echo [OK] RFID reader started
echo.

echo [4/4] Opening RFID Monitor...
echo.
timeout /t 2 /nobreak >nul
start http://localhost:8000/rfid-monitor
echo [OK] RFID Monitor opened in browser
echo.

echo ============================================================
echo    RFID SYSTEM READY!
echo ============================================================
echo.
echo Two windows have been opened:
echo   1. Laravel Server (http://localhost:8000)
echo   2. RFID Reader (Python script)
echo.
echo Your browser should open to:
echo   http://localhost:8000/rfid-monitor
echo.
echo Now you can tap your RFID card!
echo.
echo To stop the system:
echo   - Close both command windows
echo   - Or press Ctrl+C in each window
echo.
echo ============================================================
pause

