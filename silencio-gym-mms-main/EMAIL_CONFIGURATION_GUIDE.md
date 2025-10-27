# Email Configuration Guide

## Current Status

✅ **Email verification is working!** Emails are being generated and logged to `storage/logs/laravel.log`.

The current configuration uses `MAIL_MAILER=log`, which means emails are **written to the log file** instead of being sent to actual email addresses. This is perfect for local development and testing.

## How to View Verification Links (Current Setup)

### Method 1: Use the Helper Script

```bash
cd silencio-gym-mms-main
php get_verification_links.php
```

This will extract all verification links from the log file and show you:
- Email addresses
- Verification links
- Link expiry status

### Method 2: Manually Check the Log

```bash
cd silencio-gym-mms-main
Get-Content storage/logs/laravel.log | Select-String -Pattern "Verify Email Address: http" -Context 5
```

Look for lines like:
```
Verify Email Address: http://127.0.0.1:8000/member/verify-email/7/...
```

Copy the link and paste it into your browser to verify the email.

## Email Configuration Options

### Option 1: Log Driver (Current - Recommended for Local Testing)

**Best for:** Local development and testing

**Configuration (.env):**
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Pros:**
- ✅ No external service needed
- ✅ Works offline
- ✅ Fast and reliable
- ✅ Perfect for testing

**Cons:**
- ❌ Emails not sent to real inboxes
- ❌ Need to manually extract verification links from logs

**How to use:**
1. Register a member
2. Run `php get_verification_links.php` to get the verification link
3. Copy and paste the link into your browser
4. Email verified! ✅

---

### Option 2: Mailtrap (Recommended for Testing with Email Preview)

**Best for:** Testing email appearance and content without sending real emails

