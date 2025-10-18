# AUTO-DELETION SYSTEM DEPLOYMENT INSTRUCTIONS 
 
## Files to Upload to VPS (156.67.221.184) 
 
Upload all files in this directory to your VPS, maintaining the directory structure. 
 
## Commands to Run on VPS 
 
```bash 
# 1. Navigate to your Laravel project directory 
cd /path/to/your/laravel/project 
 
# 2. Run migrations 
php artisan migrate 
 
# 3. Test the auto-deletion command 
php artisan members:process-inactive-deletion --dry-run --force -v 
 
# 4. Clear cache 
php artisan config:clear 
php artisan route:clear 
php artisan view:clear 
 
# 5. Test the admin panel 
# Visit: http://156.67.221.184/auto-deletion 
``` 
