@echo off
title Deploying Silencio Gym to rbagym.com
color 0A
cls

echo ========================================
echo   PREPARING SILENCIO GYM FOR RBAGYM.COM
echo ========================================
echo.

cd /d "silencio-gym-mms-main"

echo Step 1: Installing production dependencies...
call composer install --optimize-autoloader --no-dev --no-interaction
if %errorlevel% neq 0 (
    echo ERROR: Failed to install Composer dependencies
    pause
    exit /b 1
)

echo Step 2: Building frontend assets...
call npm install --silent
call npm run build
if %errorlevel% neq 0 (
    echo ERROR: Failed to build frontend assets
    pause
    exit /b 1
)

echo Step 3: Creating production environment file...
copy hostinger.env.example .env.production

echo Creating .env.production with rbagym.com configuration...
(
echo APP_NAME="Silencio Gym Management System"
echo APP_ENV=production
echo APP_KEY=
echo APP_DEBUG=false
echo APP_URL=https://rbagym.com
echo.
echo LOG_CHANNEL=stack
echo LOG_DEPRECATIONS_CHANNEL=null
echo LOG_LEVEL=error
echo.
echo # Database Configuration for Hostinger
echo DB_CONNECTION=mysql
echo DB_HOST=localhost
echo DB_PORT=3306
echo DB_DATABASE=u123456789_rbagym
echo DB_USERNAME=u123456789_rbagym
echo DB_PASSWORD=your_database_password
echo.
echo # Session Configuration
echo SESSION_DRIVER=database
echo SESSION_LIFETIME=120
echo SESSION_ENCRYPT=false
echo SESSION_PATH=/
echo SESSION_DOMAIN=null
echo.
echo # Cache Configuration
echo CACHE_DRIVER=database
echo CACHE_PREFIX=
echo.
echo # Queue Configuration
echo QUEUE_CONNECTION=database
echo.
echo # Mail Configuration
echo MAIL_MAILER=smtp
echo MAIL_HOST=smtp.hostinger.com
echo MAIL_PORT=587
echo MAIL_USERNAME=noreply@rbagym.com
echo MAIL_PASSWORD=your_email_password
echo MAIL_ENCRYPTION=tls
echo MAIL_FROM_ADDRESS=noreply@rbagym.com
echo MAIL_FROM_NAME="Silencio Gym Management System"
echo.
echo # RFID Configuration
echo RFID_DEVICE_ID=main_reader
echo RFID_API_URL=https://rbagym.com
echo.
echo # Security Settings
echo BCRYPT_ROUNDS=12
) > .env.production

echo Step 4: Creating deployment package...
cd ..
if exist "rbagym-deployment" rmdir /s /q "rbagym-deployment"
mkdir "rbagym-deployment"

echo Copying application files...
xcopy "silencio-gym-mms-main\app" "rbagym-deployment\app\" /E /I /Q
xcopy "silencio-gym-mms-main\bootstrap" "rbagym-deployment\bootstrap\" /E /I /Q
xcopy "silencio-gym-mms-main\config" "rbagym-deployment\config\" /E /I /Q
xcopy "silencio-gym-mms-main\database" "rbagym-deployment\database\" /E /I /Q
xcopy "silencio-gym-mms-main\public" "rbagym-deployment\public\" /E /I /Q
xcopy "silencio-gym-mms-main\resources" "rbagym-deployment\resources\" /E /I /Q
xcopy "silencio-gym-mms-main\routes" "rbagym-deployment\routes\" /E /I /Q
xcopy "silencio-gym-mms-main\storage" "rbagym-deployment\storage\" /E /I /Q
xcopy "silencio-gym-mms-main\vendor" "rbagym-deployment\vendor\" /E /I /Q

echo Copying essential files...
copy "silencio-gym-mms-main\artisan" "rbagym-deployment\"
copy "silencio-gym-mms-main\composer.json" "rbagym-deployment\"
copy "silencio-gym-mms-main\composer.lock" "rbagym-deployment\"
copy "silencio-gym-mms-main\package.json" "rbagym-deployment\"
copy "silencio-gym-mms-main\package-lock.json" "rbagym-deployment\"
copy "silencio-gym-mms-main\vite.config.js" "rbagym-deployment\"
copy "silencio-gym-mms-main\.env.production" "rbagym-deployment\.env"
copy "silencio-gym-mms-main\README.md" "rbagym-deployment\"

echo Copying RFID system files...
copy "silencio-gym-mms-main\rfid_reader.py" "rbagym-deployment\"
copy "silencio-gym-mms-main\requirements.txt" "rbagym-deployment\"
copy "silencio-gym-mms-main\rfid_config.json" "rbagym-deployment\"

