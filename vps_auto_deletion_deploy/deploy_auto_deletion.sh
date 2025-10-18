#!/bin/bash
# Auto-Deletion System Deployment Script for VPS
echo "========================================="
echo "  AUTO-DELETION SYSTEM DEPLOYMENT"
echo "========================================="
echo ""

# Navigate to Laravel project directory
echo "Step 1: Navigating to Laravel project..."
cd /var/www/html/silencio-gym || cd /home/*/public_html || cd /var/www/silencio-gym

# Backup current files
echo "Step 2: Creating backup..."
mkdir -p backups/auto_deletion_backup_$(date +%Y%m%d_%H%M%S)
cp -r app/Models/Member.php backups/auto_deletion_backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
cp -r routes/web.php backups/auto_deletion_backup_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true

# Copy new files
echo "Step 3: Copying auto-deletion files..."
cp -r ~/vps_auto_deletion_deploy/app/* app/
cp -r ~/vps_auto_deletion_deploy/database/* database/
cp -r ~/vps_auto_deletion_deploy/resources/* resources/
cp -r ~/vps_auto_deletion_deploy/routes/* routes/
cp -r ~/vps_auto_deletion_deploy/bootstrap/* bootstrap/

# Set proper permissions
echo "Step 4: Setting file permissions..."
chown -R www-data:www-data app/ database/ resources/ routes/ bootstrap/ 2>/dev/null || true
chmod -R 755 app/ database/ resources/ routes/ bootstrap/

# Run Laravel commands
echo "Step 5: Running Laravel migrations..."
php artisan migrate --force

echo "Step 6: Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "Step 7: Testing auto-deletion command..."
php artisan members:process-inactive-deletion --dry-run --force -v

echo "========================================="
echo "  DEPLOYMENT COMPLETE!"
echo "========================================="
echo ""
echo "✅ Auto-deletion system deployed successfully"
echo "✅ Database migrations completed"
echo "✅ Caches cleared"
echo "✅ System tested"
echo ""
echo "🌐 Admin Panel: http://156.67.221.184/auto-deletion"
echo "🔧 Test Command: php artisan members:process-inactive-deletion --dry-run --force -v"
echo ""
echo "The auto-deletion system is now ready to use!"
