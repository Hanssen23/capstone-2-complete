# Middleware Fixes - Admin & Employee Forced Logout Issue

## Problem Statement
Admin and employee users were being **forcibly logged out** when navigating to certain pages:
- Admin login → navigates to member plans → **automatically logs out**
- Employee login → navigates to member plans → **automatically logs out**
- Employee login → navigates to accounts → **automatically logs out**

## Root Cause Analysis
The issue was caused by **shared routes being placed inside the admin-only middleware group**:

1. **Member Plans Route** (`/membership/plans`) - Line 119
   - Was inside `Route::middleware(['auth', 'admin.only'])->group()`
   - When employee tried to access it, `AdminOnly` middleware rejected them and logged them out (line 52 in AdminOnly.php)

2. **Accounts Route** (`/accounts`) - Line 152
   - Was inside `Route::middleware(['auth', 'admin.only'])->group()`
   - When employee tried to access it, `AdminOnly` middleware rejected them and logged them out

3. **Members Route** (`/members`) - Line 104
   - Was inside `Route::middleware(['auth', 'admin.only'])->group()`
   - When employee tried to access it, `AdminOnly` middleware rejected them and logged them out

## Solution Implemented

### Changes Made to `routes/web.php`

**BEFORE:** Routes were organized like this:
```
Route::middleware(['auth', 'admin.only'])->group(function () {
    // Members routes (admin only)
    // Membership plans (admin only)
    // Accounts (admin only)
    // Payments (admin only)

    // Employee routes (nested inside admin group)
    Route::prefix('employee')->middleware('employee.only')->group(...)
});
```

**AFTER:** Routes are now organized like this:
```
// Shared routes (both admin and employee can access)
Route::middleware(['auth'])->group(function () {
    // Members routes (shared)
    // Membership plans (shared)
    // Accounts (shared)
    // Payments (shared)
});

// Admin-only routes
Route::middleware(['auth', 'admin.only'])->group(function () {
    // RFID routes (admin only)
    // Auto-deletion (admin only)
});

// Employee-only routes
Route::middleware(['auth', 'employee.only'])->group(function () {
    // Employee dashboard
    // Employee-specific features
});
```

## How This Fixes the Issue

### Route Access Flow
1. **Admin navigates to `/membership/plans`**: ✅ Allowed (passes `auth` middleware)
2. **Employee navigates to `/membership/plans`**: ✅ Allowed (passes `auth` middleware)
3. **Admin navigates to `/accounts`**: ✅ Allowed (passes `auth` middleware)
4. **Employee navigates to `/accounts`**: ✅ Allowed (passes `auth` middleware)
5. **Employee navigates to `/rfid-monitor`**: ✅ Redirected (fails `admin.only` middleware)
6. **Admin navigates to `/employee/dashboard`**: ✅ Redirected (fails `employee.only` middleware)

## Deployment Instructions

### Option 1: Using Batch Script (Windows)
```bash
deploy_middleware_fixes.bat
```

### Option 2: Using Shell Script (Linux/Mac)
```bash
bash deploy_middleware_fixes.sh
```

### Manual Deployment
1. Upload `routes/web.php` to `/var/www/silencio-gym/routes/` on VPS
2. SSH into VPS: `ssh root@156.67.221.184`
3. Run cache clearing commands:
   ```bash
   cd /var/www/silencio-gym
   php artisan route:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

## Testing Checklist - CRITICAL
- [ ] **Admin login → navigate to `/membership/plans` → should NOT logout**
- [ ] **Employee login → navigate to `/membership/plans` → should NOT logout**
- [ ] **Employee login → navigate to `/accounts` → should NOT logout**
- [ ] Admin can access admin-only routes (RFID monitor, auto-deletion)
- [ ] Employee can access employee-only routes (employee dashboard)
- [ ] Admin cannot access employee routes (redirected to admin dashboard)
- [ ] Employee cannot access admin routes (redirected to employee dashboard)
- [ ] Session expired modal should NOT appear during navigation

## Files Modified
- `routes/web.php` - Restructured route groups to separate shared routes from role-specific routes

## Files Created
- `deploy_middleware_fixes.bat` - Windows deployment script
- `deploy_middleware_fixes.sh` - Linux/Mac deployment script
- `MIDDLEWARE_FIXES_SUMMARY.md` - This documentation

## Route Structure Summary

### Shared Routes (Lines 82-137)
- `/members/*` - Member management
- `/membership/plans` - Membership plans
- `/membership-plans/*` - Membership plans API
- `/membership/payments/*` - Payment management
- `/accounts/*` - Account management

### Admin-Only Routes (Lines 139-172)
- `/rfid-monitor` - RFID monitoring
- `/rfid/*` - RFID system control
- `/analytics/rfid-activity` - RFID analytics
- `/auto-deletion/*` - Auto-deletion settings

### Employee-Only Routes (Lines 174-226)
- `/employee/dashboard` - Employee dashboard
- `/employee/members/*` - Employee member management
- `/employee/rfid/*` - Employee RFID control
- `/employee/analytics/*` - Employee analytics
- `/employee/membership/*` - Employee membership management
- `/employee/membership-plans/*` - Employee membership plans API

## Related Middleware Files
- `app/Http/Middleware/AdminOnly.php` - Validates admin role (line 52 logs out non-admins)
- `app/Http/Middleware/EmployeeOnly.php` - Validates employee role (line 69 redirects admins)
- `bootstrap/app.php` - Middleware alias definitions