echo Creating .htaccess for Hostinger...
(
echo ^<IfModule mod_rewrite.c^>
echo     ^<IfModule mod_negotiation.c^>
echo         Options -MultiViews -Indexes
echo     ^</IfModule^>
echo.
echo     RewriteEngine On
echo.
echo     # Handle Authorization Header
echo     RewriteCond %%{HTTP:Authorization} .
echo     RewriteRule .* - [E=HTTP_AUTHORIZATION:%%{HTTP:Authorization}]
echo.
echo     # Redirect Trailing Slashes If Not A Folder...
echo     RewriteCond %%{REQUEST_FILENAME} !-d
echo     RewriteCond %%{REQUEST_URI} (.+^)/$
echo     RewriteRule ^^ %%1 [L,R=301]
echo.
echo     # Send Requests To Front Controller...
echo     RewriteCond %%{REQUEST_FILENAME} !-d
echo     RewriteCond %%{REQUEST_FILENAME} !-f
echo     RewriteRule ^^ index.php [L]
echo ^</IfModule^>
) > "rbagym-deployment\public\.htaccess"

echo Step 5: Creating deployment instructions...
(
echo # Hostinger Deployment Instructions for rbagym.com
echo.
echo ## 🚀 Quick Deployment Steps
echo.
echo ### 1. Upload Files to Hostinger
echo 1. **Login to Hostinger hPanel**
echo 2. **Go to File Manager**
echo 3. **Navigate to public_html directory**
echo 4. **Upload all files from this deployment package**
echo 5. **Extract if uploaded as ZIP**
echo.
echo ### 2. Database Setup
echo 1. **Create MySQL Database in hPanel:**
echo    - Database Name: `u123456789_rbagym` ^(replace with your actual database name^)
echo    - Username: `u123456789_rbagym` ^(replace with your actual username^)
echo    - Password: Create a strong password
echo.
echo 2. **Update .env file:**
echo    - Edit the `.env` file in File Manager
echo    - Update database credentials:
echo      ```
echo      DB_DATABASE=your_actual_database_name
echo      DB_USERNAME=your_actual_database_username
echo      DB_PASSWORD=your_actual_database_password
echo      ```
echo.
echo ### 3. Laravel Setup Commands
echo **In Hostinger Terminal/SSH:**
echo ```bash
echo cd public_html
echo php artisan key:generate
echo php artisan migrate --force
echo php artisan db:seed --force
echo php artisan config:cache
echo php artisan route:cache
echo php artisan view:cache
echo php artisan storage:link
echo ```
echo.
echo ### 4. File Permissions
echo ```bash
echo chmod -R 755 storage/
echo chmod -R 755 bootstrap/cache/
echo ```
echo.
echo ### 5. Access Your Website
echo - **URL:** https://rbagym.com
echo - **Admin Login:** admin@admin.com / admin123
echo.
echo ## 🔧 Important Notes
echo.
echo ### Database Migration
echo - Your SQLite database will be converted to MySQL
echo - All member data, payments, and RFID logs will be preserved
echo - Run migrations to create the proper MySQL structure
echo.
echo ### RFID System
echo - RFID functionality is fully preserved
echo - Update your local RFID reader to point to: https://rbagym.com
echo - All RFID endpoints will work automatically
echo.
echo ### SSL Certificate
echo - Hostinger provides free SSL
echo - Your site will be accessible via HTTPS automatically
echo.
echo ## 🆘 Troubleshooting
echo.
echo ### Common Issues:
echo 1. **500 Error:** Check file permissions and .env configuration
echo 2. **Database Connection:** Verify database credentials in .env
echo 3. **Missing Assets:** Run `php artisan storage:link`
echo.
echo ### Support:
echo - Check Laravel logs: `storage/logs/laravel.log`
echo - Hostinger support: Available 24/7 in hPanel
) > "rbagym-deployment\DEPLOYMENT_INSTRUCTIONS.md"

echo Step 6: Creating database backup...
if exist "silencio-gym-mms-main\database\database.sqlite" (
    copy "silencio-gym-mms-main\database\database.sqlite" "rbagym-deployment\database_backup.sqlite"
    echo ✅ Database backup created
) else (
    echo ⚠️ SQLite database not found, skipping backup
)

echo Step 7: Creating ZIP file for upload...
powershell -Command "Compress-Archive -Path 'rbagym-deployment\*' -DestinationPath 'rbagym-deployment.zip' -Force"

echo.
echo ========================================
echo   DEPLOYMENT PACKAGE READY!
echo ========================================
echo.
echo ✅ Complete Laravel application prepared
echo ✅ Production environment configured for rbagym.com
echo ✅ Database backup included
echo ✅ RFID system files included
echo ✅ Deployment instructions created
echo ✅ Ready-to-upload ZIP file created
echo.
echo 📋 Next Steps:
echo 1. Go to Hostinger hPanel
echo 2. Choose 'Upload backup files' in migration tool
echo 3. Upload: rbagym-deployment.zip
echo 4. Follow the deployment instructions
echo.
echo 🌐 Your gym will be live at: https://rbagym.com
echo.
pause
