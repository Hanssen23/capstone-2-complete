@echo off
echo ========================================
echo   AUTO-DELETION SYSTEM DEPLOYMENT
echo ========================================
echo.
echo This script will deploy the auto-deletion system to your VPS
echo Server: 156.67.221.184
echo.

REM Check if we're in the correct directory
if not exist "silencio-gym-mms-main" (
    echo ERROR: Please run this script from the directory containing silencio-gym-mms-main folder
    pause
    exit /b 1
)

echo Step 1: Preparing files for deployment...
echo.

REM Create deployment directory
if not exist "auto_deletion_deployment" mkdir auto_deletion_deployment
cd auto_deletion_deployment

REM Create directory structure
mkdir app\Console\Commands 2>nul
mkdir app\Http\Controllers 2>nul
mkdir app\Http\Middleware 2>nul
mkdir app\Models 2>nul
mkdir app\Notifications 2>nul
mkdir database\migrations 2>nul
mkdir resources\views\admin\auto-deletion 2>nul
mkdir resources\views\member\reactivation 2>nul

echo Step 2: Copying auto-deletion files...

REM Copy new files
copy "..\silencio-gym-mms-main\app\Console\Commands\ProcessInactiveMemberDeletion.php" "app\Console\Commands\" >nul
copy "..\silencio-gym-mms-main\app\Http\Controllers\AutoDeletionController.php" "app\Http\Controllers\" >nul
copy "..\silencio-gym-mms-main\app\Http\Controllers\MemberReactivationController.php" "app\Http\Controllers\" >nul
copy "..\silencio-gym-mms-main\app\Http\Middleware\TrackMemberActivity.php" "app\Http\Middleware\" >nul
copy "..\silencio-gym-mms-main\app\Models\AutoDeletionSettings.php" "app\Models\" >nul
copy "..\silencio-gym-mms-main\app\Models\MemberDeletionLog.php" "app\Models\" >nul
copy "..\silencio-gym-mms-main\app\Notifications\MemberDeletionWarning.php" "app\Notifications\" >nul
copy "..\silencio-gym-mms-main\app\Notifications\MemberFinalDeletionWarning.php" "app\Notifications\" >nul

REM Copy migrations
copy "..\silencio-gym-mms-main\database\migrations\2025_01_07_000002_add_activity_tracking_to_members_table.php" "database\migrations\" >nul
copy "..\silencio-gym-mms-main\database\migrations\2025_01_07_000003_create_member_deletion_logs_table.php" "database\migrations\" >nul
copy "..\silencio-gym-mms-main\database\migrations\2025_01_07_000004_create_auto_deletion_settings_table.php" "database\migrations\" >nul

REM Copy views
copy "..\silencio-gym-mms-main\resources\views\admin\auto-deletion\index.blade.php" "resources\views\admin\auto-deletion\" >nul
copy "..\silencio-gym-mms-main\resources\views\admin\auto-deletion\logs.blade.php" "resources\views\admin\auto-deletion\" >nul
copy "..\silencio-gym-mms-main\resources\views\member\reactivation\form.blade.php" "resources\views\member\reactivation\" >nul
copy "..\silencio-gym-mms-main\resources\views\member\reactivation\success.blade.php" "resources\views\member\reactivation\" >nul

REM Copy updated files
copy "..\silencio-gym-mms-main\app\Models\Member.php" "app\Models\" >nul
copy "..\silencio-gym-mms-main\app\Http\Controllers\AuthController.php" "app\Http\Controllers\" >nul
copy "..\silencio-gym-mms-main\app\Http\Controllers\RfidController.php" "app\Http\Controllers\" >nul
copy "..\silencio-gym-mms-main\routes\web.php" "routes\" >nul
copy "..\silencio-gym-mms-main\bootstrap\app.php" "bootstrap\" >nul
copy "..\silencio-gym-mms-main\app\Console\Kernel.php" "app\Console\" >nul

echo Step 3: Creating deployment instructions...

REM Create deployment instructions
echo # AUTO-DELETION SYSTEM DEPLOYMENT INSTRUCTIONS > DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo ## Files to Upload to VPS (156.67.221.184) >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo Upload all files in this directory to your VPS, maintaining the directory structure. >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo ## Commands to Run on VPS >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo ```bash >> DEPLOYMENT_INSTRUCTIONS.md
echo # 1. Navigate to your Laravel project directory >> DEPLOYMENT_INSTRUCTIONS.md
echo cd /path/to/your/laravel/project >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo # 2. Run migrations >> DEPLOYMENT_INSTRUCTIONS.md
echo php artisan migrate >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo # 3. Test the auto-deletion command >> DEPLOYMENT_INSTRUCTIONS.md
echo php artisan members:process-inactive-deletion --dry-run --force -v >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo # 4. Clear cache >> DEPLOYMENT_INSTRUCTIONS.md
echo php artisan config:clear >> DEPLOYMENT_INSTRUCTIONS.md
echo php artisan route:clear >> DEPLOYMENT_INSTRUCTIONS.md
echo php artisan view:clear >> DEPLOYMENT_INSTRUCTIONS.md
echo. >> DEPLOYMENT_INSTRUCTIONS.md
echo # 5. Test the admin panel >> DEPLOYMENT_INSTRUCTIONS.md
echo # Visit: http://156.67.221.184/auto-deletion >> DEPLOYMENT_INSTRUCTIONS.md
echo ``` >> DEPLOYMENT_INSTRUCTIONS.md

REM Create file list
echo # FILES INCLUDED IN THIS DEPLOYMENT > FILE_LIST.md
echo. >> FILE_LIST.md
echo ## New Files: >> FILE_LIST.md
dir /s /b app\Console\Commands\*.php >> FILE_LIST.md
dir /s /b app\Http\Controllers\AutoDeletionController.php >> FILE_LIST.md
dir /s /b app\Http\Controllers\MemberReactivationController.php >> FILE_LIST.md
dir /s /b app\Http\Middleware\TrackMemberActivity.php >> FILE_LIST.md
dir /s /b app\Models\AutoDeletionSettings.php >> FILE_LIST.md
dir /s /b app\Models\MemberDeletionLog.php >> FILE_LIST.md
dir /s /b app\Notifications\*.php >> FILE_LIST.md
dir /s /b database\migrations\2025_01_07_*.php >> FILE_LIST.md
dir /s /b resources\views\admin\auto-deletion\*.php >> FILE_LIST.md
dir /s /b resources\views\member\reactivation\*.php >> FILE_LIST.md
echo. >> FILE_LIST.md
echo ## Updated Files: >> FILE_LIST.md
echo app\Models\Member.php >> FILE_LIST.md
echo app\Http\Controllers\AuthController.php >> FILE_LIST.md
echo app\Http\Controllers\RfidController.php >> FILE_LIST.md
echo routes\web.php >> FILE_LIST.md
echo bootstrap\app.php >> FILE_LIST.md
echo app\Console\Kernel.php >> FILE_LIST.md

cd ..

echo.
echo ========================================
echo   DEPLOYMENT PREPARATION COMPLETE!
echo ========================================
echo.
echo Files have been prepared in the 'auto_deletion_deployment' directory.
echo.
echo NEXT STEPS:
echo 1. Upload all files from 'auto_deletion_deployment' to your VPS
echo 2. Maintain the directory structure when uploading
echo 3. Run the commands listed in DEPLOYMENT_INSTRUCTIONS.md
echo.
echo The admin panel will be available at:
echo http://156.67.221.184/auto-deletion
echo.
pause
