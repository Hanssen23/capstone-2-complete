#!/bin/bash

# Deployment script for VPS fixes
# This script deploys all the fixes to the VPS at 156.67.221.184

set -e  # Exit on error

VPS_HOST="156.67.221.184"
VPS_USER="root"
VPS_PATH="/var/www/silencio-gym"

echo "========================================="
echo "Deploying Fixes to VPS"
echo "========================================="
echo ""
echo "VPS Host: $VPS_HOST"
echo "VPS Path: $VPS_PATH"
echo ""

# Check if we can connect to VPS
echo "Testing VPS connection..."
if ! ssh -o ConnectTimeout=5 $VPS_USER@$VPS_HOST "echo 'Connection successful'"; then
    echo "❌ Error: Cannot connect to VPS. Please check your SSH connection."
    exit 1
fi
echo "✅ VPS connection successful"
echo ""

# Upload AuthController
echo "📤 Uploading AuthController.php..."
scp app/Http/Controllers/AuthController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
echo "✅ AuthController.php uploaded"

# Upload MemberAuthController
echo "📤 Uploading MemberAuthController.php..."
scp app/Http/Controllers/MemberAuthController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
echo "✅ MemberAuthController.php uploaded"

# Upload MembershipController
echo "📤 Uploading MembershipController.php..."
scp app/Http/Controllers/MembershipController.php $VPS_USER@$VPS_HOST:$VPS_PATH/app/Http/Controllers/
echo "✅ MembershipController.php uploaded"

# Upload UID pool seeder
echo "📤 Uploading seed_uid_pool.php..."
scp seed_uid_pool.php $VPS_USER@$VPS_HOST:$VPS_PATH/
echo "✅ seed_uid_pool.php uploaded"

echo ""
echo "========================================="
echo "Running Post-Deployment Tasks"
echo "========================================="
echo ""

# Run UID pool seeder
echo "🌱 Seeding UID pool..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php seed_uid_pool.php"
echo ""

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php artisan cache:clear"
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php artisan config:clear"
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php artisan route:clear"
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && php artisan view:clear"
echo "✅ Caches cleared"

# Set proper permissions
echo "🔒 Setting proper permissions..."
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && chmod -R 755 storage bootstrap/cache"
ssh $VPS_USER@$VPS_HOST "cd $VPS_PATH && chown -R www-data:www-data storage bootstrap/cache"
echo "✅ Permissions set"

echo ""
echo "========================================="
echo "Deployment Complete!"
echo "========================================="
echo ""
echo "✅ All fixes have been deployed successfully!"
echo ""
echo "Please test the following:"
echo "  1. Employee logout (should not show 500 error)"
echo "  2. Member registration (should work with valid data)"
echo "  3. Payment confirmation (should process successfully)"
echo ""
echo "For detailed testing instructions, see DEPLOYMENT_FIXES.md"
echo ""

