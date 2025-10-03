@echo off
echo ========================================
echo   Opening Device Manager
echo ========================================
echo.
echo Look for "ACR122 Smart Card Reader"
echo It should be under "Smart card readers"
echo.
echo Driver location:
echo C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280
echo.
echo Instructions:
echo 1. Right-click "ACR122 Smart Card Reader"
echo 2. Click "Update driver"
echo 3. Click "Browse my computer for drivers"
echo 4. Click "Browse" and select the folder above
echo 5. Click "Next" and wait for installation
echo.
pause

start devmgmt.msc

