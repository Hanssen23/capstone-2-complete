@echo off
echo ========================================
echo   COPYING AUTO-DELETION SYSTEM FILES
echo ========================================
echo.

set SOURCE=silencio-gym-mms-main
set DEST=auto_deletion_deployment

echo Creating directories...
mkdir "%DEST%\app\Console\Commands" 2>nul
mkdir "%DEST%\app\Http\Controllers" 2>nul
mkdir "%DEST%\app\Http\Middleware" 2>nul
mkdir "%DEST%\app\Models" 2>nul
mkdir "%DEST%\app\Notifications" 2>nul
mkdir "%DEST%\database\migrations" 2>nul
mkdir "%DEST%\resources\views\admin\auto-deletion" 2>nul
mkdir "%DEST%\resources\views\member\reactivation" 2>nul
mkdir "%DEST%\routes" 2>nul
mkdir "%DEST%\bootstrap" 2>nul
mkdir "%DEST%\app\Console" 2>nul

echo.
echo Copying files...

REM New files
copy "%SOURCE%\app\Console\Commands\ProcessInactiveMemberDeletion.php" "%DEST%\app\Console\Commands\" >nul 2>&1
if %errorlevel%==0 (echo ✓ ProcessInactiveMemberDeletion.php) else (echo ✗ ProcessInactiveMemberDeletion.php)

copy "%SOURCE%\app\Http\Controllers\AutoDeletionController.php" "%DEST%\app\Http\Controllers\" >nul 2>&1
if %errorlevel%==0 (echo ✓ AutoDeletionController.php) else (echo ✗ AutoDeletionController.php)

copy "%SOURCE%\app\Http\Controllers\MemberReactivationController.php" "%DEST%\app\Http\Controllers\" >nul 2>&1
if %errorlevel%==0 (echo ✓ MemberReactivationController.php) else (echo ✗ MemberReactivationController.php)

copy "%SOURCE%\app\Http\Middleware\TrackMemberActivity.php" "%DEST%\app\Http\Middleware\" >nul 2>&1
if %errorlevel%==0 (echo ✓ TrackMemberActivity.php) else (echo ✗ TrackMemberActivity.php)

copy "%SOURCE%\app\Models\AutoDeletionSettings.php" "%DEST%\app\Models\" >nul 2>&1
if %errorlevel%==0 (echo ✓ AutoDeletionSettings.php) else (echo ✗ AutoDeletionSettings.php)

copy "%SOURCE%\app\Models\MemberDeletionLog.php" "%DEST%\app\Models\" >nul 2>&1
if %errorlevel%==0 (echo ✓ MemberDeletionLog.php) else (echo ✗ MemberDeletionLog.php)

copy "%SOURCE%\app\Notifications\MemberDeletionWarning.php" "%DEST%\app\Notifications\" >nul 2>&1
if %errorlevel%==0 (echo ✓ MemberDeletionWarning.php) else (echo ✗ MemberDeletionWarning.php)

copy "%SOURCE%\app\Notifications\MemberFinalDeletionWarning.php" "%DEST%\app\Notifications\" >nul 2>&1
if %errorlevel%==0 (echo ✓ MemberFinalDeletionWarning.php) else (echo ✗ MemberFinalDeletionWarning.php)

REM Migrations
copy "%SOURCE%\database\migrations\2025_01_07_000002_add_activity_tracking_to_members_table.php" "%DEST%\database\migrations\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Migration: add_activity_tracking) else (echo ✗ Migration: add_activity_tracking)

copy "%SOURCE%\database\migrations\2025_01_07_000003_create_member_deletion_logs_table.php" "%DEST%\database\migrations\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Migration: member_deletion_logs) else (echo ✗ Migration: member_deletion_logs)

copy "%SOURCE%\database\migrations\2025_01_07_000004_create_auto_deletion_settings_table.php" "%DEST%\database\migrations\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Migration: auto_deletion_settings) else (echo ✗ Migration: auto_deletion_settings)

REM Views
copy "%SOURCE%\resources\views\admin\auto-deletion\index.blade.php" "%DEST%\resources\views\admin\auto-deletion\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Admin view: index.blade.php) else (echo ✗ Admin view: index.blade.php)

copy "%SOURCE%\resources\views\admin\auto-deletion\logs.blade.php" "%DEST%\resources\views\admin\auto-deletion\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Admin view: logs.blade.php) else (echo ✗ Admin view: logs.blade.php)

copy "%SOURCE%\resources\views\member\reactivation\form.blade.php" "%DEST%\resources\views\member\reactivation\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Reactivation view: form.blade.php) else (echo ✗ Reactivation view: form.blade.php)

copy "%SOURCE%\resources\views\member\reactivation\success.blade.php" "%DEST%\resources\views\member\reactivation\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Reactivation view: success.blade.php) else (echo ✗ Reactivation view: success.blade.php)

REM Updated files
copy "%SOURCE%\app\Models\Member.php" "%DEST%\app\Models\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Updated: Member.php) else (echo ✗ Updated: Member.php)

copy "%SOURCE%\app\Http\Controllers\AuthController.php" "%DEST%\app\Http\Controllers\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Updated: AuthController.php) else (echo ✗ Updated: AuthController.php)

copy "%SOURCE%\app\Http\Controllers\RfidController.php" "%DEST%\app\Http\Controllers\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Updated: RfidController.php) else (echo ✗ Updated: RfidController.php)

copy "%SOURCE%\routes\web.php" "%DEST%\routes\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Updated: web.php) else (echo ✗ Updated: web.php)

copy "%SOURCE%\bootstrap\app.php" "%DEST%\bootstrap\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Updated: app.php) else (echo ✗ Updated: app.php)

copy "%SOURCE%\app\Console\Kernel.php" "%DEST%\app\Console\" >nul 2>&1
if %errorlevel%==0 (echo ✓ Updated: Kernel.php) else (echo ✗ Updated: Kernel.php)

echo.
echo ========================================
echo   COPY OPERATION COMPLETE
echo ========================================
echo.
echo Files have been copied to: %DEST%
echo.
echo NEXT STEPS:
echo 1. Upload all files from '%DEST%' to your VPS (156.67.221.184)
echo 2. Maintain the directory structure when uploading
echo 3. Run: php artisan migrate
echo 4. Test: php artisan members:process-inactive-deletion --dry-run --force -v
echo 5. Access: http://156.67.221.184/auto-deletion
echo.
pause
