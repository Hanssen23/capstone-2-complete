# Deploy Dashboard Fixes to Production Server
# This script deploys the latest dashboard changes to 156.67.221.184:8001

$SERVER_HOST = "156.67.221.184"
$SERVER_USER = "root"
$SERVER_PATH = "/var/www/html"

Write-Host "🚀 Starting deployment to $SERVER_HOST..."

try {
    # Create backup directory on server
    Write-Host "📦 Creating backup on server..."
    ssh "$SERVER_USER@$SERVER_HOST" "mkdir -p /var/www/html/backup_$(date +%Y%m%d_%H%M%S)"

    # Backup current dashboard file
    Write-Host "💾 Backing up current dashboard..."
    $backupDir = ssh "$SERVER_USER@$SERVER_HOST" "mkdir -p /var/www/html/backup_$(date +%Y%m%d_%H%M%S) && echo /var/www/html/backup_$(date +%Y%m%d_%H%M%S)"
    ssh "$SERVER_USER@$SERVER_HOST" "cp -f \"$SERVER_NAME/resources/views/dashboard.blade.php\" \"$backupDir/\""

    # Upload the updated dashboard file
    Write-Host "📤 Uploading updated dashboard..."
    scp "resources/views/dashboard.blade.php" "$SERVER_USER@$SERVER_HOST`:$SERVER_PATH/resources/views/"

    # Set proper permissions
    Write-Host "🔧 Setting permissions..."
    ssh "$SERVER_USER@$SERVER_HOST" "chown -R www-data:www-data $SERVER_PATH/resources/views/ && chmod -R 755 $SERVER_PATH/resources/views/"

    # Clear Laravel caches
    Write-Host "🧹 Clearing Laravel caches..."
    ssh "$SERVER_USER@$SERVER_HOST" "cd $SERVER_PATH && php artisan view:clear && php artisan cache:clear && php artisan config:clear"

    # Restart web service if available
    Write-Host "🔄 Restarting web services..."
    ssh "$SERVER_USER@$SERVER_HOST" "systemctl reload apache2 2>/dev/null || systemctl reload nginx 2>/dev/null || echo 'Web service reload not available - manual restart may be required'"

    Write-Host "✅ Deployment completed successfully!"
    Write-Host "🌐 Dashboard should now be available at: http://$SERVER_HOST`:8001/dashboard"

    Write-Host ""
    Write-Host "📝 Deployment Summary:"
    Write-Host "  - Updated Weekly Revenue card (Monthly to Weekly)"
    Write-Host "  - Updated subtitle (This month to This week)"
    Write-Host "  - Fixed expiring card undefined text issue"
    Write-Host "  - Updated Monthly Revenue Trend to Weekly Revenue Trend"
    Write-Host "  - Enhanced responsive design for mobile and zoom"
    Write-Host "  - Added high DPI and zoom level support"

} catch {
    Write-Host "❌ Deployment failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}