@echo off
echo ========================================
echo    Silencio RFID System Auto-Start
echo ========================================
echo.

:: Set Python path
set PYTHON_PATH=C:\Users\hanss\AppData\Local\Programs\Python\Python313\python.exe

:: Check if Python is installed
if not exist "%PYTHON_PATH%" (
    echo ERROR: Python not found at %PYTHON_PATH%
    echo Please check your Python installation
    pause
    exit /b 1
)

:: Check if PHP is installed
php --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and add it to PATH
    pause
    exit /b 1
)

:: Install Python dependencies if needed
echo Installing Python dependencies...
"%PYTHON_PATH%" -m pip install pyscard requests >nul 2>&1
if errorlevel 1 (
    echo WARNING: Failed to install Python dependencies
    echo You may need to run: "%PYTHON_PATH%" -m pip install pyscard requests
)

:: Clear any existing Python cache
echo Clearing Python cache...
if exist "__pycache__" rmdir /s /q "__pycache__" >nul 2>&1

:: Start Laravel server in background
echo Starting Laravel server...
start "Laravel Server" cmd /k "php -S localhost:8000 -t public"

:: Wait a moment for server to start
timeout /t 5 /nobreak >nul

:: Test if server is running
echo Testing server connection...
curl -s http://localhost:8000 >nul 2>&1
if errorlevel 1 (
    echo ERROR: Laravel server failed to start
    echo Please check if port 8000 is available
    pause
    exit /b 1
)

echo Laravel server is running on http://localhost:8000
echo.
echo ========================================
echo    SYSTEM READY FOR RFID TESTING
echo ========================================
echo.
echo 1. Open your browser and go to: http://localhost:8000
echo 2. Login to the system
echo 3. Go to "RFID Monitor" page
echo 4. The RFID reader will automatically start
echo 5. Place your ACR122U reader and tap cards
echo 6. Watch for real-time updates on the website
echo.
echo Starting RFID reader...
echo Press Ctrl+C to stop the RFID reader
echo.

:: Start the RFID reader with automatic restart
:rfid_loop
echo [%date% %time%] Starting RFID reader...
"%PYTHON_PATH%" rfid_reader.py
if errorlevel 1 (
    echo [%date% %time%] RFID reader stopped or crashed. Restarting in 5 seconds...
    timeout /t 5 /nobreak >nul
    goto rfid_loop
)

echo.
echo RFID reader stopped. Press any key to exit...
pause >nul
