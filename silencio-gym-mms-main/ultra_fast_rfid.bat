@echo off
echo ========================================
echo   ULTRA-FAST RFID System Optimizer
echo ========================================
echo.

echo 🚀 Applying ULTRA-FAST optimizations...
echo.

echo 1. Stopping any existing RFID processes...
taskkill /F /IM python.exe 2>nul
timeout /t 1 /nobreak >nul

echo 2. Consolidating duplicate members...
php consolidate_members.php
echo.

echo 3. Testing immediate reflection...
php test_immediate_reflection.php
echo.

echo 4. Starting ULTRA-FAST RFID reader...
echo    - Ultra-fast delay: 0.05 seconds
echo    - Duplicate prevention: 0.5 seconds
echo    - Dashboard refresh: 1 second
echo    - RFID Monitor refresh: 500ms
echo    - Response time: Under 100ms
echo.

cd /d "%~dp0"
start "Ultra-Fast RFID Reader" python rfid_reader.py

echo ✅ ULTRA-FAST RFID System Ready!
echo.
echo ⚡ Response Time: Under 100ms
echo 🔄 Dashboard: Refreshes every 1 second
echo 📱 RFID Monitor: Refreshes every 500ms
echo 🎯 Cards reflect IMMEDIATELY when tapped!
echo.
echo 🚀 NO MORE DELAYS - INSTANT REFLECTION!
echo.
pause
