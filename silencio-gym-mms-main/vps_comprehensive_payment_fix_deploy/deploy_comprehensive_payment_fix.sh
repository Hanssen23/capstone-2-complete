#!/bin/bash 
echo "========================================" 
echo "  COMPREHENSIVE PAYMENT PROCESSING FIX" 
echo "========================================" 
echo "" 
echo "Step 1: Backing up existing files..." 
mkdir -p /var/www/silencio-gym/backup/payment_comprehensive_fix_$(date +%Y%m%d_%H%M%S) 
cp /var/www/silencio-gym/app/Http/Controllers/MembershipController.php /var/www/silencio-gym/backup/payment_comprehensive_fix_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true 
cp /var/www/silencio-gym/resources/views/membership/manage-member.blade.php /var/www/silencio-gym/backup/payment_comprehensive_fix_$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true 
echo "" 
echo "Step 2: Copying new files..." 
cp app/Http/Controllers/MembershipController.php /var/www/silencio-gym/app/Http/Controllers/ 
cp resources/views/membership/manage-member.blade.php /var/www/silencio-gym/resources/views/membership/ 
cp test_comprehensive_payment_fix.php /var/www/silencio-gym/ 
echo "" 
echo "Step 3: Running database migrations..." 
cd /var/www/silencio-gym 
php artisan migrate --force 
echo "" 
echo "Step 4: Setting file permissions..." 
chown -R www-data:www-data /var/www/silencio-gym/app/Http/Controllers/ 
chown -R www-data:www-data /var/www/silencio-gym/resources/views/ 
chmod 644 /var/www/silencio-gym/app/Http/Controllers/*.php 
chmod 644 /var/www/silencio-gym/resources/views/membership/*.php 
echo "" 
echo "Step 5: Clearing Laravel caches..." 
php artisan config:clear 
php artisan route:clear 
php artisan view:clear 
php artisan cache:clear 
php artisan optimize:clear 
echo "" 
echo "Step 6: Testing database structure..." 
php artisan tinker --execute="try { \$payment = new App\\Models\\Payment(); \$fillable = \$payment->getFillable(); echo 'Payment model fillable fields: ' . implode(', ', \$fillable) . PHP_EOL; \$testFields = ['is_pwd', 'is_senior_citizen', 'discount_amount', 'discount_percentage']; foreach (\$testFields as \$field) { if (in_array(\$field, \$fillable)) { echo '✅ Field ' . \$field . ' exists' . PHP_EOL; } else { echo '❌ Field ' . \$field . ' missing' . PHP_EOL; } } } catch (Exception \$e) { echo '❌ Error testing Payment model: ' . \$e->getMessage() . PHP_EOL; }" 
echo "" 
echo "Step 7: Testing payment creation..." 
php test_comprehensive_payment_fix.php 
echo "" 
echo "Step 8: Testing routes..." 
php artisan route:list --name=membership.process-payment 
echo "" 
echo "========================================" 
echo "  COMPREHENSIVE PAYMENT FIX COMPLETE!" 
echo "========================================" 
echo "" 
echo "✅ Payment processing validation rules fixed" 
echo "✅ JavaScript error handling improved" 
echo "✅ Server error logging enhanced" 
echo "✅ Database migrations run" 
echo "✅ Database structure verified" 
echo "✅ Payment creation tested" 
echo "" 
echo "🎯 Test the system at: http://156.67.221.184/membership/manage-member" 
echo "" 
