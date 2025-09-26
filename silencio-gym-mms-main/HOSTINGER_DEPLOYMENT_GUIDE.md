# Hostinger Deployment Guide for Silencio Gym Management System

This guide will help you deploy your Laravel-based gym management system to Hostinger hosting.

## 📋 Prerequisites

### Hostinger Account Requirements
- **Hosting Plan**: Business or Premium plan (required for PHP 8.2+ and MySQL)
- **Domain**: Your domain name
- **SSL Certificate**: Free SSL provided by Hostinger
- **PHP Version**: 8.2 or higher
- **MySQL Database**: Available in your hosting plan

### Local Requirements
- Composer installed
- Node.js and npm installed
- Git (for version control)

## 🚀 Step-by-Step Deployment

### Step 1: Prepare Your Application

1. **Build Frontend Assets**
   ```bash
   cd silencio-gym-mms-main
   npm install
   npm run build
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

### Step 2: Upload Files to Hostinger

#### Option A: Using File Manager (Recommended for beginners)

1. **Login to Hostinger Control Panel**
   - Go to your Hostinger account
   - Navigate to "File Manager"

2. **Navigate to Public HTML**
   - Go to `public_html` folder
   - This is your website's root directory

3. **Upload Application Files**
   - Create a ZIP file of your entire `silencio-gym-mms-main` folder
   - Upload the ZIP file to `public_html`
   - Extract the ZIP file
   - Move all contents from `silencio-gym-mms-main` to `public_html`

#### Option B: Using FTP/SFTP

1. **Get FTP Credentials**
   - From Hostinger control panel, go to "FTP Accounts"
   - Note down your FTP host, username, and password

2. **Connect with FTP Client**
   - Use FileZilla, WinSCP, or similar
   - Connect to your server

3. **Upload Files**
   - Upload all files to `/public_html/` directory
   - Ensure all files are uploaded correctly

### Step 3: Set Up Database

1. **Create MySQL Database**
   - Go to Hostinger control panel
   - Navigate to "MySQL Databases"
   - Create a new database (e.g., `silencio_gym_db`)
   - Create a database user
   - Assign the user to the database with full privileges

2. **Note Database Credentials**
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost`)

### Step 4: Configure Environment

1. **Create .env File**
   - In your `public_html` directory, create a `.env` file
   - Copy the contents from `hostinger.env.example`
   - Update the following values:

   ```env
   APP_NAME="Silencio Gym Management System"
   APP_ENV=production
   APP_KEY=base64:your_generated_key_here
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_username
   DB_PASSWORD=your_database_password
   ```

2. **Generate Application Key**
   - In Hostinger File Manager, open Terminal/SSH
   - Navigate to your website directory
   - Run: `php artisan key:generate`

### Step 5: Run Database Migrations

1. **Access Terminal/SSH**
   - In Hostinger control panel, go to "Terminal" or "SSH"
   - Navigate to your website directory

2. **Run Migrations**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

### Step 6: Set File Permissions

1. **Set Directory Permissions**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   chmod -R 755 public/
   ```

2. **Set File Permissions**
   ```bash
   chmod 644 .env
   chmod 644 database/database.sqlite
   ```

### Step 7: Configure Web Server

1. **Create .htaccess File** (if not exists)
   - In `public_html`, create `.htaccess` file with:

   ```apache
   <IfModule mod_rewrite.c>
       RewriteEngine On
       RewriteRule ^(.*)$ public/$1 [L]
   </IfModule>
   ```

2. **Alternative: Move Public Files**
   - Move all contents from `public/` folder to `public_html/`
   - Update `.env` file paths accordingly

### Step 8: Optimize Application

1. **Clear and Cache Configuration**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Optimize Autoloader**
   ```bash
   composer dump-autoload --optimize
   ```

## 🔧 Post-Deployment Configuration

### 1. Test Your Application

1. **Visit Your Website**
   - Go to `https://yourdomain.com`
   - Check if the login page loads

