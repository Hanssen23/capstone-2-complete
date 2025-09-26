@echo off
echo ========================================
echo   Silencio Gym RFID Reader Restart
echo ========================================
echo.

echo Stopping any existing RFID reader processes...
taskkill /F /IM python.exe 2>nul
timeout /t 2 /nobreak >nul

echo Starting RFID reader with optimized settings...
echo - Reduced delay: 0.1 seconds
echo - Faster duplicate prevention: 1 second
echo - Real-time updates enabled
echo.

cd /d "%~dp0"
python rfid_reader.py

pause
