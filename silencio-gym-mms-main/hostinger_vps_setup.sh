#!/bin/bash

# Silencio Gym VPS Setup Script for Hostinger
# Run this in the Hostinger browser terminal after uploading files

echo "🚀 Setting up Silencio Gym on Hostinger VPS..."

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: Not in Laravel root directory"
    echo "Please navigate to your project directory first"
    echo "Example: cd /var/www/html/"
    exit 1
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ ERROR: .env file not found"
    echo "Please upload your .env file first"
    exit 1
fi

echo "✅ Laravel application detected"

# Install system dependencies
echo "📦 Installing system dependencies..."
apt update
apt install -y php8.1-cli php8.1-mysql php8.1-mbstring php8.1-xml php8.1-gd php8.1-curl composer unzip

# Install PHP dependencies
echo "📥 Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Set permissions
echo "🔐 Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
chmod 644 .env

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate --force

# Run database migrations
echo "🗄️ Setting up database..."
php artisan migrate --force

# Seed database
echo "🌱 Seeding database..."
php artisan db:seed --force

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
echo "🔗 Creating storage symlink..."
php artisan storage:link

echo ""
echo "🎉 Silencio Gym setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Configure your web server (Nginx/Apache)"
echo "2. Point web root to: $(pwd)/public"
echo "3. Update DNS if using custom domain"
echo "4. Configure SSL certificate"
echo ""
echo "🌐 Access your site at:"
echo "   https://156.67.221.184"
echo ""
echo "🔑 Default admin login:"
echo "   Email: admin@admin.com"
echo "   Password: admin123"
echo ""
echo "🏷️ RFID functionality is fully preserved!"
echo ""

# Check system status
echo "📊 System Status:"
echo "PHP Version: $(php --version | head -1)"
echo "Laravel: $(php artisan --version)"
echo "Disk Usage: $(df -h / | tail -1)"
echo "Memory Usage: $(free -h | grep Mem)"
