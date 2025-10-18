# 🚀 Email Features Deployment Checklist

## Quick Fix for "Forgot Password" Not Working

The "Forgot password?" link isn't working because the email verification and password reset features haven't been deployed to your VPS server yet.

## 🎯 Deployment Options

### Option 1: Automated Deployment (Recommended)
Run one of these scripts from your project directory:

**Windows Command Prompt:**
```cmd
deploy_to_vps.bat
```

**PowerShell:**
```powershell
.\deploy_to_vps.ps1
```

### Option 2: Manual Deployment
If the automated scripts don't work, follow these steps:

#### Step 1: Upload Files to VPS
Upload these files to your VPS at `/var/www/silencio-gym/`:

**Database Migrations:**
- `database/migrations/2025_01_15_000002_add_email_verification_to_members_table.php`
- `database/migrations/2025_01_15_000003_create_member_password_reset_tokens_table.php`

**Models:**
- `app/Models/Member.php`

**Notifications:**
- `app/Notifications/MemberEmailVerification.php`
- `app/Notifications/MemberPasswordReset.php`

**Controllers:**
- `app/Http/Controllers/MemberEmailVerificationController.php`
- `app/Http/Controllers/MemberPasswordResetController.php`
- `app/Http/Controllers/MemberAuthController.php`
- `app/Http/Controllers/AuthController.php`

**Configuration:**
- `config/auth.php`

**Views:**
- `resources/views/auth/member-verify-email.blade.php`
- `resources/views/auth/member-forgot-password.blade.php`
- `resources/views/auth/member-reset-password.blade.php`
- `resources/views/login.blade.php`

**Routes:**
- `routes/web_server.php`

#### Step 2: Run Commands on VPS
SSH into your VPS and run:
```bash
cd /var/www/silencio-gym
php artisan migrate --force
php artisan config:clear
php artisan route:clear
php artisan view:clear
chown -R www-data:www-data /var/www/silencio-gym
chmod -R 755 /var/www/silencio-gym
chmod -R 775 /var/www/silencio-gym/storage
chmod -R 775 /var/www/silencio-gym/bootstrap/cache
```

## 🔧 Post-Deployment Configuration

### Update .env File on VPS
Add these SMTP settings to your VPS `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Silencio Gym"
```

## ✅ Testing

After deployment, test these URLs:

1. **Forgot Password:** http://156.67.221.184/member/forgot-password
2. **Login Page:** http://156.67.221.184/login (should show "Forgot password?" link)
3. **Member Registration:** http://156.67.221.184/member/register

## 🆘 Troubleshooting

If the forgot password link still doesn't work:

1. Check if the routes file was uploaded correctly
2. Clear Laravel cache: `php artisan config:clear && php artisan route:clear`
3. Check Laravel logs: `tail -f /var/www/silencio-gym/storage/logs/laravel.log`
4. Verify file permissions are correct

## 📞 Support

If you encounter any issues during deployment, the error messages will help identify what needs to be fixed.