2. **Test Login**
   - Default admin credentials:
     - Email: `admin@admin.com`
     - Password: `admin123`

### 2. Configure RFID System (Optional)

If you're using RFID functionality:

1. **Update RFID Configuration**
   - In `.env` file, set:
   ```env
   RFID_API_URL=https://yourdomain.com
   ```

2. **Python Script Setup** (if needed)
   - Upload `rfid_reader.py` to your server
   - Install Python dependencies on server
   - Configure the script to point to your domain

### 3. Set Up SSL Certificate

1. **Enable SSL**
   - In Hostinger control panel, go to "SSL"
   - Enable "Free SSL Certificate"
   - Force HTTPS redirect

### 4. Configure Email (Optional)

1. **Set Up SMTP**
   - In `.env` file, configure mail settings:
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@yourdomain.com
   MAIL_PASSWORD=your_email_password
   MAIL_ENCRYPTION=tls
   ```

## 🛠️ Troubleshooting

### Common Issues and Solutions

#### 1. 500 Internal Server Error
- Check file permissions
- Verify `.env` file configuration
- Check error logs in Hostinger control panel

#### 2. Database Connection Error
- Verify database credentials in `.env`
- Ensure database user has proper privileges
- Check if database exists

#### 3. CSS/JS Not Loading
- Run `npm run build` locally
- Upload `public/build/` folder
- Check file permissions

#### 4. Login Not Working
- Clear browser cache
- Check session configuration
- Verify database is properly seeded

#### 5. RFID Not Working
- Check if Python is available on server
- Verify API endpoints are accessible
- Check firewall settings

### Error Logs Location
- Hostinger Error Logs: Control Panel → Logs
- Laravel Logs: `storage/logs/laravel.log`
- PHP Error Logs: Control Panel → Error Logs

## 🔒 Security Considerations

### 1. File Permissions
```bash
# Secure file permissions
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 .env
```

### 2. Environment Security
- Never commit `.env` file to version control
- Use strong database passwords
- Enable HTTPS
- Keep application updated

### 3. Database Security
- Use strong database passwords
- Limit database user privileges
- Regular database backups

## 📊 Performance Optimization

### 1. Enable Caching
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Optimize Database
- Add database indexes
- Regular database maintenance
- Monitor query performance

### 3. CDN Setup (Optional)
- Use Hostinger's CDN service
- Optimize images
- Minify CSS/JS files

## 🔄 Maintenance

### Regular Tasks
1. **Backup Database**
   - Use Hostinger's backup feature
   - Export database regularly

2. **Update Dependencies**
   ```bash
   composer update
   npm update
   ```

3. **Clear Logs**
   ```bash
   php artisan log:clear
   ```

4. **Monitor Performance**
   - Check server resources
   - Monitor website speed
   - Review error logs

## 📞 Support

### Hostinger Support
- Live Chat: Available 24/7
- Knowledge Base: Comprehensive guides
- Community Forum: User discussions

### Application Support
- Check error logs
- Review this deployment guide
- Contact development team

## 🎯 Success Checklist

- [ ] Files uploaded to `public_html`
- [ ] Database created and configured
- [ ] `.env` file properly configured
- [ ] Migrations run successfully
- [ ] File permissions set correctly
- [ ] SSL certificate enabled
- [ ] Website loads without errors
- [ ] Login functionality works
- [ ] RFID system configured (if needed)
- [ ] Email notifications working (if configured)
- [ ] Performance optimized
- [ ] Security measures implemented

## 📝 Notes

- Always test in a staging environment first
- Keep backups before making changes
- Monitor your website after deployment
- Update dependencies regularly
- Review security settings periodically

---

**Congratulations!** Your Silencio Gym Management System should now be successfully deployed on Hostinger. If you encounter any issues, refer to the troubleshooting section or contact support.
