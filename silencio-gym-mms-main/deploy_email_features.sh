#!/bin/bash

# Deploy Email Verification and Password Reset Features to VPS
# Server: 156.67.221.184
# Path: /var/www/silencio-gym/

echo "🚀 Deploying Email Verification and Password Reset Features to VPS..."

# VPS connection details
VPS_HOST="156.67.221.184"
VPS_USER="root"
VPS_PATH="/var/www/silencio-gym"

# Files to upload
FILES_TO_UPLOAD=(
    "database/migrations/2025_01_15_000002_add_email_verification_to_members_table.php"
    "database/migrations/2025_01_15_000003_create_member_password_reset_tokens_table.php"
    "app/Models/Member.php"
    "app/Notifications/MemberEmailVerification.php"
    "app/Notifications/MemberPasswordReset.php"
    "app/Http/Controllers/MemberEmailVerificationController.php"
    "app/Http/Controllers/MemberPasswordResetController.php"
    "app/Http/Controllers/MemberAuthController.php"
    "app/Http/Controllers/AuthController.php"
    "config/auth.php"
    "resources/views/auth/member-verify-email.blade.php"
    "resources/views/auth/member-forgot-password.blade.php"
    "resources/views/auth/member-reset-password.blade.php"
    "resources/views/login.blade.php"
    "routes/web_server.php"
)

echo "📁 Uploading files to VPS..."

# Upload each file
for file in "${FILES_TO_UPLOAD[@]}"; do
    echo "  📤 Uploading $file..."
    
    # Create directory structure on VPS if it doesn't exist
    dir_path=$(dirname "$file")
    ssh "$VPS_USER@$VPS_HOST" "mkdir -p $VPS_PATH/$dir_path"
    
    # Upload the file
    scp "$file" "$VPS_USER@$VPS_HOST:$VPS_PATH/$file"
    
    if [ $? -eq 0 ]; then
        echo "    ✅ Successfully uploaded $file"
    else
        echo "    ❌ Failed to upload $file"
    fi
done

echo ""
echo "🔧 Running post-deployment commands on VPS..."

# Run commands on VPS
ssh "$VPS_USER@$VPS_HOST" << 'EOF'
cd /var/www/silencio-gym

echo "📦 Installing/updating dependencies..."
composer install --no-dev --optimize-autoloader

echo "🗃️ Running database migrations..."
php artisan migrate --force

echo "🧹 Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "🔧 Setting proper permissions..."
chown -R www-data:www-data /var/www/silencio-gym
chmod -R 755 /var/www/silencio-gym
chmod -R 775 /var/www/silencio-gym/storage
chmod -R 775 /var/www/silencio-gym/bootstrap/cache

echo "✅ Deployment completed successfully!"
EOF

echo ""
echo "🎉 Email Verification and Password Reset Features Deployed!"
echo ""
echo "📋 Next Steps:"
echo "1. Update .env file on VPS with SMTP settings:"
echo "   MAIL_MAILER=smtp"
echo "   MAIL_HOST=smtp.hostinger.com"
echo "   MAIL_PORT=587"
echo "   MAIL_USERNAME=noreply@rbagym.com"
echo "   MAIL_PASSWORD=your_actual_password"
echo "   MAIL_ENCRYPTION=tls"
echo "   MAIL_FROM_ADDRESS=noreply@rbagym.com"
echo "   MAIL_FROM_NAME=\"Silencio Gym\""
echo ""
echo "2. Test the features:"
echo "   - Registration: http://156.67.221.184/register"
echo "   - Login: http://156.67.221.184/login"
echo "   - Forgot Password: http://156.67.221.184/member/forgot-password"
echo ""
echo "3. Features implemented:"
echo "   ✅ Email verification for new registrations"
echo "   ✅ Password reset functionality"
echo "   ✅ Secure token-based system"
echo "   ✅ Professional email templates"
echo "   ✅ User-friendly interfaces"
echo ""
echo "🔗 Access your system: http://156.67.221.184/"
