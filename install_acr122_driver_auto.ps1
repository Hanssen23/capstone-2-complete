# ACR122U Automatic Driver Installation
# Run as Administrator

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   ACR122U Automatic Driver Install" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Right-click this file and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    pause
    exit
}

# Driver path
$driverPath = "C:\Users\hanss\Documents\ACS-Unified-Driver-Win-4280"

if (-not (Test-Path $driverPath)) {
    Write-Host "ERROR: Driver folder not found at:" -ForegroundColor Red
    Write-Host $driverPath -ForegroundColor Yellow
    Write-Host ""
    pause
    exit
}

Write-Host "Driver folder found: $driverPath" -ForegroundColor Green
Write-Host ""

# Find ACR122 device
Write-Host "Looking for ACR122 device..." -ForegroundColor Cyan
$devices = Get-PnpDevice | Where-Object {$_.FriendlyName -like "*ACR122*"}

if ($devices.Count -eq 0) {
    Write-Host "ERROR: ACR122 device not found!" -ForegroundColor Red
    Write-Host "Please make sure the ACR122U reader is connected via USB." -ForegroundColor Yellow
    Write-Host ""
    pause
    exit
}

Write-Host "Found ACR122 device(s):" -ForegroundColor Green
$devices | ForEach-Object {
    Write-Host "  - $($_.FriendlyName) (Status: $($_.Status))" -ForegroundColor Yellow
}
Write-Host ""

# Try to update driver
Write-Host "Attempting to install driver..." -ForegroundColor Cyan
Write-Host "This may take a minute..." -ForegroundColor Yellow
Write-Host ""

try {
    # Use pnputil to add driver
    Write-Host "Adding driver to Windows driver store..." -ForegroundColor Cyan
    $infFiles = Get-ChildItem -Path $driverPath -Filter "*.inf" -Recurse
    
    if ($infFiles.Count -eq 0) {
        Write-Host "ERROR: No .inf files found in driver folder!" -ForegroundColor Red
        Write-Host ""
        pause
        exit
    }
    
    Write-Host "Found $($infFiles.Count) driver file(s)" -ForegroundColor Green
    
    foreach ($inf in $infFiles) {
        Write-Host "Installing: $($inf.Name)" -ForegroundColor Cyan
        $result = pnputil /add-driver $inf.FullName /install
        Write-Host $result -ForegroundColor Gray
    }
    
    Write-Host ""
    Write-Host "Driver installation completed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Please unplug and replug the ACR122U reader." -ForegroundColor Yellow
    Write-Host ""
    
} catch {
    Write-Host "ERROR: Driver installation failed!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    Write-Host "Please try manual installation:" -ForegroundColor Yellow
    Write-Host "1. Open Device Manager (devmgmt.msc)" -ForegroundColor Yellow
    Write-Host "2. Find 'ACR122 Smart Card Reader'" -ForegroundColor Yellow
    Write-Host "3. Right-click -> Update driver" -ForegroundColor Yellow
    Write-Host "4. Browse to: $driverPath" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Next step: Test the RFID reader" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Run this command to test:" -ForegroundColor Yellow
Write-Host "python silencio-gym-mms-main/debug_rfid_reader.py" -ForegroundColor Green
Write-Host ""
pause

