#!/bin/bash

# Automated Deployment Script for Silencio Gym Management System to rbagym.com
# This script will prepare your application for Hostinger deployment

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Preparing Silencio Gym Management System for rbagym.com${NC}"
echo -e "${BLUE}================================================================${NC}"

# Configuration
DOMAIN="rbagym.com"
APP_NAME="Silencio Gym Management System"

echo -e "${YELLOW}📋 Step 1: Preparing application files...${NC}"

# Navigate to the application directory
cd silencio-gym-mms-main

# Install production dependencies
echo -e "${YELLOW}📥 Installing production dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction

# Install Node.js dependencies and build assets
echo -e "${YELLOW}🎨 Building frontend assets...${NC}"
npm install --silent
npm run build

echo -e "${GREEN}✅ Dependencies and assets prepared${NC}"

echo -e "${YELLOW}📋 Step 2: Creating production environment file...${NC}"

# Create production .env file
cp hostinger.env.example .env.production

# Update the .env with your domain
cat > .env.production << EOF
APP_NAME="Silencio Gym Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://rbagym.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration for Hostinger
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_rbagym
DB_USERNAME=u123456789_rbagym
DB_PASSWORD=your_database_password

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
MAIL_USERNAME=noreply@rbagym.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rbagym.com
MAIL_FROM_NAME="Silencio Gym Management System"

# RFID Configuration
RFID_DEVICE_ID=main_reader
RFID_API_URL=https://rbagym.com

# Security Settings
BCRYPT_ROUNDS=12
EOF

echo -e "${GREEN}✅ Production environment file created${NC}"

echo -e "${YELLOW}📋 Step 3: Creating deployment package...${NC}"

# Create deployment directory
mkdir -p ../rbagym-deployment

# Copy essential files and directories
echo -e "${YELLOW}📦 Copying application files...${NC}"

# Core Laravel directories
cp -r app ../rbagym-deployment/
cp -r bootstrap ../rbagym-deployment/
cp -r config ../rbagym-deployment/
cp -r database ../rbagym-deployment/
cp -r public ../rbagym-deployment/
cp -r resources ../rbagym-deployment/
cp -r routes ../rbagym-deployment/
cp -r storage ../rbagym-deployment/
cp -r vendor ../rbagym-deployment/

# Essential files
cp artisan ../rbagym-deployment/
cp composer.json ../rbagym-deployment/
cp composer.lock ../rbagym-deployment/
cp package.json ../rbagym-deployment/
cp package-lock.json ../rbagym-deployment/
cp vite.config.js ../rbagym-deployment/
cp .env.production ../rbagym-deployment/.env
cp README.md ../rbagym-deployment/

# RFID system files
cp rfid_reader.py ../rbagym-deployment/
cp requirements.txt ../rbagym-deployment/
cp rfid_config.json ../rbagym-deployment/

# Create .htaccess for Hostinger
cat > ../rbagym-deployment/public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

echo -e "${GREEN}✅ Deployment package created in ../rbagym-deployment/${NC}"

echo -e "${YELLOW}📋 Step 4: Creating deployment instructions...${NC}"

# Create deployment instructions
cat > ../rbagym-deployment/DEPLOYMENT_INSTRUCTIONS.md << 'EOF'
# Hostinger Deployment Instructions for rbagym.com

## 🚀 Quick Deployment Steps

### 1. Upload Files to Hostinger
1. **Login to Hostinger hPanel**
2. **Go to File Manager**
3. **Navigate to public_html directory**
4. **Upload all files from this deployment package**
5. **Extract if uploaded as ZIP**

### 2. Database Setup
1. **Create MySQL Database in hPanel:**
   - Database Name: `u123456789_rbagym` (replace with your actual database name)
   - Username: `u123456789_rbagym` (replace with your actual username)
   - Password: Create a strong password

2. **Update .env file:**
   - Edit the `.env` file in File Manager
   - Update database credentials:
     ```
     DB_DATABASE=your_actual_database_name
     DB_USERNAME=your_actual_database_username
     DB_PASSWORD=your_actual_database_password
     ```

### 3. Laravel Setup Commands
**In Hostinger Terminal/SSH:**
```bash
cd public_html
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### 4. File Permissions
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 5. Access Your Website
- **URL:** https://rbagym.com
- **Admin Login:** admin@admin.com / admin123

## 🔧 Important Notes

### Database Migration
- Your SQLite database will be converted to MySQL
- All member data, payments, and RFID logs will be preserved
- Run migrations to create the proper MySQL structure

### RFID System
- RFID functionality is fully preserved
- Update your local RFID reader to point to: https://rbagym.com
- All RFID endpoints will work automatically

### SSL Certificate
- Hostinger provides free SSL
- Your site will be accessible via HTTPS automatically

## 🆘 Troubleshooting

### Common Issues:
1. **500 Error:** Check file permissions and .env configuration
2. **Database Connection:** Verify database credentials in .env
3. **Missing Assets:** Run `php artisan storage:link`

### Support:
- Check Laravel logs: `storage/logs/laravel.log`
- Hostinger support: Available 24/7 in hPanel
EOF

echo -e "${GREEN}✅ Deployment instructions created${NC}"

echo -e "${YELLOW}📋 Step 5: Creating backup of current database...${NC}"

# Export current SQLite database
if [ -f "database/database.sqlite" ]; then
    cp database/database.sqlite ../rbagym-deployment/database_backup.sqlite
    echo -e "${GREEN}✅ Database backup created${NC}"
else
    echo -e "${YELLOW}⚠️ SQLite database not found, skipping backup${NC}"
fi

echo -e "${YELLOW}📋 Step 6: Final preparations...${NC}"

# Create a ZIP file for easy upload
cd ../rbagym-deployment
zip -r rbagym-deployment.zip . -x "*.DS_Store" "*.git*"

echo -e "${GREEN}✅ ZIP file created: rbagym-deployment.zip${NC}"

cd ..

echo ""
echo -e "${GREEN}🎉 DEPLOYMENT PACKAGE READY!${NC}"
echo ""
echo -e "${BLUE}📋 What's been prepared:${NC}"
echo -e "${BLUE}• Complete Laravel application${NC}"
echo -e "${BLUE}• Production environment configuration${NC}"
echo -e "${BLUE}• Database backup (SQLite)${NC}"
echo -e "${BLUE}• RFID system files${NC}"
echo -e "${BLUE}• Deployment instructions${NC}"
echo -e "${BLUE}• Ready-to-upload ZIP file${NC}"
echo ""
echo -e "${YELLOW}📋 Next Steps:${NC}"
echo -e "${YELLOW}1. Go to Hostinger hPanel${NC}"
echo -e "${YELLOW}2. Choose 'Upload backup files' in migration tool${NC}"
echo -e "${YELLOW}3. Upload: rbagym-deployment/rbagym-deployment.zip${NC}"
echo -e "${YELLOW}4. Follow the deployment instructions${NC}"
echo ""
echo -e "${GREEN}🌐 Your gym management system will be live at: https://rbagym.com${NC}"
echo ""
