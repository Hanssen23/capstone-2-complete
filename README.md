# Silencio Gym Management System - Complete Capstone Project

A comprehensive gym management system built with Laravel, featuring member management, payment processing, RFID attendance tracking, and automated member lifecycle management.

## 🎯 Features

### Core Functionality
- **Unified Login System** - Single login page for all user types (Admin, Employee, Member)
- **Role-Based Access Control** - Different dashboards and permissions for each role
- **Member Management** - Complete member lifecycle from registration to deletion
- **Payment Processing** - Membership payments with receipt generation
- **RFID Integration** - Automated attendance tracking with RFID cards
- **Auto-Deletion System** - Automated inactive member management with email notifications

### User Roles

#### Admin
- Full system access
- Account management (create/edit admin and employee accounts)
- Member management
- Payment processing and history
- RFID monitoring
- Auto-deletion settings
- Analytics and reports

#### Employee
- Member management
- Payment processing
- RFID monitoring
- Limited account access (can edit own profile)
- Analytics viewing

#### Member
- Self-registration
- Profile management
- Membership plan viewing
- Payment history
- Email verification
- Password reset

## 🚀 Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- MySQL/SQLite
- Node.js & NPM
- Python 3.x (for RFID reader)

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/Hanssen23/capstone-2-complete.git
cd capstone-2-complete/silencio-gym-mms-main
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
```

4. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=silencio_gym
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Seed database (optional)**
```bash
php artisan db:seed
```

8. **Start development server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 📁 Project Structure

```
silencio-gym-mms-main/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Http/
│   │   ├── Controllers/           # Application controllers
│   │   └── Middleware/            # Custom middleware
│   ├── Models/                    # Eloquent models
│   └── Notifications/             # Email notifications
├── database/
│   ├── migrations/                # Database migrations
│   └── seeders/                   # Database seeders
├── resources/
│   └── views/                     # Blade templates
├── routes/
│   ├── web.php                    # Web routes
│   └── api.php                    # API routes
├── public/                        # Public assets
└── deploy_to_vps.bat             # Deployment script
```

## 🔧 Configuration

### Email Configuration
Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="Silencio Gym"
```

### RFID Configuration
Edit `rfid_config.json`:
```json
{
    "server_url": "http://your-server-ip",
    "api_endpoint": "/rfid/tap",
    "reader_port": "COM3"
}
```

## 📝 Key Files

### Deployment Scripts
- `deploy_to_vps.bat` - Deploy to VPS server
- `deploy_to_vps.ps1` - PowerShell deployment script
- `simple_rfid_reader.py` - RFID reader script

### Important Controllers
- `AuthController.php` - Unified authentication
- `AccountController.php` - User account management
- `MemberController.php` - Member management
- `PaymentController.php` - Payment processing
- `RfidController.php` - RFID attendance tracking
- `AutoDeletionController.php` - Automated member deletion

### Key Views
- `login.blade.php` - Unified login page
- `accounts.blade.php` - Admin account management
- `employee/accounts.blade.php` - Employee profile
- `members/register.blade.php` - Member registration
- `membership/manage-member.blade.php` - Payment processing

## 🌐 Deployment

### VPS Deployment

1. **Configure VPS details**
Edit `deploy_to_vps.bat` or `deploy_to_vps.ps1` with your VPS credentials

2. **Run deployment script**
```bash
./deploy_to_vps.bat
```

3. **On VPS, run migrations**
```bash
cd /var/www/silencio-gym
php artisan migrate --force
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

## 📊 Database Schema

### Main Tables
- `users` - Admin and employee accounts
- `members` - Gym members
- `payments` - Payment records
- `membership_plans` - Available membership plans
- `rfid_logs` - RFID tap logs
- `active_sessions` - Current gym sessions
- `member_deletion_logs` - Auto-deletion history
- `auto_deletion_settings` - Auto-deletion configuration

## 🔐 Default Credentials

After seeding, default admin account:
- Email: `admin@silencio.com`
- Password: `admin123`

**⚠️ Change these credentials immediately in production!**

## 🛠️ Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Clear Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
```

## 📱 Features in Detail

### Unified Login
- Single login page at `/login`
- Automatic role detection
- Redirects to appropriate dashboard
- Session management

### Account Management
- Admin can create admin/employee accounts
- Personal info fields: First Name, Middle Name, Last Name, Age, Gender
- Email verification for activation
- Mobile number with +63 prefix

### Payment System
- Multiple membership plans
- Discount support (PWD, Senior)
- Receipt generation
- Payment history
- CSV export

### RFID System
- Automatic tap-in/tap-out
- Real-time monitoring
- Session tracking
- Python-based reader integration

### Auto-Deletion
- Configurable inactivity period
- Email warnings before deletion
- Reactivation links
- Deletion logs

## 🤝 Contributing

This is a capstone project. For educational purposes only.

## 📄 License

This project is for academic purposes.

## 👥 Authors

- **Hanssen23** - *Initial work* - [GitHub](https://github.com/Hanssen23)

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Python RFID libraries
- All contributors and testers

