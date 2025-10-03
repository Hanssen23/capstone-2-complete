@echo off
echo ========================================
echo   ACR122U Driver Installation (Easy)
echo ========================================
echo.

echo Step 1: Opening Device Manager...
echo.
echo When Device Manager opens:
echo 1. Look for "ACR122 Smart Card Reader" 
echo    (It might be under "Smart card readers" or "Other devices")
echo 2. Right-click on "ACR122 Smart Card Reader"
echo 3. Select "Update driver"
echo 4. Click "Browse my computer for drivers"
echo 5. Click "Browse" button
echo 6. Navigate to: C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280
echo 7. Click "OK" then "Next"
echo 8. Wait for installation to complete
echo.
pause

echo Opening Device Manager...
start devmgmt.msc

echo.
echo ========================================
echo Driver folder location:
echo C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280
echo ========================================
echo.
echo After installation, run this to test:
echo python silencio-gym-mms-main/debug_rfid_reader.py
echo.
pause

