@echo off
REM Enable SQLite extensions in php.ini
setlocal enabledelayedexpansion

set "phpini=C:\Program Files\php-8.4.14\php.ini"

REM Create a temporary file
set "tempfile=%temp%\php_temp.ini"

REM Read the php.ini and replace commented extensions
(for /f "delims=" %%a in ('type "%phpini%"') do (
    set "line=%%a"
    if "!line:~0,24!"==";extension=pdo_sqlite" (
        echo extension=pdo_sqlite
    ) else if "!line:~0,20!"==";extension=sqlite3" (
        echo extension=sqlite3
    ) else (
        echo !line!
    )
)) > "%tempfile%"

REM Replace the original file
copy /Y "%tempfile%" "%phpini%"
del "%tempfile%"

echo SQLite extensions enabled!
pause

