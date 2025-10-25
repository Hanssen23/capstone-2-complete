# Hostinger File Manager Deployment - Step by Step

## Overview
This guide shows how to deploy the forced logout fix using Hostinger's File Manager (no SSH needed).

## Prerequisites
- Access to Hostinger control panel
- The `web.php` file from `vps_forced_logout_fix_deploy.zip`

## Step-by-Step Instructions

### Step 1: Log into Hostinger Control Panel
1. Go to https://hpanel.hostinger.com
2. Enter your email and password
3. Click "Sign In"

### Step 2: Open File Manager
1. In the left sidebar, click **"Files"**
2. Click **"File Manager"**
3. You should see your website files

### Step 3: Navigate to Routes Directory
1. In the file browser, navigate to: `/var/www/silencio-gym/routes/`
   - Or: `public_html/routes/` (depending on your setup)
2. You should see the `web.php` file

### Step 4: Upload the New File
1. Click the **"Upload"** button (usually at the top)
2. Select the `web.php` file from `vps_forced_logout_fix_deploy.zip`
3. Click **"Open"** or **"Upload"**
4. When prompted to overwrite, click **"Yes"** or **"Overwrite"**

### Step 5: Verify Upload
1. Refresh the file manager
2. Right-click on `web.php`
3. Click **"Properties"** or **"Details"**
4. Check the modification date - should be today's date

### Step 6: Clear Caches via Terminal
1. In Hostinger control panel, go to **"Advanced"** → **"Terminal"**
   - Or: **"Tools"** → **"Terminal"**
2. Run these commands one by one:

```bash
cd /var/www/silencio-gym
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

3. Wait for each command to complete (you'll see `✓` or similar)

### Step 7: Test the Fix
1. Open a new browser tab
2. Go to http://156.67.221.184/login
3. **Test as Admin:**
   - Username: (your admin email)
   - Password: (your admin password)
   - Click "Login"
   - Navigate to `/membership/plans`
   - Should work without logout ✅

4. **Test as Employee:**
   - Logout first
   - Username: (your employee email)
   - Password: (your employee password)
   - Click "Login"
   - Navigate to `/membership/plans`
   - Should work without logout ✅

## Troubleshooting

### Issue: File upload fails
**Solution:**
1. Check file permissions on the routes directory
2. In File Manager, right-click on `routes` folder
3. Click "Permissions"
4. Set to `755` or `775`
5. Try uploading again

### Issue: Still getting forced logout
**Solution:**
1. Verify the file was uploaded correctly:
   - In File Manager, right-click `web.php`
   - Click "Edit" or "View"
   - First line should say: `<?php`
   - Look for: `// Shared Routes for Admin and Employee`

2. Clear caches again in Terminal:
   ```bash
   cd /var/www/silencio-gym
   php artisan cache:clear
   php artisan route:clear
   php artisan config:clear
   php artisan view:clear
   ```

3. Try in a different browser or incognito mode

### Issue: "Permission denied" error in Terminal
**Solution:**
1. Make sure you're in the correct directory:
   ```bash
   pwd
   ```
   Should show: `/var/www/silencio-gym`

2. If not, navigate there:
   ```bash
   cd /var/www/silencio-gym
   ```

3. Check file permissions:
   ```bash
   ls -la routes/web.php
   ```

4. If needed, fix permissions:
   ```bash
   chmod 644 routes/web.php
   chown www-data:www-data routes/web.php
   ```

## Verification Commands

Run these in Terminal to verify the fix:

```bash
# Check if file was uploaded
head -20 /var/www/silencio-gym/routes/web.php

# Should show:
# <?php
# use Illuminate\Support\Facades\Route;
# ...
# // Shared Routes for Admin and Employee (both can access)

# Check routes are loaded
php artisan route:list | grep membership

# Should show routes like:
# GET|HEAD  /membership/plans
# GET|HEAD  /membership-plans/all
```

## Rollback (If needed)

If something goes wrong:

1. In File Manager, navigate to `/var/www/silencio-gym/routes/`
2. Look for `web.php.backup` (if it exists)
3. Right-click and select "Copy"
4. Paste it in the same directory
5. Rename the copy to `web.php`
6. Delete the old `web.php`
7. Clear caches in Terminal:
   ```bash
   php artisan route:clear && php artisan config:clear && php artisan cache:clear
   ```

## Testing Checklist

After deployment, verify:

- [ ] Admin login → navigate to `/membership/plans` → No logout ✅
- [ ] Employee login → navigate to `/membership/plans` → No logout ✅
- [ ] Employee login → navigate to `/accounts` → No logout ✅
- [ ] Employee login → navigate to `/members` → No logout ✅
- [ ] Admin can access `/rfid-monitor` ✅
- [ ] Employee cannot access `/rfid-monitor` (redirected) ✅
- [ ] No "Session Expired" modal appears ✅

## Support

If you need help:
1. Check the Laravel logs in File Manager:
   - Navigate to `/var/www/silencio-gym/storage/logs/`
   - Open `laravel.log`
   - Look for errors

2. Check the deployment documentation:
   - `DEPLOYMENT_INSTRUCTIONS.md`
   - `FORCED_LOGOUT_FIX_COMPLETE.md`
   - `ROUTE_STRUCTURE_DIAGRAM.md`

## Summary

✅ Upload `web.php` to `/var/www/silencio-gym/routes/`
✅ Clear caches in Terminal
✅ Test the fix
✅ Done!

**Time to complete:** ~5 minutes
**Difficulty:** Easy
**Risk:** Low (can rollback easily)

