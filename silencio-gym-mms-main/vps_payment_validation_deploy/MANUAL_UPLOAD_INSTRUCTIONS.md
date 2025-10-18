# Payment Validation System - Manual Upload Instructions 
 
## Files to Upload: 
 
1. **app/Http/Controllers/MembershipController.php** → `/var/www/silencio-gym/app/Http/Controllers/` 
2. **app/Http/Controllers/EmployeeController.php** → `/var/www/silencio-gym/app/Http/Controllers/` 
3. **web.php** → `/var/www/silencio-gym/routes/` 
4. **resources/views/components/payment-validation-modals.blade.php** → `/var/www/silencio-gym/resources/views/components/` 
5. **resources/views/membership/manage-member.blade.php** → `/var/www/silencio-gym/resources/views/membership/` 
 
## After Upload Commands: 
 
```bash 
# SSH into your VPS 
ssh root@156.67.221.184 
 
# Navigate to Laravel directory 
cd /var/www/silencio-gym 
 
# Clear caches 
php artisan config:clear 
php artisan route:clear 
php artisan view:clear 
php artisan cache:clear 
 
# Test the routes 
php artisan route:list --name=membership.check-active-membership 
``` 
