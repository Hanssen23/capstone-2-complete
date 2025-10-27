# Multi-Session Testing Mode

## Overview

The Silencio Gym Management System now supports **multi-session testing mode**, which allows you to login with multiple user accounts (admin, employee, member) simultaneously in different browser tabs. This is extremely useful for testing workflows that involve different user roles.

## Problem Solved

**Before**: When you logged in with an admin account and then tried to login with an employee account in a new tab, the admin session would be overwritten, and you couldn't test both accounts at the same time.

**After**: You can now login with:
- Admin account in Tab 1
- Employee account in Tab 2  
- Member account in Tab 3

All three sessions remain active and independent!

## How It Works

### 1. Separate Session Cookies

Each authentication guard now uses its own session cookie:

- **Web Guard** (Admin/Employee): `silencio_gym_session_web`
- **Member Guard**: `silencio_gym_session_member`

This prevents session conflicts when switching between user types.

### 2. Guard Session Manager Middleware

A new middleware (`GuardSessionManager`) automatically:
- Detects which guard should be used based on the route
- Sets the appropriate session cookie for that guard
- Ensures sessions don't interfere with each other

### 3. Permissive Login Page

The login page is now accessible even when you're already logged in, allowing you to:
- Login with a different account type in a new tab
- Switch between accounts without logging out
- Test multiple user workflows simultaneously

## How to Use Multi-Session Testing

### Step 1: Login with First Account (Admin)

1. Open your browser
2. Go to `http://127.0.0.1:8000/login`
3. Login with admin credentials:
   - Email: `admin@silencio.com`
   - Password: `admin123`
4. You'll be redirected to the admin dashboard

### Step 2: Login with Second Account (Employee) in New Tab

1. **Open a NEW TAB** (Ctrl+T or Cmd+T)
2. Go to `http://127.0.0.1:8000/login`
3. Login with employee credentials:
   - Email: `employee@silencio-gym.com`
   - Password: (employee password)
4. You'll be redirected to the employee dashboard
5. **Both sessions are now active!**

### Step 3: Login with Third Account (Member) in Another Tab

1. **Open another NEW TAB**
2. Go to `http://127.0.0.1:8000/login`
3. Login with member credentials
4. You'll be redirected to the member dashboard
5. **All three sessions are now active!**

### Step 4: Switch Between Tabs

- **Tab 1**: Admin dashboard - fully functional
- **Tab 2**: Employee dashboard - fully functional
- **Tab 3**: Member dashboard - fully functional

You can switch between tabs and all sessions remain active!

## Important Notes

### Session Isolation

- **Web Guard Sessions** (admin/employee) are isolated from **Member Guard Sessions**
- Within the same guard, only one session is active at a time
- Example: You can have 1 admin + 1 member logged in, but not 2 admins simultaneously

### Session Switching

When you login with a different account in the same guard:
- The previous session for that guard is automatically logged out
- Example: If you're logged in as admin and login as employee in the same tab, the admin session ends

### Cross-Contamination Prevention

The system prevents cross-contamination by:
1. Using separate session cookies for each guard
2. Automatically logging out from the opposite guard when switching
3. Isolating session data by guard type

## Testing Scenarios

### Scenario 1: Admin + Member Testing

**Use Case**: Test how admin actions affect member experience

1. **Tab 1**: Login as admin
2. **Tab 2**: Login as member
3. **Tab 1**: Create a new membership plan
4. **Tab 2**: Refresh - see the new plan available
5. Both sessions remain active throughout

### Scenario 2: Employee + Member Testing

**Use Case**: Test employee workflows and member interactions

1. **Tab 1**: Login as employee
2. **Tab 2**: Login as member
3. **Tab 1**: Process a payment for the member
4. **Tab 2**: Refresh - see payment reflected
5. Both sessions remain active

### Scenario 3: Admin + Employee Comparison

**Use Case**: Compare admin vs employee permissions

1. **Tab 1**: Login as admin
2. **Tab 2**: Login as employee
3. Compare what features are available in each dashboard
4. Test permission differences

## Technical Implementation

### Files Modified

1. **config/session.php**
   - Added `guard_cookies` configuration for separate session cookies

2. **app/Http/Middleware/GuardSessionManager.php** (NEW)
   - Manages guard-specific session cookies
   - Automatically detects and sets the correct cookie

