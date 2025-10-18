@echo off
echo ========================================
echo    Simple RFID Reader for Silencio Gym
echo ========================================
echo.
echo Starting simple RFID reader...
echo API URL: http://156.67.221.184/api/rfid/tap
echo.
echo Make sure your ACR122U reader is connected!
echo.
echo USAGE TIPS:
echo - Place card on reader to check-in/out
echo - Each card has a 5-second cooldown
echo - Remove card and wait, or try different card
echo - Press Ctrl+C to stop the reader
echo.
pause

cd /d "%~dp0"
python simple_rfid_reader.py

echo.
echo RFID reader stopped.
pause
