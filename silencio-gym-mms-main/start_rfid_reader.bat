@echo off
echo ========================================
echo    ACR122U NFC Reader Integration
echo ========================================
echo.

REM Check if Python is installed
"C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe" --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Python is not found at expected location
    echo Please check Python installation
    pause
    exit /b 1
)

REM Check if required packages are installed
echo Checking required packages...
"C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe" -c "import smartcard" >nul 2>&1
if errorlevel 1 (
    echo Installing pyscard...
    "C:\Users\hanss\AppData\Local\Programs\Python\Python313\Scripts\pip.exe" install pyscard
)

"C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe" -c "import requests" >nul 2>&1
if errorlevel 1 (
    echo Installing requests...
    "C:\Users\hanss\AppData\Local\Programs\Python\Python313\Scripts\pip.exe" install requests
)

echo.
echo Starting RFID Reader...
echo Make sure your ACR122U is connected via USB
echo.
"C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe" rfid_reader.py

pause
