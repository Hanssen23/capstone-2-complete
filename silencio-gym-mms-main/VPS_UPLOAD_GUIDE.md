# 🚀 Upload Silencio Gym to Hostinger VPS

## Overview
Deploy your Laravel gym management system to your Hostinger VPS at `156.67.221.184`. All RFID functionality will be preserved!

## 📋 Prerequisites
- Hostinger VPS access: `ssh root@156.67.221.184`
- Your VPS password
- All project files uploaded

## 🔧 Method 1: Hostinger File Manager (Easiest)

1. **Login to Hostinger Control Panel**
   - Go to your Hostinger account
   - Navigate to your VPS `srv1031558.hstgr.cloud`

2. **Upload Files via Browser Terminal**
   - Click "Browser terminal" button in your VPS overview
   - Navigate to web root: `cd /var/www/html/`

3. **Upload Your Project Files**
   ```bash
   # Download project files (if not already uploaded)
   # Extract your project files to /var/www/html/
   ```

4. **Run Setup Script**
   ```bash
   # Navigate to project directory
   cd /var/www/html/
   
   # Run the setup script
   chmod +x hostinger_vps_setup.sh
   ./hostinger_vps_setup.sh
   ```

## 🔧 Method 2: SSH Upload (Advanced)

1. **SSH into your VPS**
   ```bash
   ssh root@156.67.221.184
   ```

2. **Upload via SCP** (from your local machine)
   ```bash
   # Compress your project (excluding unnecessary files)
   tar --exclude='node_modules' --exclude='.git' --exclude='storage/logs/*' -czf silencio-gym.tar.gz .
   
   # Upload to VPS
   scp silencio-gym.tar.gz root@156.67.221.184:/var/www/html/
   
   # SSH into VPS and extract
   ssh root@156.67.221.184
   cd /var/www/html/
   tar -xzf silencio-gym.tar.gz
   ```

3. **Run Setup Commands**
   ```bash
   cd /var/www/html/
   chmod +x hostinger_vps_setup.sh
   ./hostinger_vps_setup.sh
   ```

## 🔧 Method 3: GitHub (If repository exists)

```bash
# SSH into VPS
ssh root@156.67.221.184

# Navigate to web root
cd /var/www/html/

# Clone your repository (if available)
git clone https://github.com/yourusername/silencio-gym.git .

# Run setup
chmod +x hostinger_vps_setup.sh
./hostinger_vps_setup.sh
```

## 🗄️ Database Setup (If MySQL needed)

```bash
# Create database
mysql -u root -p
CREATE DATABASE silencio_gym;
CREATE USER 'silencio_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON silencio_gym.* TO 'silencio_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 🌐 Web Server Configuration

### For Nginx:
```nginx
server {
    listen 80;
    server_name 156.67.221.184;
    root /var/www/html/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### For Apache:
Ensure `.htaccess` file is in the root directory and mod_rewrite is enabled.

## ✅ Verification

After deployment, check:

1. **Site Accessibility**
   ```
   https://156.67.221.184
   ```

2. **Admin Login**
   ```
   Email: admin@admin.com
   Password: admin123
   ```

3. **RFID Status**
   - Check RFID monitor at: `/rfid-monitor`
   - API endpoints at: `/api/rfid/*`

## 🔧 Troubleshooting

### Common Issues:

1. **Permission Errors**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

2. **Missing Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Cache Issues**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

4. **Database Connection**
   ```bash
   php artisan migrate:status
   ```

## 📊 Post-Deployment

1. **Enable SSL** (recommended)
   ```bash
   # Install Certbot (example)
   apt install certbot python3-certbot-nginx
   certbot --nginx -d 156.67.221.184
   ```

2. **Setup Cron Jobs** (if needed)
   ```bash
   crontab -e
   # Add: * * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **Monitor Performance**
   - Check VPS resources in Hostinger panel
   - Monitor logs: `tail -f storage/logs/laravel.log`

## 🚨 Important Notes

- **RFID Functionality**: All RFID features are preserved
- **Database**: Application uses SQLite by default (MySQL optional)
- **Security**: Change default admin password after deployment
- **Updates**: Keep Laravel and dependencies updated
- **Backups**: Use Hostinger's backup features regularly

Your Silencio Gym Management System will be fully functional on the VPS!
