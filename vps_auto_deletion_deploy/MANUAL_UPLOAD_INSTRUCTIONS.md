# Manual Upload Instructions for Auto-Deletion System

## 🚀 Quick Upload to VPS (156.67.221.184)

### Option 1: Using SCP (Recommended)
```bash
scp -r vps_auto_deletion_deploy/* root@156.67.221.184:~/
ssh root@156.67.221.184
chmod +x ~/deploy_auto_deletion.sh
~/deploy_auto_deletion.sh
```

### Option 2: Using SFTP
```bash
sftp root@156.67.221.184
put -r vps_auto_deletion_deploy/*
quit
ssh root@156.67.221.184
chmod +x ~/deploy_auto_deletion.sh
~/deploy_auto_deletion.sh
```

### Option 3: Manual File Upload
1. Use FileZilla/WinSCP to connect to 156.67.221.184
2. Upload all files from `vps_auto_deletion_deploy` folder
3. SSH into VPS and run: `chmod +x ~/deploy_auto_deletion.sh 

## 🎯 After Deployment

Visit: http://156.67.221.184/auto-deletion
Test: `php artisan members:process-inactive-deletion --dry-run --force -v`

## 🔧 Troubleshooting

If you get permission errors:
```bash
sudo chown -R www-data:www-data /var/www/html/silencio-gym/
sudo chmod -R 755 /var/www/html/silencio-gym/
```

If routes don't work:
```bash
php artisan route:clear
php artisan config:clear
```
