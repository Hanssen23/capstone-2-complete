# 📋 Manual Upload Checklist for Auto-Deletion System

## 🎯 Quick Fix for 404 Error

The 404 error occurs because the auto-deletion files haven't been uploaded to your VPS yet. Here's how to fix it:

## 📁 Files to Upload

Upload **ALL** files from the `auto_deletion_deployment` folder to your VPS at **156.67.221.184**, maintaining the exact directory structure.

### ✅ Upload Checklist

**New Controllers:**
- [ ] `app/Http/Controllers/AutoDeletionController.php`
- [ ] `app/Http/Controllers/MemberReactivationController.php`

**New Command:**
- [ ] `app/Console/Commands/ProcessInactiveMemberDeletion.php`

**New Middleware:**
- [ ] `app/Http/Middleware/TrackMemberActivity.php`

**New Models:**
- [ ] `app/Models/AutoDeletionSettings.php`
- [ ] `app/Models/MemberDeletionLog.php`

**New Notifications:**
- [ ] `app/Notifications/MemberDeletionWarning.php`
- [ ] `app/Notifications/MemberFinalDeletionWarning.php`

**Database Migrations:**
- [ ] `database/migrations/2025_01_07_000002_add_activity_tracking_to_members_table.php`
- [ ] `database/migrations/2025_01_07_000003_create_member_deletion_logs_table.php`
- [ ] `database/migrations/2025_01_07_000004_create_auto_deletion_settings_table.php`

**Admin Views:**
- [ ] `resources/views/admin/auto-deletion/index.blade.php`
- [ ] `resources/views/admin/auto-deletion/logs.blade.php`

**Member Views:**
- [ ] `resources/views/member/reactivation/form.blade.php`
- [ ] `resources/views/member/reactivation/success.blade.php`

**Updated Files:**
- [ ] `app/Models/Member.php` (updated with activity tracking)
- [ ] `app/Http/Controllers/AuthController.php` (updated with login tracking)
- [ ] `app/Http/Controllers/RfidController.php` (updated with activity tracking)
- [ ] `routes/web.php` (updated with new routes)
- [ ] `bootstrap/app.php` (updated with middleware)
- [ ] `app/Console/Kernel.php` (updated with scheduled task)

## 🚀 Upload Methods

### Option 1: FTP/SFTP Client (Recommended)
1. **Use FileZilla, WinSCP, or similar**
2. **Connect to**: 156.67.221.184
3. **Upload**: All files from `auto_deletion_deployment` folder
4. **Maintain**: Exact directory structure

### Option 2: Command Line (if you have SSH access)
```bash
# Using SCP
scp -r auto_deletion_deployment/* username@156.67.221.184:/path/to/laravel/project/

# Using rsync
rsync -avz auto_deletion_deployment/ username@156.67.221.184:/path/to/laravel/project/
```

### Option 3: Web Hosting Control Panel
1. **Access**: Your hosting control panel
2. **Navigate**: To file manager
3. **Upload**: Files to Laravel project directory
4. **Extract**: If uploaded as ZIP

## ⚡ Quick Commands After Upload

SSH into your VPS and run:

```bash
# Navigate to Laravel project
cd /path/to/your/laravel/project

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Test the system
php artisan members:process-inactive-deletion --dry-run --force -v

# Check if routes are registered
php artisan route:list | grep auto-deletion
```

## 🎯 Verification Steps

After upload and running commands:

1. **Visit**: `http://156.67.221.184/auto-deletion`
   - Should show the admin settings page (not 404)

2. **Check Command**: 
   ```bash
   php artisan list | grep members
   ```
   - Should show: `members:process-inactive-deletion`

3. **Test Dry Run**:
   ```bash
   php artisan members:process-inactive-deletion --dry-run --force -v
   ```
   - Should execute without errors

## 🚨 Common Issues & Solutions

### Still Getting 404?
- **Check**: Files uploaded to correct directory
- **Run**: `php artisan route:clear`
- **Verify**: `routes/web.php` was updated

### Migration Errors?
- **Check**: Database connection
- **Run**: `php artisan migrate:status`
- **Fix**: Any pending migrations

### Command Not Found?
- **Check**: Command file uploaded
- **Run**: `php artisan cache:clear`
- **Verify**: `app/Console/Kernel.php` updated

## 📞 Need Help?

If you're still having issues:

1. **Check Laravel logs**: `storage/logs/laravel.log`
2. **Verify file permissions**: Ensure web server can read files
3. **Test basic Laravel**: Make sure other pages work
4. **Check web server config**: Nginx/Apache configuration

---

**🎯 Goal**: Fix the 404 error by uploading the auto-deletion system files to your VPS.

Once uploaded and migrations run, you'll have access to the full auto-deletion system!
