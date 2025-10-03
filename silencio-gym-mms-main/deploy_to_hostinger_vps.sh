#!/bin/bash

# Deploy Silencio Gym to Hostinger VPS
# This script prepares files for VPS deployment

echo "🚀 Preparing Silencio Gym for Hostinger VPS deployment..."

# Check required files
if [ ! -f "artisan" ]; then
    echo "❌ ERROR: Not in Laravel root directory"
    exit 1
fi

echo "✅ Laravel application found"

# Create deployment package
echo "📦 Creating deployment package..."

# Install production dependencies
echo "📥 Installing production dependencies..."
composer install --optimize-autoloader --no-dev

# Build frontend assets
echo "🎨 Building frontend assets..."
npm install
npm run build

# Create .env for production
echo "🔧 Creating production .env file..."
cp hostinger.env.example .env.api

# Update the .env with VPS settings
cat > .env.api << 'EOF'
APP_NAME="Silencio Gym Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://156.67.221.184

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration for VPS
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=silencio_gym
DB_USERNAME=silencio_user
DB_PASSWORD=your_db_password_here

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache Configuration
CACHE_DRIVER=database
CACHE_PREFIX=

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@silencio-gym.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@silencio-gym.com
MAIL_FROM_NAME="${APP_NAME}"

# RFID Configuration (PRESERVED)
RFID_DEVICE_ID=main_reader
RFID_API_URL=https://156.67.221.184/api/rfid

# Security Settings
BCRYPT_ROUNDS=12
TELESCOPE_ENABLED=false
EOF

echo "✅ Production .env created"

# Clear caches
echo "🧹 Clearing development caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Create deployment list
echo "📋 Creating deployment checklist..."
cat > DEPLOYMENT_CHECKLIST.md << 'EOF'
# Hostinger VPS Deployment Checklist

## SSH Access to VPS
```bash
ssh root@156.67.221.184
Password: [your_vps_password]
```

## Files to Upload to VPS
Upload everything to: /var/www/html/

Required folders/files:
- /app/
- /bootstrap/
- /config/
- /database/
- /public/
- /resources/
- /routes/
- /storage/
- /vendor/
- artisan
- composer.json
- composer.lock
- package.json
- README.md
- .env.vps

## Commands to run on VPS:
```bash
# 1. Navigate to project directory
cd /var/www/html/

# 2. Set up MySQL database (if needed)
mysql -u root -p
CREATE DATABASE silencio_gym;
CREATE USER 'silencio_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON silencio_gym.* TO 'silencio_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# 3. Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/

# 4. Generate app key
php artisan key:generate

# 5. Run migrations
php artisan migrate --force

# 6. Seed database
php artisan db:seed --force

# 7. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Install PHP dependencies (if needed)
composer install --optimize-autoloader --no-dev

# 9. Enable Nginx/Apache to serve the site
# After uploading files, configure web server to point to /var/www/html/public
```

Your RFID functionality will remain fully intact!
EOF

echo "✅ Deployment checklist created"

# Create package for upload
echo "📦 Creating upload package..."
tar --exclude='node_modules' --exclude='.git' --exclude='storage/logs/*' --exclude='vendor' -czf silencio-gym-vps.tar.gz . 

echo ""
echo "🎉 DEPLOYMENT PREP COMPLETE!"
echo ""
echo "📁 Upload Package: silencio-gym-vps.tar.gz"
echo "🔧 Environment file: .env.api"
echo "📋 Instructions: DEPLOYMENT_CHECKLIST.md"
echo ""
echo "📤 Next steps:"
echo "1. Upload silencio-gym-vps.tar.gz to your VPS"
echo "2. Extract it in /var/www/html/"
echo "3. Follow DEPLOYMENT_CHECKLIST.md"
echo "4. Your site will be available at: https://156.67.221.184"
echo ""
