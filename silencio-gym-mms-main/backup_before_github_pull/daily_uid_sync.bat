@echo off
REM Daily UID Pool Synchronization
REM Run this daily to prevent duplicate UID errors

echo ========================================
echo Daily UID Pool Synchronization
echo ========================================
echo.

echo Checking system status...
php check_current_status.php
echo.

echo Running UID pool synchronization...
echo.
php uid_pool_sync_validator.php
echo.

echo Synchronization complete! Press any key to exit.
pause >nul
