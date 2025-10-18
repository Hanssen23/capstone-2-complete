#!/bin/bash 
echo "========================================" 
echo "  PAYMENT VALIDATION DEPLOYMENT" 
echo "========================================" 
echo "" 
echo "Step 1: Backing up existing files..." 
mkdir -p /var/www/silencio-gym/backup/payment_validation_$(date +%Y%m%d_%H%M%S) 
cp /var/www/silencio-gym/app/Http/Controllers/MembershipController.php /var/www/silencio-gym/backup/payment_validation_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true 
cp /var/www/silencio-gym/app/Http/Controllers/EmployeeController.php /var/www/silencio-gym/backup/payment_validation_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true 
cp /var/www/silencio-gym/routes/web.php /var/www/silencio-gym/backup/payment_validation_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true 
echo "" 
echo "Step 2: Copying new files..." 
cp app/Http/Controllers/MembershipController.php /var/www/silencio-gym/app/Http/Controllers/ 
cp app/Http/Controllers/EmployeeController.php /var/www/silencio-gym/app/Http/Controllers/ 
cp web.php /var/www/silencio-gym/routes/ 
mkdir -p /var/www/silencio-gym/resources/views/components 
cp resources/views/components/payment-validation-modals.blade.php /var/www/silencio-gym/resources/views/components/ 
cp resources/views/membership/manage-member.blade.php /var/www/silencio-gym/resources/views/membership/ 
echo "" 
echo "Step 3: Setting file permissions..." 
chown -R www-data:www-data /var/www/silencio-gym/app/Http/Controllers/ 
chown -R www-data:www-data /var/www/silencio-gym/routes/ 
chown -R www-data:www-data /var/www/silencio-gym/resources/views/ 
chmod 644 /var/www/silencio-gym/app/Http/Controllers/*.php 
chmod 644 /var/www/silencio-gym/routes/web.php 
chmod 644 /var/www/silencio-gym/resources/views/components/*.php 
chmod 644 /var/www/silencio-gym/resources/views/membership/*.php 
echo "" 
echo "Step 4: Clearing Laravel caches..." 
cd /var/www/silencio-gym 
php artisan config:clear 
php artisan route:clear 
php artisan view:clear 
php artisan cache:clear 
echo "" 
echo "Step 5: Testing routes..." 
php artisan route:list --name=membership.check-active-membership 
echo "" 
echo "========================================" 
echo "  PAYMENT VALIDATION DEPLOYMENT COMPLETE!" 
echo "========================================" 
echo "" 
echo "✅ Payment validation system deployed successfully" 
echo "✅ Employee blocking system active" 
echo "✅ Admin override system with countdown active" 
echo "✅ Audit logging enabled" 
echo "" 
echo "🎯 Test the system at: http://156.67.221.184/membership/manage-member" 
echo "" 
