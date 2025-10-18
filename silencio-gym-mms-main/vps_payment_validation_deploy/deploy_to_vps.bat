@echo off 
echo ======================================== 
echo   VPS PAYMENT VALIDATION DEPLOYMENT 
echo ======================================== 
echo. 
set VPS_HOST=156.67.221.184 
set VPS_USER=root 
set VPS_PATH=/var/www/silencio-gym 
echo. 
echo Step 1: Uploading files to VPS... 
scp "app/Http/Controllers/MembershipController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/ 
scp "app/Http/Controllers/EmployeeController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/ 
scp "web.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/routes/ 
ssh %VPS_USER%@%VPS_HOST% "mkdir -p %VPS_PATH%/resources/views/components" 
scp "resources/views/components/payment-validation-modals.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/components/ 
scp "resources/views/membership/manage-member.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/membership/ 
echo. 
echo Step 2: Running deployment script on VPS... 
ssh %VPS_USER%@%VPS_HOST% "cd ~ && chmod +x deploy_payment_validation.sh && ./deploy_payment_validation.sh" 
echo. 
echo ======================================== 
echo   DEPLOYMENT COMPLETE! 
echo ======================================== 
pause 
