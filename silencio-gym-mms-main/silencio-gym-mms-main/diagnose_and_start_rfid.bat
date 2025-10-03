@echo off
title RFID System Diagnostic and Startup
color 0A
echo ========================================
echo    RFID SYSTEM DIAGNOSTIC
echo ========================================
echo.

REM Change to the correct directory
cd /d "%~dp0"
echo Current Directory: %CD%
echo.

echo [1/6] Checking Python Installation...
where python >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Python is installed
    python --version
) else (
    echo [ERROR] Python is NOT installed!
    echo Please install Python from https://www.python.org/downloads/
    pause
    exit /b 1
)
echo.

echo [2/6] Checking Python Dependencies...
python -c "import smartcard" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] pyscard is installed
) else (
    echo [WARNING] pyscard is NOT installed
    echo Installing pyscard...
    pip install pyscard
)

python -c "import requests" >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] requests is installed
) else (
    echo [WARNING] requests is NOT installed
    echo Installing requests...
    pip install requests
)
echo.

echo [3/6] Checking RFID Reader Script...
if exist "rfid_reader.py" (
    echo [OK] rfid_reader.py found
) else (
    echo [ERROR] rfid_reader.py NOT found!
    echo Please make sure you're in the correct directory
    pause
    exit /b 1
)
echo.

echo [4/6] Checking if RFID Reader is already running...
tasklist | findstr python.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [WARNING] Python process is already running
    echo Stopping existing Python processes...
    taskkill /F /IM python.exe >nul 2>&1
    timeout /t 2 /nobreak >nul
    echo [OK] Stopped existing processes
) else (
    echo [OK] No existing Python processes found
)
echo.

echo [5/6] Checking NFC Reader Hardware...
echo Please make sure your ACR122U NFC reader is plugged in via USB
echo.
python -c "from smartcard.System import readers; r = readers(); print('[OK] Found %d reader(s):' % len(r)); [print('  - %s' % str(reader)) for reader in r]" 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] No NFC readers detected!
    echo.
    echo Troubleshooting steps:
    echo 1. Make sure the ACR122U is plugged into a USB port
    echo 2. Check if the device appears in Device Manager
    echo 3. Try unplugging and replugging the device
    echo 4. Install the ACS driver if not already installed
    echo.
    pause
    exit /b 1
)
echo.

echo [6/6] Checking Laravel Server...
netstat -an | findstr :8000 >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Laravel server is running on port 8000
) else (
    echo [WARNING] Laravel server is NOT running on port 8000
    echo.
    echo Starting Laravel server...
    start "Laravel Server" cmd /k "php artisan serve"
    timeout /t 3 /nobreak >nul
    echo [OK] Laravel server started
)
echo.

echo ========================================
echo    STARTING RFID READER
echo ========================================
echo.
echo The RFID reader will now start...
echo You can tap your NFC card to test it.
echo.
echo Press Ctrl+C to stop the RFID reader
echo.
pause

REM Start the RFID reader
echo Starting RFID reader...
python rfid_reader.py

echo.
echo RFID reader stopped.
pause

