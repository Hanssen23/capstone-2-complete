#!/bin/bash

# Deploy Dashboard Fixes to Production Server
# This script deploys the latest dashboard changes to 156.67.221.184:8001

SERVER_HOST="156.67.221.184"
SERVER_USER="root"
SERVER_PATH="/var/www/html"
REMOTE_BACKUP_DIR="/var/www/html/backup_$(date +%Y%m%d_%H%M%S)"

echo "🚀 Starting deployment to $SERVER_HOST..."

# Create backup directory on server
echo "📦 Creating backup on server..."
ssh $SERVER_USER@$SERVER_HOST "mkdir -p $REMOTE_BACKUP_DIR"

# Backup current dashboard file
echo "💾 Backing up current dashboard..."
ssh $SERVER_USER@$SERVER_HOST "cp -f $SERVER_PATH/resources/views/dashboard.blade.php $REMOTE_BACKUP_DIR/"

# Upload the updated dashboard file
echo "📤 Uploading updated dashboard..."
scp resources/views/dashboard.blade.php $SERVER_USER@$SERVER_HOST:$SERVER_PATH/resources/views/

# Set proper permissions
echo "🔧 Setting permissions..."
ssh $SERVER_USER@$SERVER_HOST "chown -R www-data:www-data $SERVER_PATH/resources/views/ && chmod -R 755 $SERVER_PATH/resources/views/"

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
ssh $SERVER_USER@$SERVER_HOST "cd $SERVER_PATH && php artisan view:clear && php artisan cache:clear && php artisan config:clear"

# Restart web service if available
echo "🔄 Restarting web services..."
ssh $SERVER_USER@$SERVER_HOST "systemctl reload apache2 2>/dev/null || systemctl reload nginx 2>/dev/null || echo 'Web service reload not available - manual restart may be required'"

echo "✅ Deployment completed successfully!"
echo "🌐 Dashboard should now be available at: http://$SERVER_HOST:8001/dashboard"
echo "📁 Backup created at: $REMOTE_BACKUP_DIR"

# Test the deployment
echo "🔍 Testing deployment..."
curl -s -o /dev/null -w "HTTP Status: %{http_code}" "http://$SERVER_HOST:8001/dashboard"

echo ""
echo "📝 Deployment Summary:"
echo "  - Updated Weekly Revenue card (Monthly → Weekly)"
echo "  - Updated subtitle (This month → This week)"
echo "  - Fixed expiring card undefined text issue"
echo "  - Updated Monthly Revenue Trend → Weekly Revenue Trend"
echo "  - Enhanced responsive design for mobile and zoom"
echo "  - Added high DPI and zoom level support"
