#!/bin/bash

# Simple Deployment Script for Silencio Gym Management System
# Bash version for better compatibility

VPS_IP="156.67.221.184"
VPS_USER="root"
APP_DIR="/var/www/silencio-gym"
DOMAIN="156.67.221.184"
REPO_URL="https://github.com/Hanssen23/Silencio.git"

echo "🚀 Starting deployment of Silencio Gym Management System to VPS"
echo "VPS IP: $VPS_IP"
echo "Target Directory: $APP_DIR"
echo ""

# Function to run commands on VPS
run_vps_command() {
    ssh -o StrictHostKeyChecking=no "$VPS_USER@$VPS_IP" "$1"
}

echo "📋 Step 1: Preparing VPS environment..."

# Update system and install required packages
run_vps_command "apt update"
run_vps_command "apt upgrade -y"
run_vps_command "apt install -y nginx php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-sqlite3 composer nodejs npm git unzip mysql-server"

echo "✅ VPS environment prepared"

echo "📋 Step 2: Setting up web server..."

# Configure Nginx
run_vps_command "cat > /etc/nginx/sites-available/silencio-gym << 'EOF'
server {
    listen 80;
    server_name $DOMAIN;
    root $APP_DIR/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF"

run_vps_command "ln -sf /etc/nginx/sites-available/silencio-gym /etc/nginx/sites-enabled/"
run_vps_command "rm -f /etc/nginx/sites-enabled/default"
run_vps_command "nginx -t"
run_vps_command "systemctl reload nginx"

echo "✅ Nginx configured"

echo "📋 Step 3: Setting up MySQL database..."

# Create database and user
run_vps_command "mysql -e \"CREATE DATABASE IF NOT EXISTS silencio_gym_db;\""
run_vps_command "mysql -e \"CREATE USER IF NOT EXISTS 'silencio_user'@'localhost' IDENTIFIED BY 'SilencioGym2024!';\""
run_vps_command "mysql -e \"GRANT ALL PRIVILEGES ON silencio_gym_db.* TO 'silencio_user'@'localhost';\""
run_vps_command "mysql -e \"FLUSH PRIVILEGES;\""

echo "✅ MySQL database created"

echo "📋 Step 4: Deploying application..."

# Create application directory and clone repository
run_vps_command "mkdir -p $APP_DIR"
run_vps_command "cd $APP_DIR && if [ -d '.git' ]; then git pull origin main; else git clone $REPO_URL .; fi"

echo "✅ Application code deployed"

echo "📋 Step 5: Installing dependencies..."

# Install PHP dependencies
run_vps_command "cd $APP_DIR && composer install --optimize-autoloader --no-dev"

# Install Node.js dependencies and build assets
run_vps_command "cd $APP_DIR && npm install"
run_vps_command "cd $APP_DIR && npm run build"

echo "✅ Dependencies installed"

echo "📋 Step 6: Configuring environment..."

# Create .env file
run_vps_command "cd $APP_DIR && cat > .env << 'EOF'
APP_NAME=\"Silencio Gym Management System\"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://$DOMAIN

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=silencio_gym_db
DB_USERNAME=silencio_user
DB_PASSWORD=SilencioGym2024!

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

CACHE_DRIVER=database
CACHE_PREFIX=

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@$DOMAIN
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@$DOMAIN
MAIL_FROM_NAME=\"Silencio Gym Management System\"

RFID_DEVICE_ID=main_reader
RFID_API_URL=http://$DOMAIN

BCRYPT_ROUNDS=12
EOF"

echo "✅ Environment configured"

echo "📋 Step 7: Setting up Laravel application..."

# Generate application key and run migrations
run_vps_command "cd $APP_DIR && php artisan key:generate --force"
run_vps_command "cd $APP_DIR && php artisan migrate --force"
run_vps_command "cd $APP_DIR && php artisan db:seed --force"
run_vps_command "cd $APP_DIR && php artisan config:cache"
run_vps_command "cd $APP_DIR && php artisan route:cache"
run_vps_command "cd $APP_DIR && php artisan view:cache"
run_vps_command "cd $APP_DIR && php artisan storage:link"

echo "✅ Laravel application configured"

echo "📋 Step 8: Setting file permissions..."

# Set proper permissions
run_vps_command "cd $APP_DIR && chown -R www-data:www-data ."
run_vps_command "cd $APP_DIR && chmod -R 755 storage/"
run_vps_command "cd $APP_DIR && chmod -R 755 bootstrap/cache/"
run_vps_command "cd $APP_DIR && chmod -R 755 public/"
run_vps_command "cd $APP_DIR && chmod 600 .env"

echo "✅ File permissions set"

echo "📋 Step 9: Setting up RFID system..."

# Install Python dependencies for RFID
run_vps_command "apt install -y python3 python3-pip"
run_vps_command "pip3 install pyscard requests"

echo "✅ RFID system prepared"

echo "📋 Step 10: Final optimizations..."

# Final optimizations
run_vps_command "cd $APP_DIR && composer dump-autoload --optimize"
run_vps_command "cd $APP_DIR && php artisan optimize"

echo "✅ Application optimized"

echo ""
echo "🎉 Deployment completed successfully!"
echo ""
echo "📋 Deployment Summary:"
echo "• Application URL: http://$DOMAIN"
echo "• Admin Login: admin@admin.com / admin123"
echo "• Database: silencio_gym_db"
echo "• Database User: silencio_user"
echo "• Application Directory: $APP_DIR"
echo ""
echo "📋 Next Steps:"
echo "1. Test your application at http://$DOMAIN"
echo "2. Configure RFID to connect to http://$DOMAIN"
echo "3. Configure email settings in .env file"
echo ""
echo "✅ Your Silencio Gym Management System is now live!"
