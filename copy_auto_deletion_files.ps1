# Auto-Deletion System File Copy Script
Write-Host "========================================" -ForegroundColor Green
Write-Host "  COPYING AUTO-DELETION SYSTEM FILES" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

$sourceDir = "silencio-gym-mms-main"
$destDir = "auto_deletion_deployment"

# Ensure destination directories exist
$directories = @(
    "$destDir\app\Console\Commands",
    "$destDir\app\Http\Controllers",
    "$destDir\app\Http\Middleware",
    "$destDir\app\Models",
    "$destDir\app\Notifications",
    "$destDir\database\migrations",
    "$destDir\resources\views\admin\auto-deletion",
    "$destDir\resources\views\member\reactivation",
    "$destDir\routes",
    "$destDir\bootstrap",
    "$destDir\app\Console"
)

foreach ($dir in $directories) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "Created directory: $dir" -ForegroundColor Yellow
    }
}

# Define files to copy
$filesToCopy = @(
    @{Source="$sourceDir\app\Console\Commands\ProcessInactiveMemberDeletion.php"; Dest="$destDir\app\Console\Commands\ProcessInactiveMemberDeletion.php"},
    @{Source="$sourceDir\app\Http\Controllers\AutoDeletionController.php"; Dest="$destDir\app\Http\Controllers\AutoDeletionController.php"},
    @{Source="$sourceDir\app\Http\Controllers\MemberReactivationController.php"; Dest="$destDir\app\Http\Controllers\MemberReactivationController.php"},
    @{Source="$sourceDir\app\Http\Middleware\TrackMemberActivity.php"; Dest="$destDir\app\Http\Middleware\TrackMemberActivity.php"},
    @{Source="$sourceDir\app\Models\AutoDeletionSettings.php"; Dest="$destDir\app\Models\AutoDeletionSettings.php"},
    @{Source="$sourceDir\app\Models\MemberDeletionLog.php"; Dest="$destDir\app\Models\MemberDeletionLog.php"},
    @{Source="$sourceDir\app\Notifications\MemberDeletionWarning.php"; Dest="$destDir\app\Notifications\MemberDeletionWarning.php"},
    @{Source="$sourceDir\app\Notifications\MemberFinalDeletionWarning.php"; Dest="$destDir\app\Notifications\MemberFinalDeletionWarning.php"},
    @{Source="$sourceDir\database\migrations\2025_01_07_000002_add_activity_tracking_to_members_table.php"; Dest="$destDir\database\migrations\2025_01_07_000002_add_activity_tracking_to_members_table.php"},
    @{Source="$sourceDir\database\migrations\2025_01_07_000003_create_member_deletion_logs_table.php"; Dest="$destDir\database\migrations\2025_01_07_000003_create_member_deletion_logs_table.php"},
    @{Source="$sourceDir\database\migrations\2025_01_07_000004_create_auto_deletion_settings_table.php"; Dest="$destDir\database\migrations\2025_01_07_000004_create_auto_deletion_settings_table.php"},
    @{Source="$sourceDir\resources\views\admin\auto-deletion\index.blade.php"; Dest="$destDir\resources\views\admin\auto-deletion\index.blade.php"},
    @{Source="$sourceDir\resources\views\admin\auto-deletion\logs.blade.php"; Dest="$destDir\resources\views\admin\auto-deletion\logs.blade.php"},
    @{Source="$sourceDir\resources\views\member\reactivation\form.blade.php"; Dest="$destDir\resources\views\member\reactivation\form.blade.php"},
    @{Source="$sourceDir\resources\views\member\reactivation\success.blade.php"; Dest="$destDir\resources\views\member\reactivation\success.blade.php"},
    @{Source="$sourceDir\app\Models\Member.php"; Dest="$destDir\app\Models\Member.php"},
    @{Source="$sourceDir\app\Http\Controllers\AuthController.php"; Dest="$destDir\app\Http\Controllers\AuthController.php"},
    @{Source="$sourceDir\app\Http\Controllers\RfidController.php"; Dest="$destDir\app\Http\Controllers\RfidController.php"},
    @{Source="$sourceDir\routes\web.php"; Dest="$destDir\routes\web.php"},
    @{Source="$sourceDir\bootstrap\app.php"; Dest="$destDir\bootstrap\app.php"},
    @{Source="$sourceDir\app\Console\Kernel.php"; Dest="$destDir\app\Console\Kernel.php"}
)

# Copy files
$copiedCount = 0
$errorCount = 0

foreach ($file in $filesToCopy) {
    if (Test-Path $file.Source) {
        try {
            Copy-Item $file.Source $file.Dest -Force
            Write-Host "✓ Copied: $($file.Source)" -ForegroundColor Green
            $copiedCount++
        }
        catch {
            Write-Host "✗ Failed to copy: $($file.Source)" -ForegroundColor Red
            Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Red
            $errorCount++
        }
    }
    else {
        Write-Host "✗ Source file not found: $($file.Source)" -ForegroundColor Red
        $errorCount++
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  COPY OPERATION COMPLETE" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host "Files copied: $copiedCount" -ForegroundColor Green
Write-Host "Errors: $errorCount" -ForegroundColor $(if ($errorCount -eq 0) { "Green" } else { "Red" })
Write-Host ""

if ($errorCount -eq 0) {
    Write-Host "SUCCESS: All files copied successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "NEXT STEPS:" -ForegroundColor Yellow
    Write-Host "1. Upload the 'auto_deletion_deployment' folder contents to your VPS" -ForegroundColor White
    Write-Host "2. Maintain the directory structure when uploading" -ForegroundColor White
    Write-Host "3. Run migrations: php artisan migrate" -ForegroundColor White
    Write-Host "4. Test the system: php artisan members:process-inactive-deletion --dry-run --force -v" -ForegroundColor White
    Write-Host "5. Access admin panel: http://156.67.221.184/auto-deletion" -ForegroundColor White
}
else {
    Write-Host "WARNING: Some files failed to copy. Please check the errors above." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
