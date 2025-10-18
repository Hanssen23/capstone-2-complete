@echo off 
echo ======================================== 
echo   VPS COMPREHENSIVE PAYMENT FIX DEPLOYMENT 
echo ======================================== 
echo. 
set VPS_HOST=156.67.221.184 
set VPS_USER=root 
set VPS_PATH=/var/www/silencio-gym 
echo. 
echo Step 1: Uploading files to VPS... 
scp "app/Http/Controllers/MembershipController.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/app/Http/Controllers/ 
scp "resources/views/membership/manage-member.blade.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/resources/views/membership/ 
scp "test_comprehensive_payment_fix.php" %VPS_USER%@%VPS_HOST%:%VPS_PATH%/ 
echo. 
echo Step 2: Running deployment script on VPS... 
ssh %VPS_USER%@%VPS_HOST% "cd ~ && chmod +x deploy_comprehensive_payment_fix.sh && ./deploy_comprehensive_payment_fix.sh" 
echo. 
echo ======================================== 
echo   DEPLOYMENT COMPLETE! 
echo ======================================== 
pause 