3. **app/Http/Middleware/RedirectIfAuthenticated.php**
   - Updated to allow login page access when authenticated
   - Enables multi-session testing mode

4. **app/Http/Controllers/AuthController.php**
   - Updated `showLogin()` to always show login page
   - Updated `login()` to handle guard switching properly

5. **routes/web.php**
   - Removed guest middleware from login routes
   - Allows authenticated users to access login page

6. **bootstrap/app.php**
   - Registered `GuardSessionManager` middleware
   - Applied to web routes for automatic guard detection

### Session Cookie Configuration

```php
'guard_cookies' => [
    'web' => 'silencio_gym_session_web',
    'member' => 'silencio_gym_session_member',
],
```

### Middleware Stack

```
Request → GuardSessionManager → StartSession → Auth → Controller
          ↓
    Detects guard type
    Sets correct session cookie
```

## Troubleshooting

### Issue: Sessions Still Conflicting

**Solution**:
1. Clear all browser cookies for localhost:8000
2. Close all browser tabs
3. Clear Laravel caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan session:clear
   ```
4. Restart the server
5. Try again with fresh tabs

### Issue: Login Page Redirects to Dashboard

**Solution**:
- This shouldn't happen anymore with multi-session mode
- If it does, check that the `RedirectIfAuthenticated` middleware is updated
- Verify that login routes don't have `guest` middleware

### Issue: Can't See Multiple Sessions

**Solution**:
- Make sure you're using **different browser tabs**, not the same tab
- Check browser developer tools → Application → Cookies
- You should see both `silencio_gym_session_web` and `silencio_gym_session_member` cookies

### Issue: Session Expires Quickly

**Solution**:
- Check `SESSION_LIFETIME` in `.env` (default: 120 minutes)
- Increase if needed: `SESSION_LIFETIME=240` (4 hours)
- Clear config cache: `php artisan config:clear`

## Disabling Multi-Session Mode (Production)

For production environments, you may want to disable multi-session mode:

### Option 1: Re-enable Guest Middleware

In `routes/web.php`, wrap login routes with guest middleware:

```php
Route::middleware(['guest:web,member'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
```

### Option 2: Update RedirectIfAuthenticated

In `app/Http/Middleware/RedirectIfAuthenticated.php`, remove the login page exception:

```php
public function handle(Request $request, Closure $next, string ...$guards): Response
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            // Redirect logic...
        }
    }

    return $next($request);
}
```

## Security Considerations

### Development vs Production

- **Development**: Multi-session mode is perfect for testing
- **Production**: Consider disabling to prevent confusion

### Session Security

- Each guard still has its own authentication
- Sessions are still encrypted and secure
- CSRF protection remains active
- Session hijacking protection is maintained

### Best Practices

1. **Use multi-session mode only for testing**
2. **Don't share session cookies between users**
3. **Always logout when done testing**
4. **Clear sessions regularly during development**

## Benefits

✅ **Faster Testing**: No need to logout/login repeatedly
✅ **Better Workflow Testing**: Test interactions between user types
✅ **Improved Development Speed**: See changes from multiple perspectives
✅ **Easier Debugging**: Compare behavior across user roles
✅ **No Session Conflicts**: Guards are properly isolated

## Date Implemented

October 27, 2025

## Status

✅ **ACTIVE** - Multi-session testing mode is enabled and ready to use!

## Quick Reference

### Available Test Accounts

1. **Admin**: `admin@silencio.com` / `admin123`
2. **Admin**: `admin@admin.com` / (password)
3. **Admin**: `admin@silencio-gym.com` / (password)
4. **Employee**: `employee@silencio-gym.com` / (password)
5. **Employee**: `staff@silencio-gym.com` / (password)

### Testing Workflow

```
1. Open Tab 1 → Login as Admin → Test admin features
2. Open Tab 2 → Login as Employee → Test employee features  
3. Open Tab 3 → Login as Member → Test member features
4. Switch between tabs → All sessions active
5. Test interactions between user types
```

### Commands

```bash
# Clear all caches
php artisan config:clear && php artisan cache:clear && php artisan route:clear

# Clear sessions
php artisan tinker --execute="DB::table('sessions')->truncate();"

# Restart server
php artisan serve
```

---

**Happy Testing! 🎉**

