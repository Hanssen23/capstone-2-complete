# Working Deployment Script for Silencio Gym Management System
# PowerShell version with proper syntax

param(
    [string]$VPS_IP = "156.67.221.184",
    [string]$VPS_USER = "root",
    [string]$APP_DIR = "/var/www/silencio-gym",
    [string]$DOMAIN = "156.67.221.184",
    [string]$REPO_URL = "https://github.com/Hanssen23/Silencio.git"
)

Write-Host "🚀 Starting deployment of Silencio Gym Management System to VPS"
Write-Host "VPS IP: $VPS_IP"
Write-Host "Target Directory: $APP_DIR"
Write-Host ""

# Function to run commands on VPS
function Invoke-VPSCommand {
    param([string]$Command)
    Write-Host "Executing: $Command" -ForegroundColor Yellow
    ssh -o StrictHostKeyChecking=no "$VPS_USER@$VPS_IP" $Command
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Command failed with exit code: $LASTEXITCODE" -ForegroundColor Red
        return $false
    }
    return $true
}

try {
    Write-Host "📋 Step 1: Preparing VPS environment..." -ForegroundColor Cyan
    
    # Update system
    if (!(Invoke-VPSCommand "apt update")) { throw "Failed to update packages" }
    if (!(Invoke-VPSCommand "apt upgrade -y")) { throw "Failed to upgrade packages" }
    
    # Install required packages
    $packages = "nginx php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-sqlite3 composer nodejs npm git unzip mysql-server"
    if (!(Invoke-VPSCommand "apt install -y $packages")) { throw "Failed to install packages" }
    
    Write-Host "✅ VPS environment prepared" -ForegroundColor Green
    
    Write-Host "📋 Step 2: Setting up web server..." -ForegroundColor Cyan
    
    # Create Nginx configuration
    $nginxConfig = @'
server {
    listen 80;
    server_name 156.67.221.184;
    root /var/www/silencio-gym/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
'@
    
    # Write nginx config to file
    $tempFile = [System.IO.Path]::GetTempFileName()
    $nginxConfig | Out-File -FilePath $tempFile -Encoding UTF8
    
    # Copy config to VPS
    scp -o StrictHostKeyChecking=no $tempFile "$VPS_USER@$VPS_IP":/etc/nginx/sites-available/silencio-gym
    Remove-Item $tempFile
    
    # Enable site
    if (!(Invoke-VPSCommand "ln -sf /etc/nginx/sites-available/silencio-gym /etc/nginx/sites-enabled/")) { throw "Failed to enable site" }
    if (!(Invoke-VPSCommand "rm -f /etc/nginx/sites-enabled/default")) { throw "Failed to remove default site" }
    if (!(Invoke-VPSCommand "nginx -t")) { throw "Nginx configuration test failed" }
    if (!(Invoke-VPSCommand "systemctl reload nginx")) { throw "Failed to reload nginx" }
    
    Write-Host "✅ Nginx configured" -ForegroundColor Green
    
    Write-Host "📋 Step 3: Setting up MySQL database..." -ForegroundColor Cyan
    
    # Create database and user
    if (!(Invoke-VPSCommand "mysql -e 'CREATE DATABASE IF NOT EXISTS silencio_gym_db;'")) { throw "Failed to create database" }
    if (!(Invoke-VPSCommand "mysql -e 'CREATE USER IF NOT EXISTS ''silencio_user''@''localhost'' IDENTIFIED BY ''SilencioGym2024!'';'")) { throw "Failed to create user" }
    if (!(Invoke-VPSCommand "mysql -e 'GRANT ALL PRIVILEGES ON silencio_gym_db.* TO ''silencio_user''@''localhost'';'")) { throw "Failed to grant privileges" }
    if (!(Invoke-VPSCommand "mysql -e 'FLUSH PRIVILEGES;'")) { throw "Failed to flush privileges" }
    
    Write-Host "✅ MySQL database created" -ForegroundColor Green
    
    Write-Host "📋 Step 4: Deploying application..." -ForegroundColor Cyan
    
    # Create application directory and clone repository
    if (!(Invoke-VPSCommand "mkdir -p $APP_DIR")) { throw "Failed to create app directory" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; if [ -d '.git' ]; then git pull origin main; else git clone $REPO_URL .; fi")) { throw "Failed to deploy code" }
    
    Write-Host "✅ Application code deployed" -ForegroundColor Green
    
    Write-Host "📋 Step 5: Installing dependencies..." -ForegroundColor Cyan
    
    # Install PHP dependencies
    if (!(Invoke-VPSCommand "cd $APP_DIR; composer install --optimize-autoloader --no-dev")) { throw "Failed to install PHP dependencies" }
    
    # Install Node.js dependencies and build assets
    if (!(Invoke-VPSCommand "cd $APP_DIR; npm install")) { throw "Failed to install Node dependencies" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; npm run build")) { throw "Failed to build assets" }
    
    Write-Host "✅ Dependencies installed" -ForegroundColor Green
    
    Write-Host "📋 Step 6: Configuring environment..." -ForegroundColor Cyan
    
    # Create .env file content
    $envContent = @"
APP_NAME="Silencio Gym Management System"
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
MAIL_FROM_NAME="Silencio Gym Management System"

RFID_DEVICE_ID=main_reader
RFID_API_URL=http://$DOMAIN

BCRYPT_ROUNDS=12
"@
    
    # Write .env file to VPS
    $tempEnvFile = [System.IO.Path]::GetTempFileName()
    $envContent | Out-File -FilePath $tempEnvFile -Encoding UTF8
    scp -o StrictHostKeyChecking=no $tempEnvFile "$VPS_USER@$VPS_IP":"$APP_DIR/.env"
    Remove-Item $tempEnvFile
    
    Write-Host "✅ Environment configured" -ForegroundColor Green
    
    Write-Host "📋 Step 7: Setting up Laravel application..." -ForegroundColor Cyan
    
    # Generate application key and run migrations
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan key:generate --force")) { throw "Failed to generate app key" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan migrate --force")) { throw "Failed to run migrations" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan db:seed --force")) { throw "Failed to seed database" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan config:cache")) { throw "Failed to cache config" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan route:cache")) { throw "Failed to cache routes" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan view:cache")) { throw "Failed to cache views" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan storage:link")) { throw "Failed to link storage" }
    
    Write-Host "✅ Laravel application configured" -ForegroundColor Green
    
    Write-Host "📋 Step 8: Setting file permissions..." -ForegroundColor Cyan
    
    # Set proper permissions
    if (!(Invoke-VPSCommand "cd $APP_DIR; chown -R www-data:www-data .")) { throw "Failed to set ownership" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; chmod -R 755 storage/")) { throw "Failed to set storage permissions" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; chmod -R 755 bootstrap/cache/")) { throw "Failed to set cache permissions" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; chmod -R 755 public/")) { throw "Failed to set public permissions" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; chmod 600 .env")) { throw "Failed to set env permissions" }
    
    Write-Host "✅ File permissions set" -ForegroundColor Green
    
    Write-Host "📋 Step 9: Final optimizations..." -ForegroundColor Cyan
    
    # Final optimizations
    if (!(Invoke-VPSCommand "cd $APP_DIR; composer dump-autoload --optimize")) { throw "Failed to optimize autoloader" }
    if (!(Invoke-VPSCommand "cd $APP_DIR; php artisan optimize")) { throw "Failed to optimize application" }
    
    Write-Host "✅ Application optimized" -ForegroundColor Green
    
    Write-Host ""
    Write-Host "🎉 Deployment completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📋 Deployment Summary:" -ForegroundColor Blue
    Write-Host "• Application URL: http://$DOMAIN" -ForegroundColor Blue
    Write-Host "• Admin Login: admin@admin.com / admin123" -ForegroundColor Blue
    Write-Host "• Database: silencio_gym_db" -ForegroundColor Blue
    Write-Host "• Database User: silencio_user" -ForegroundColor Blue
    Write-Host "• Application Directory: $APP_DIR" -ForegroundColor Blue
    Write-Host ""
    Write-Host "📋 Next Steps:" -ForegroundColor Yellow
    Write-Host "1. Test your application at http://$DOMAIN" -ForegroundColor Yellow
    Write-Host "2. Configure RFID to connect to http://$DOMAIN" -ForegroundColor Yellow
    Write-Host "3. Configure email settings in .env file" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "✅ Your Silencio Gym Management System is now live!" -ForegroundColor Green
    
} catch {
    Write-Host "❌ Deployment failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Please check the error and try again." -ForegroundColor Red
    exit 1
}
