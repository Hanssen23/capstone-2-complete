# Email Verification and Password Reset Deployment Guide

## 🎯 Overview
This guide will help you deploy the email verification and password reset features to your VPS server at **156.67.221.184**.

## 📁 Files to Upload

Upload the following files to your VPS server at `/var/www/silencio-gym/`:

### Database Migrations
- `database/migrations/2025_01_15_000002_add_email_verification_to_members_table.php`
- `database/migrations/2025_01_15_000003_create_member_password_reset_tokens_table.php`

### Models
- `app/Models/Member.php`

### Notifications
- `app/Notifications/MemberEmailVerification.php`
- `app/Notifications/MemberPasswordReset.php`

### Controllers
- `app/Http/Controllers/MemberEmailVerificationController.php`
- `app/Http/Controllers/MemberPasswordResetController.php`
- `app/Http/Controllers/MemberAuthController.php`
- `app/Http/Controllers/AuthController.php`

### Configuration
- `config/auth.php`

### Views
- `resources/views/auth/member-verify-email.blade.php`
- `resources/views/auth/member-forgot-password.blade.php`
- `resources/views/auth/member-reset-password.blade.php`
- `resources/views/login.blade.php`

### Routes
- `routes/web_server.php`

## 🚀 Deployment Steps

### Step 1: Upload Files
1. Connect to your VPS via FTP/SFTP or file manager
2. Navigate to `/var/www/silencio-gym/`
3. Upload all the files listed above, maintaining the directory structure

### Step 2: Run Database Migrations
SSH into your VPS and run:
```bash
cd /var/www/silencio-gym
php artisan migrate --force
```

### Step 3: Clear Caches
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 4: Set Permissions
```bash
chown -R www-data:www-data /var/www/silencio-gym
chmod -R 755 /var/www/silencio-gym
chmod -R 775 /var/www/silencio-gym/storage
chmod -R 775 /var/www/silencio-gym/bootstrap/cache
```

### Step 5: Configure Email Settings
Update your `.env` file on the VPS with SMTP settings:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@rbagym.com
MAIL_PASSWORD=your_actual_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@rbagym.com
MAIL_FROM_NAME="Silencio Gym"
```

## ✅ Features Implemented

### 1. Email Verification for Registration
- **What it does**: New members must verify their email before they can log in
- **How it works**: 
  - User registers → Account created as "inactive"
  - Verification email sent automatically
  - User clicks verification link → Account activated
  - User can now log in

### 2. Password Reset Functionality
- **What it does**: Members can reset their password via email
- **How it works**:
  - User clicks "Forgot Password?" on login page
  - Enters email → Reset link sent
  - User clicks reset link → Sets new password
  - Redirected to login with success message

### 3. Security Features
- ✅ **Token-based reset** with 60-minute expiration
- ✅ **Rate limiting** (60 seconds between requests)
- ✅ **Email verification required** before login
- ✅ **Secure signed URLs** for verification
- ✅ **HMAC token hashing** for security

## 🧪 Testing the Features

### Test Email Verification
1. Go to: `http://156.67.221.184/register`
2. Register a new member
3. Check email for verification link
4. Click verification link
5. Try to log in

### Test Password Reset
1. Go to: `http://156.67.221.184/login`
2. Click "Forgot Password?"
3. Enter email address
4. Check email for reset link
5. Click reset link and set new password
6. Log in with new password

## 🔗 Important URLs

- **Main Login**: `http://156.67.221.184/login`
- **Registration**: `http://156.67.221.184/register`
- **Forgot Password**: `http://156.67.221.184/member/forgot-password`
- **Email Verification Notice**: `http://156.67.221.184/member/verify-email`

## 🛠️ Troubleshooting

### Email Not Sending
1. Check SMTP credentials in `.env`
2. Verify Hostinger email account is active
3. Check Laravel logs: `/var/www/silencio-gym/storage/logs/laravel.log`

### Database Errors
1. Ensure migrations ran successfully
2. Check database connection in `.env`
3. Verify tables exist: `members` table has `email_verified_at` column

### Route Errors
1. Clear route cache: `php artisan route:clear`
2. Check web server configuration
3. Verify all controller files are uploaded

## 📞 Support

If you encounter any issues:
1. Check the Laravel logs for detailed error messages
2. Verify all files were uploaded correctly
3. Ensure database migrations completed successfully
4. Test email configuration with a simple test

## 🎉 Success!

Once deployed, your gym management system will have:
- ✅ **Real email verification** for new registrations
- ✅ **Secure password reset** functionality
- ✅ **Professional email templates** with gym branding
- ✅ **User-friendly interfaces** for all email-related actions

Your members can now safely register and manage their accounts with proper email verification and password reset capabilities!
