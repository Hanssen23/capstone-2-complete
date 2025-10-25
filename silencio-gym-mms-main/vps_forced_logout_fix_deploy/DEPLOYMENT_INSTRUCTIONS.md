# VPS Deployment Instructions - Forced Logout Fix

## Quick Summary
This package contains the fixed `routes/web.php` file that resolves the forced logout issue when admin and employee users navigate between pages.

## What's Included
- `routes/web.php` - Fixed routes file
- `FORCED_LOGOUT_FIX_COMPLETE.md` - Complete fix summary
- `ROUTE_STRUCTURE_DIAGRAM.md` - Visual route structure
- `MIDDLEWARE_FIXES_SUMMARY.md` - Detailed documentation

## Deployment Method 1: Using File Manager (Easiest)

### Step 1: Upload the File
1. Log into your hosting control panel (Hostinger, cPanel, etc.)
2. Open File Manager
3. Navigate to `/var/www/silencio-gym/routes/`
4. Upload the `web.php` file from this package
5. **Confirm to overwrite** the existing file

### Step 2: Clear Caches
1. In your hosting control panel, open Terminal or SSH Console
2. Run these commands:
```bash
cd /var/www/silencio-gym
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Step 3: Verify
1. Visit http://156.67.221.184/login
2. Login as admin
3. Navigate to `/membership/plans` - should NOT logout ✅
4. Login as employee
5. Navigate to `/membership/plans` - should NOT logout ✅

## Deployment Method 2: Using SSH (If you have SSH access)

```bash
# 1. Connect to VPS
ssh root@156.67.221.184

# 2. Navigate to app directory
cd /var/www/silencio-gym

# 3. Backup current routes file
cp routes/web.php routes/web.php.backup

# 4. Upload the new file (from your local machine in a new terminal)
scp routes/web.php root@156.67.221.184:/var/www/silencio-gym/routes/

# 5. Clear caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Deployment Method 3: Using Git (If Git is set up)

```bash
# 1. SSH into VPS
ssh root@156.67.221.184

# 2. Navigate to app directory
cd /var/www/silencio-gym

# 3. Pull the latest changes
git pull origin main

# 4. Clear caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Troubleshooting

### Issue: "Permission denied" when uploading
**Solution:** 
- Make sure the file permissions are correct
- Run: `chmod 644 /var/www/silencio-gym/routes/web.php`
- Run: `chown www-data:www-data /var/www/silencio-gym/routes/web.php`

### Issue: Still getting forced logout after deployment
**Solution:**
1. Verify the file was uploaded correctly:
   ```bash
   head -20 /var/www/silencio-gym/routes/web.php
   ```
   Should show: `// Shared Routes for Admin and Employee (both can access)`

2. Clear all caches again:
   ```bash
   php artisan cache:clear
   php artisan route:clear
   php artisan config:clear
   php artisan view:clear
   ```

3. Check Laravel logs:
   ```bash
   tail -f /var/www/silencio-gym/storage/logs/laravel.log
   ```

### Issue: "Class not found" or "Route not found" errors
**Solution:**
- Run: `php artisan route:list` to verify routes are loaded
- Run: `php artisan config:cache` to rebuild config cache

## Testing Checklist

After deployment, test these scenarios:

### Admin User
- [ ] Login as admin
- [ ] Navigate to `/membership/plans` → Should work ✅
- [ ] Navigate to `/accounts` → Should work ✅
- [ ] Navigate to `/members` → Should work ✅
- [ ] Navigate to `/rfid-monitor` → Should work ✅

### Employee User
- [ ] Login as employee
- [ ] Navigate to `/membership/plans` → Should work ✅
- [ ] Navigate to `/accounts` → Should work ✅
- [ ] Navigate to `/members` → Should work ✅
- [ ] Navigate to `/rfid-monitor` → Should redirect to employee dashboard ✅
- [ ] Navigate to `/employee/dashboard` → Should work ✅

### Session
- [ ] Refresh page → Should stay logged in ✅
- [ ] Navigate between pages → Should stay logged in ✅
- [ ] No "Session Expired" modal should appear ✅

## Rollback (If needed)

If something goes wrong, you can rollback:

```bash
cd /var/www/silencio-gym

# Restore backup
cp routes/web.php.backup routes/web.php

# Clear caches
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Support

If you need help:
1. Check the Laravel logs: `tail -f /var/www/silencio-gym/storage/logs/laravel.log`
2. Verify the file was uploaded: `cat /var/www/silencio-gym/routes/web.php | head -20`
3. Check route list: `php artisan route:list | grep membership`

## What Changed

The fix restructures routes into three groups:
1. **Shared routes** (auth only) - Both admin and employee can access
2. **Admin-only routes** (auth + admin.only) - Only admin can access
3. **Employee-only routes** (auth + employee.only) - Only employee can access

This prevents forced logout when accessing shared routes like `/membership/plans` and `/accounts`.

For detailed information, see `FORCED_LOGOUT_FIX_COMPLETE.md` and `ROUTE_STRUCTURE_DIAGRAM.md`.

