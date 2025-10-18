@echo off
title Silencio Gym - RFID Reader
color 0A

echo.
echo ========================================
echo    SILENCIO GYM - RFID READER
echo ========================================
echo.
echo 📍 Location: %CD%
echo 🌐 Server: http://156.67.221.184
echo 📡 Status: Connecting...
echo.

REM Check if Python is installed
python --version >nul 2>&1
if errorlevel 1 (
    echo ❌ ERROR: Python is not installed or not in PATH
    echo.
    echo Please install Python first:
    echo https://www.python.org/downloads/
    echo.
    pause
    exit /b 1
)

REM Check if we're in the right directory
if not exist "simple_rfid_reader.py" (
    echo ❌ ERROR: simple_rfid_reader.py not found
    echo.
    echo Make sure you're running this from:
    echo c:\Users\hanss\Documents\silencio-gym-mms-main\silencio-gym-mms-main
    echo.
    pause
    exit /b 1
)

REM Check if ACR122U is connected
echo 🔍 Checking RFID reader...
python -c "from smartcard.System import readers; r=readers(); print('✅ Found reader:', r[0] if r else '❌ No reader found'); exit(0 if r else 1)" 2>nul
if errorlevel 1 (
    echo ❌ ERROR: ACR122U RFID reader not found
    echo.
    echo Please check:
    echo - USB cable is connected
    echo - Reader drivers are installed
    echo - Reader is powered on
    echo.
    pause
    exit /b 1
)

echo ✅ RFID reader detected
echo ✅ Python is ready
echo ✅ Starting RFID system...
echo.
echo ========================================
echo    READY TO SCAN CARDS!
echo ========================================
echo.
echo 💡 Place RFID cards on the reader
echo 🛑 Press Ctrl+C to stop
echo.

REM Start the RFID reader
python simple_rfid_reader.py

echo.
echo ========================================
echo    RFID READER STOPPED
echo ========================================
echo.
pause
