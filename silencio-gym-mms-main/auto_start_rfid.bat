@echo off
echo Starting RFID Reader Service...
echo.

REM Set the project directory
set PROJECT_DIR=%~dp0
cd /d "%PROJECT_DIR%"

REM Set Python path
set PYTHON_PATH=C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe

REM Check if Python exists
if not exist "%PYTHON_PATH%" (
    echo Error: Python not found at %PYTHON_PATH%
    echo Please update the PYTHON_PATH in this batch file.
    pause
    exit /b 1
)

REM Check if rfid_reader.py exists
if not exist "rfid_reader.py" (
    echo Error: rfid_reader.py not found in the project directory.
    pause
    exit /b 1
)

echo Starting RFID reader in background...
echo Logs will be saved to: storage\logs\rfid_reader.log
echo.

REM Start the RFID reader in background
start /B "%PYTHON_PATH%" "rfid_reader.py" --api http://silencio-gym-mms-main.test

echo RFID reader started successfully!
echo You can monitor the logs at: storage\logs\rfid_reader.log
echo.
echo Press any key to exit...
pause >nul
