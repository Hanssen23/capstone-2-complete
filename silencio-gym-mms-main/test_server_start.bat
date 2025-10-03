@echo off
echo Testing Laravel server startup...

echo Testing artisan command...
php artisan --version
if errorlevel 1 (
    echo ERROR: artisan command failed
    pause
    exit /b 1
)

echo Testing serve command...
php artisan serve --help | findstr serve
if errorlevel 1 (
    echo ERROR: serve command not available
    pause
    exit /b 1
)

echo Trying to start server on localhost...
echo You should see server startup messages...
echo Press Ctrl+C to stop when you see "Server started"
echo.

php artisan serve --host=127.0.0.1 --port=8001

echo.
echo Server stopped.
pause
