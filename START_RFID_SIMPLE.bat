@echo off
title RFID System - Simple Start
color 0A
cls
echo ========================================
echo    STARTING RFID SYSTEM
echo ========================================
echo.

cd /d "C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main"

echo Step 1: Checking if Laravel server is running...
netstat -an | findstr :8000 >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo Starting Laravel server...
    start "Laravel Server" cmd /k "cd /d C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main && php artisan serve"
    timeout /t 3 /nobreak >nul
    echo [OK] Laravel server started
) else (
    echo [OK] Laravel server is already running
)
echo.

echo Step 2: Starting RFID Reader...
echo.
echo ========================================
echo    RFID READER ACTIVE
echo ========================================
echo.
echo The RFID reader is now starting...
echo.
echo What you should see:
echo - "Starting ACR122U RFID reader..."
echo - "Waiting for cards..."
echo.
echo Then you can tap your NFC card!
echo.
echo Press Ctrl+C to stop the RFID reader
echo ========================================
echo.

python rfid_reader.py

echo.
echo RFID reader stopped.
pause

