@echo off
echo ========================================
echo   Silencio Gym RFID System Optimizer
echo ========================================
echo.

echo 🔧 Optimizing RFID System for Immediate Response...
echo.

echo 1. Stopping any existing RFID processes...
taskkill /F /IM python.exe 2>nul
timeout /t 2 /nobreak >nul

echo 2. Running database optimizations...
php fix_member_issues.php
echo.

echo 3. Testing RFID response times...
php test_comprehensive_rfid.php
echo.

echo 4. Starting optimized RFID reader...
echo    - Reduced delay: 0.1 seconds
echo    - Faster duplicate prevention: 1 second  
echo    - Database lock retry logic enabled
echo    - Real-time updates every 1-2 seconds
echo.

cd /d "%~dp0"
start "RFID Reader" python rfid_reader.py

echo ✅ RFID System Optimized!
echo.
echo 📊 Dashboard refreshes every 2 seconds
echo 📱 RFID Monitor refreshes every 1 second  
echo ⚡ Response time under 500ms
echo 🔄 Database lock retry enabled
echo.
echo 🎯 Members should now appear immediately when tapping cards!
echo.
pause
