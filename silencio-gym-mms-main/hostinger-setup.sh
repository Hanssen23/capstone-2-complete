#!/bin/bash

# Hostinger Setup Script for Silencio Gym Management System
# Run this script after uploading files to your Hostinger server

echo "🚀 Setting up Silencio Gym Management System on Hostinger..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

echo "✅ Laravel application detected"

# Set proper file permissions
echo "🔧 Setting file permissions..."

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set specific permissions for Laravel
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/

# Secure .env file
if [ -f ".env" ]; then
    chmod 600 .env
    echo "✅ .env file secured"
else
    echo "⚠️  Warning: .env file not found. Please create it manually."
fi

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found. Please create it from hostinger.env.example"
    exit 1
fi

# Generate application key if not set
echo "🔑 Generating application key..."
php artisan key:generate --force

# Clear and cache configuration
echo "⚡ Optimizing application..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Optimize autoloader
echo "📦 Optimizing autoloader..."
composer dump-autoload --optimize

# Create storage symlink if needed
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

# Set final permissions
echo "🔒 Setting final security permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 600 .env

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Test your website at https://yourdomain.com"
echo "2. Login with admin credentials: admin@admin.com / admin123"
echo "3. Configure RFID system if needed"
echo "4. Set up email notifications"
echo "5. Review security settings"
echo ""
echo "🔧 If you encounter issues:"
echo "- Check error logs in Hostinger control panel"
echo "- Verify database credentials in .env file"
echo "- Ensure all file permissions are correct"
echo "- Contact support if needed"
echo ""
echo "📚 For detailed instructions, see HOSTINGER_DEPLOYMENT_GUIDE.md"
