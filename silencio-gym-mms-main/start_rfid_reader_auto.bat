@echo off
echo Starting RFID Reader System...
echo ==============================

cd /d "%~dp0"

echo Checking if RFID reader is already running...
tasklist | findstr python >nul
if %errorlevel% == 0 (
    echo RFID reader is already running.
    echo Stopping existing processes...
    taskkill /f /im python.exe >nul 2>&1
    timeout /t 2 >nul
)

echo Starting RFID reader...
start "RFID Reader" python rfid_reader.py

echo Waiting for RFID reader to initialize...
timeout /t 3 >nul

echo Testing RFID system...
php test_rfid_after_logout.php

echo.
echo RFID Reader started successfully!
echo The RFID reader is now running in the background.
echo You can close this window - the RFID reader will continue running.
echo.
echo To stop the RFID reader, run: taskkill /f /im python.exe
echo.
pause
