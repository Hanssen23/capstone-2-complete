# Auto-Deletion System VPS Upload Script
param(
    [string]$VpsHost = "156.67.221.184",
    [string]$Username = "",
    [string]$RemotePath = "",
    [string]$Method = "scp"
)

Write-Host "========================================" -ForegroundColor Green
Write-Host "  AUTO-DELETION SYSTEM VPS UPLOAD" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Check if auto_deletion_deployment folder exists
if (!(Test-Path "auto_deletion_deployment")) {
    Write-Host "ERROR: auto_deletion_deployment folder not found!" -ForegroundColor Red
    Write-Host "Please run the copy_files_simple.bat script first." -ForegroundColor Yellow
    exit 1
}

# Get credentials if not provided
if ([string]::IsNullOrEmpty($Username)) {
    $Username = Read-Host "Enter your VPS username"
}

if ([string]::IsNullOrEmpty($RemotePath)) {
    $RemotePath = Read-Host "Enter Laravel project path on VPS (e.g., /var/www/html/silencio-gym)"
}

Write-Host "Upload Details:" -ForegroundColor Yellow
Write-Host "Host: $VpsHost" -ForegroundColor White
Write-Host "Username: $Username" -ForegroundColor White
Write-Host "Remote Path: $RemotePath" -ForegroundColor White
Write-Host "Method: $Method" -ForegroundColor White
Write-Host ""

$confirm = Read-Host "Proceed with upload? (y/n)"
if ($confirm -ne "y" -and $confirm -ne "Y") {
    Write-Host "Upload cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host "Starting upload..." -ForegroundColor Green
Write-Host ""

try {
    switch ($Method.ToLower()) {
        "scp" {
            Write-Host "Using SCP to upload files..." -ForegroundColor Yellow
            
            # Upload each directory separately to maintain structure
            $directories = @(
                "app/Console/Commands",
                "app/Http/Controllers", 
                "app/Http/Middleware",
                "app/Models",
                "app/Notifications",
                "app/Console",
                "database/migrations",
                "resources/views/admin/auto-deletion",
                "resources/views/member/reactivation",
                "routes",
                "bootstrap"
            )
            
            foreach ($dir in $directories) {
                $localPath = "auto_deletion_deployment/$dir"
                $remotePath = "$RemotePath/$dir"
                
                if (Test-Path $localPath) {
                    Write-Host "Uploading $dir..." -ForegroundColor Cyan
                    $scpCommand = "scp -r `"$localPath/*`" $Username@${VpsHost}:$remotePath/"
                    Invoke-Expression $scpCommand
                    
                    if ($LASTEXITCODE -eq 0) {
                        Write-Host "✓ $dir uploaded successfully" -ForegroundColor Green
                    } else {
                        Write-Host "✗ Failed to upload $dir" -ForegroundColor Red
                    }
                }
            }
        }
        
        "rsync" {
            Write-Host "Using rsync to upload files..." -ForegroundColor Yellow
            $rsyncCommand = "rsync -avz auto_deletion_deployment/ $Username@${VpsHost}:$RemotePath/"
            Invoke-Expression $rsyncCommand
        }
        
        default {
            Write-Host "Unsupported upload method: $Method" -ForegroundColor Red
            exit 1
        }
    }
    
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  UPLOAD COMPLETED" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""
    
    Write-Host "Next steps:" -ForegroundColor Yellow
    Write-Host "1. SSH into your VPS: ssh $Username@$VpsHost" -ForegroundColor White
    Write-Host "2. Navigate to project: cd $RemotePath" -ForegroundColor White
    Write-Host "3. Run migrations: php artisan migrate" -ForegroundColor White
    Write-Host "4. Clear caches: php artisan config:clear && php artisan route:clear" -ForegroundColor White
    Write-Host "5. Test system: php artisan members:process-inactive-deletion --dry-run --force -v" -ForegroundColor White
    Write-Host "6. Visit admin panel: http://156.67.221.184/auto-deletion" -ForegroundColor White
    Write-Host ""
    
} catch {
    Write-Host "Upload failed with error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative upload methods:" -ForegroundColor Yellow
    Write-Host "1. Use FileZilla or WinSCP GUI" -ForegroundColor White
    Write-Host "2. Use built-in Windows SFTP: sftp $Username@$VpsHost" -ForegroundColor White
    Write-Host "3. Upload files manually via web hosting control panel" -ForegroundColor White
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
