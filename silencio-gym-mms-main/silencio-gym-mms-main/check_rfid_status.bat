@echo off
echo ========================================
echo    RFID SYSTEM STATUS CHECK
echo ========================================
echo.

echo [1] Checking Python...
python --version 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Python is installed
) else (
    echo [ERROR] Python is NOT installed
)
echo.

echo [2] Checking Python Libraries...
python -c "import smartcard" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] pyscard is installed
) else (
    echo [ERROR] pyscard is NOT installed
)

python -c "import requests" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] requests is installed
) else (
    echo [ERROR] requests is NOT installed
)
echo.

echo [3] Checking NFC Reader...
python -c "from smartcard.System import readers; r = readers(); print('[OK] Found', len(r), 'reader(s)'); [print('  -', str(reader)) for reader in r]" 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] No NFC readers detected
)
echo.

echo [4] Checking if RFID Reader is running...
tasklist | findstr python.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Python RFID reader is RUNNING
) else (
    echo [WARNING] Python RFID reader is NOT running
)
echo.

echo [5] Checking Laravel Server...
netstat -an | findstr :8000 >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [OK] Laravel server is RUNNING on port 8000
) else (
    echo [WARNING] Laravel server is NOT running
)
echo.

echo ========================================
echo    STATUS SUMMARY
echo ========================================
echo.
echo To start the RFID system:
echo 1. Run: start_rfid_reader.bat
echo 2. Or manually: python rfid_reader.py
echo.
pause

