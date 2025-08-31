@echo off
echo ========================================
echo    ACS Unified Driver Installation
echo ========================================
echo.

echo This script will help you install the ACS Unified Driver
echo for your ACR122U NFC Reader.
echo.

echo Step 1: Checking current smart card readers...
echo.
powershell -Command "Get-PnpDevice | Where-Object {$_.FriendlyName -like '*Smartcard*' -or $_.FriendlyName -like '*Card*'} | Select-Object Status, FriendlyName"
echo.

echo Step 2: Installing ACS Unified Driver...
echo.
echo Please follow these steps:
echo 1. Open Device Manager (devmgmt.msc)
echo 2. Find "Microsoft Usbccid Smartcard Reader" under Smart card readers
echo 3. Right-click and select "Update driver"
echo 4. Choose "Browse my computer for drivers"
echo 5. Navigate to: C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280
echo 6. Select the folder and click Next
echo 7. Follow the installation prompts
echo.

echo Step 3: After installation, test the RFID reader:
echo.
echo 1. Connect your ACR122U reader to USB
echo 2. Run: start_rfid_reader.bat
echo 3. Place an NFC card on the reader
echo.

echo Press any key to open Device Manager...
pause >nul

start devmgmt.msc

echo.
echo Installation guide completed.
echo Please complete the driver installation in Device Manager.
pause