**Configuration (.env):**
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@silencio-gym.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Setup Steps:**
1. Go to [mailtrap.io](https://mailtrap.io) and create a free account
2. Create an inbox
3. Copy the SMTP credentials
4. Update your `.env` file with the credentials
5. Run `php artisan config:clear`

**Pros:**
- ✅ See emails in a real inbox interface
- ✅ Test email appearance and formatting
- ✅ No risk of sending emails to real users
- ✅ Free tier available

**Cons:**
- ❌ Requires internet connection
- ❌ Requires account setup

---

### Option 3: Gmail SMTP (For Production or Real Testing)

**Best for:** Production or testing with real email delivery

**Configuration (.env):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_gmail@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**Setup Steps:**

1. **Enable 2-Factor Authentication on Gmail:**
   - Go to Google Account → Security
   - Enable 2-Step Verification

2. **Generate App Password:**
   - Go to Google Account → Security → 2-Step Verification
   - Scroll to "App passwords"
   - Select "Mail" and "Windows Computer"
   - Copy the 16-character password

3. **Update .env file:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_gmail@gmail.com
   MAIL_PASSWORD=your_16_char_app_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="your_gmail@gmail.com"
   MAIL_FROM_NAME="Silencio Gym"
   ```

4. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

**Pros:**
- ✅ Real email delivery
- ✅ Free (up to 500 emails/day)
- ✅ Reliable

**Cons:**
- ❌ Requires Gmail account
- ❌ Daily sending limit
- ❌ Not recommended for high-volume production

---

### Option 4: Hostinger SMTP (For Production Deployment)

**Best for:** Production deployment on Hostinger

**Configuration (.env):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Silencio Gym"
```

**Setup Steps:**

1. **Create Email Account in Hostinger:**
   - Log in to Hostinger control panel
   - Go to Email → Email Accounts
   - Create new email: `noreply@yourdomain.com`
   - Set a strong password

2. **Get SMTP Settings:**
   - SMTP Host: `smtp.hostinger.com`
   - SMTP Port: `587`
   - Encryption: `TLS`

3. **Update .env file** with the credentials

4. **Clear config cache:**
   ```bash
   php artisan config:clear
   ```

**Pros:**
- ✅ Professional email address
- ✅ Included with hosting
- ✅ No daily limits
- ✅ Reliable for production

**Cons:**
- ❌ Requires domain and hosting
- ❌ Requires email account setup

---

## Testing Email Configuration

After configuring email, test it:

### 1. Test Email Sending

```bash
cd silencio-gym-mms-main
php artisan tinker
```

Then run:
```php
Mail::raw('Test email from Silencio Gym', function ($message) {
    $message->to('your_email@example.com')
            ->subject('Test Email');
});
```

If successful, you'll see no errors. Check your inbox (or Mailtrap) for the email.

### 2. Test Member Registration

1. Go to `http://127.0.0.1:8000/register`
2. Fill in the registration form
3. Submit
4. Check your email inbox (or log file) for the verification email

### 3. Verify Email Works

Click the verification link in the email. You should see:
- ✅ "Email verified successfully" message
- ✅ Member status changed to "active"
- ✅ Member can now log in

---

## Troubleshooting

### Problem: "Connection refused" or "Connection timeout"

**Solution:**
- Check SMTP host and port are correct
- Verify firewall isn't blocking the port
- Try different port (465 for SSL, 587 for TLS)

### Problem: "Authentication failed"

**Solution:**
- Verify username and password are correct
- For Gmail: Make sure you're using App Password, not regular password
- Check if 2FA is enabled (required for Gmail)

### Problem: Emails going to spam

**Solution:**
- Use a professional email address (not @gmail.com for production)
- Configure SPF and DKIM records in your domain DNS
- Use a reputable SMTP service

### Problem: "No verification email received"

**Solution:**
1. Check if email is being logged:
   ```bash
   php get_verification_links.php
   ```

2. Check Laravel logs for errors:
   ```bash
   Get-Content storage/logs/laravel.log -Tail 50
   ```

3. Verify mail configuration:
   ```bash
   php artisan tinker --execute="echo config('mail.mailers.smtp.host') . PHP_EOL;"
   ```

4. Test SMTP connection:
   ```bash
   php artisan tinker --execute="Mail::raw('Test', function(\$m) { \$m->to('test@example.com')->subject('Test'); });"
   ```

---

## Recommended Setup for Different Environments

### Local Development (Current)
```env
MAIL_MAILER=log
```
✅ Use `php get_verification_links.php` to extract links

### Testing/Staging
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
# ... Mailtrap credentials
```
✅ View emails in Mailtrap inbox

### Production
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
# ... Hostinger credentials
```
✅ Real emails sent to users

---

## Quick Reference

### Check Current Mail Configuration

```bash
php artisan tinker --execute="
echo 'Mail Driver: ' . config('mail.default') . PHP_EOL;
echo 'Mail Host: ' . config('mail.mailers.smtp.host') . PHP_EOL;
echo 'Mail Port: ' . config('mail.mailers.smtp.port') . PHP_EOL;
echo 'Mail From: ' . config('mail.from.address') . PHP_EOL;
"
```

### Clear Configuration Cache

```bash
php artisan config:clear
php artisan cache:clear
```

### View Recent Emails in Log

```bash
php get_verification_links.php
```

### Test Email Sending

```bash
php artisan tinker --execute="
Mail::raw('Test email', function(\$message) {
    \$message->to('test@example.com')->subject('Test');
});
echo 'Email sent!' . PHP_EOL;
"
```

---

## Summary

✅ **Email verification is working correctly!**

The system is currently configured to **log emails** instead of sending them. This is perfect for local development.

**To view verification links:**
```bash
php get_verification_links.php
```

**To send real emails:**
1. Choose an email service (Mailtrap for testing, Gmail/Hostinger for production)
2. Update `.env` with SMTP credentials
3. Run `php artisan config:clear`
4. Test with member registration

**Current setup is fine for testing!** You can manually copy verification links from the log and paste them into your browser to verify member emails.

