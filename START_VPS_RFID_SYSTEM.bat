@echo off
title RFID System - VPS Mode
color 0A
cls

echo ============================================================
echo    STARTING RFID SYSTEM FOR VPS
echo ============================================================
echo.

cd /d "C:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main"

echo [1/3] Checking configuration...
echo.

REM Check if rfid_config.json has VPS URL
findstr /C:"156.67.221.184" rfid_config.json >nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] RFID reader configured for VPS: http://156.67.221.184
) else (
    echo [ERROR] RFID reader is NOT configured for VPS
    echo Please check rfid_config.json
    pause
    exit /b 1
)
echo.

echo [2/3] Stopping any existing RFID reader...
taskkill /F /IM python.exe 2>nul
timeout /t 2 /nobreak >nul
echo [OK] Cleared any existing processes
echo.

echo [3/3] Starting RFID reader for VPS...
echo.
echo ============================================================
echo    RFID READER ACTIVE - VPS MODE
echo ============================================================
echo.
echo Configuration:
echo   - VPS URL: http://156.67.221.184
echo   - API Endpoint: /api/rfid/tap
echo   - Device ID: main_reader
echo.
echo The RFID reader will send card data to your VPS server.
echo.
echo View the RFID Monitor at:
echo   http://156.67.221.184/rfid-monitor
echo.
echo Now you can tap your RFID card!
echo.
echo Press Ctrl+C to stop the RFID reader
echo ============================================================
echo.

python rfid_reader.py

echo.
echo RFID reader stopped.
pause

