@echo off
REM Simple Test Runner for Silencio Gym
echo Testing Silencio Gym Management System
echo ======================================

REM Check basic PHP functionality
php -v
echo.

REM Check Laravel commands
php artisan --version
echo.

REM Try to run Pest
echo Attempting to run Pest...
vendor\bin\pest --version
echo.

REM Try PHPUnit instead
echo Attempting PHPUnit...
vendor\bin\phpunit --version
echo.

echo Test framework check complete.
pause
