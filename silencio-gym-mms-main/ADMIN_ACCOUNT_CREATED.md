# Admin Account Created

## New Admin Account Details

**Email:** `admin@silencio.com`  
**Password:** `admin123`  
**Role:** Admin  
**Status:** Active and Email Verified

## How to Login

1. Go to: `http://127.0.0.1:8000/login`
2. Enter:
   - Email: `admin@silencio.com`
   - Password: `admin123`
3. Click "Log in"
4. You should be redirected to the admin dashboard

## Other Existing Admin Accounts

The database also has these admin accounts:

1. **admin@admin.com** - Admin role
2. **admin@silencio-gym.com** - Admin role  
3. **manager@silencio-gym.com** - Admin role

## Employee Accounts

1. **employee@silencio-gym.com** - Employee role
2. **staff@silencio-gym.com** - Employee role

## Important Notes

- The account is **fully activated** (email_verified_at is set)
- The password is **securely hashed** using bcrypt
- The account has **admin privileges**
- You can change the password after logging in

## If Login Still Fails

### 1. Clear Browser Cache
- Press `Ctrl + Shift + Delete`
- Clear cookies and cached data
- Close all browser tabs
- Open a new tab and try again

### 2. Check Server is Running
Make sure the Laravel server is running:
```bash
cd silencio-gym-mms-main
php artisan serve
```

### 3. Verify Account in Database
Run this command to verify the account exists:
```bash
php artisan tinker --execute="$user = \App\Models\User::where('email', 'admin@silencio.com')->first(); echo 'Email: ' . $user->email . PHP_EOL; echo 'Role: ' . $user->role . PHP_EOL;"
```

### 4. Test Login with Other Accounts
If this account doesn't work, try these:
- `admin@admin.com` (you'll need to reset the password)
- `admin@silencio-gym.com` (you'll need to reset the password)

## Changing the Password

If you want to change the password, run:
```bash
php artisan tinker --execute="$user = \App\Models\User::where('email', 'admin@silencio.com')->first(); $user->password = bcrypt('your_new_password'); $user->save(); echo 'Password updated' . PHP_EOL;"
```

## Creating Additional Admin Accounts

To create more admin accounts, run:
```bash
php artisan tinker --execute="$user = \App\Models\User::create(['name' => 'Your Name', 'first_name' => 'First', 'last_name' => 'Last', 'email' => 'your@email.com', 'password' => bcrypt('your_password'), 'role' => 'admin', 'email_verified_at' => now()]); echo 'Account created: ' . $user->email . PHP_EOL;"
```

## Account Created
**Date:** October 27, 2025  
**Status:** ✅ Active and Ready to Use

## Next Steps

1. **Clear your browser cache** (Ctrl + Shift + Delete)
2. **Go to login page**: http://127.0.0.1:8000/login
3. **Login with**:
   - Email: `admin@silencio.com`
   - Password: `admin123`
4. **You should now be able to access the admin dashboard!**

---

**Note:** Keep this password secure and change it after your first login for better security.

