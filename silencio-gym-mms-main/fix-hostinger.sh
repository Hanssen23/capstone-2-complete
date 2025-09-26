#!/bin/bash

echo "🔧 Fixing Hostinger deployment issues..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the Laravel root directory."
    exit 1
fi

echo "✅ Laravel application detected"

# Fix file permissions
echo "🔧 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
chmod 600 .env

# Clear all caches
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Generate application key if not set
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Cache configuration for production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
echo "📦 Optimizing autoloader..."
composer dump-autoload --optimize

# Create storage symlink if needed
if [ ! -L "public/storage" ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link
fi

echo ""
echo "🎉 Fix completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Test your website at https://yourdomain.com"
echo "2. Check if CSS is loading properly"
echo "3. Test login functionality"
echo "4. Verify database operations"
echo ""
echo "🔧 If issues persist:"
echo "- Check error logs in Hostinger control panel"
echo "- Verify database credentials in .env file"
echo "- Ensure all files were uploaded correctly"
echo "- Contact support if needed"
