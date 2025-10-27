# Silencio Gym Management System

A comprehensive gym management system with RFID integration, member management, attendance tracking, and payment processing.

## 🚀 Features

### Core Features
- **Member Management**: Complete CRUD operations for gym members
- **RFID Integration**: Automated check-in/check-out using RFID cards
- **Attendance Tracking**: Real-time attendance monitoring and history
- **Payment Processing**: Membership payments and billing management
- **Member Profiles**: Detailed member profiles with activity history
- **Dashboard**: Real-time statistics and overview

### Technical Features
- **Laravel 12**: Modern PHP framework with latest features
- **SQLite Database**: Lightweight, file-based database
- **Tailwind CSS**: Modern, responsive UI design
- **Python Integration**: RFID reader automation
- **Real-time Monitoring**: Live RFID activity tracking
- **Error Handling**: Comprehensive error management
- **Performance Optimized**: Database indexes and query optimization

## 📋 Requirements

### System Requirements
- PHP 8.2 or higher
- Python 3.13 (for RFID integration)
- Composer
- Node.js & npm
- Laravel Herd (recommended) or XAMPP/WAMP

### Python Dependencies
- pyscard==2.0.3
- requests==2.31.0

### Hardware Requirements
- ACR122U NFC Reader (or compatible)
- ACS Unified Driver

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/lucasram20/silencio-gym-copy.git
cd silencio-gym-copy

# Checkout the latest development branch with fixes
git checkout nightly
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed
```

### 6. Build Frontend Assets
```bash
npm run build
```

### 7. Python Setup
```bash
# Install Python dependencies
pip install -r requirements.txt
```

### 8. RFID Reader Setup
1. Install ACS Unified Driver
2. Connect ACR122U reader
3. Test with `python test_rfid_reader.py`

## 🔧 Configuration

### Environment Variables (.env)
```env
APP_NAME="Silencio Gym"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://silencio-gym-mms-main.test

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

RFID_DEVICE_ID=main_reader
```

### Python Configuration
Update `rfid_reader.py` with your API URL:
```python
API_URL = "http://silencio-gym-mms-main.test"
```

## 🚀 Usage

### Starting the Application
```bash
# Using Laravel Herd (recommended)
herd start

# Or using Laravel's built-in server
php artisan serve
```

### Access the Application
- **URL**: http://silencio-gym-mms-main.test
- **Admin Login**: admin@admin.com / admin123

### RFID Reader Management
```bash
# Start RFID reader
php artisan rfid:reader start

# Stop RFID reader
php artisan rfid:reader stop

# Check status
php artisan rfid:reader status

# Restart RFID reader
php artisan rfid:reader restart
```

### Manual RFID Reader
```bash
# Run the batch file
auto_start_rfid.bat

# Or run Python script directly
python rfid_reader.py --api http://silencio-gym-mms-main.test
```

## 📊 Database Schema

### Core Tables
- **members**: Member information and details
- **attendances**: Check-in/check-out records
- **payments**: Payment transactions
- **rfid_logs**: RFID activity logs
- **active_sessions**: Current active sessions
- **membership_periods**: Membership history

### Key Relationships
- Members have many attendances, payments, and sessions
- RFID logs track all card interactions
- Membership periods track plan changes

## 🔍 Troubleshooting

### Common Issues

#### 1. RFID Reader Not Working
```bash
# Check Python installation
python --version

# Verify dependencies
pip list | grep pyscard

# Test reader connection
python test_rfid_reader.py
```

#### 2. Database Issues
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Recreate database
php artisan migrate:fresh --seed
```

#### 3. Performance Issues
```bash
# Optimize application
php artisan optimize

# Clear compiled views
php artisan view:clear
```

#### 4. Permission Issues
```bash
# Set proper permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Error Logs
- **Laravel Logs**: `storage/logs/laravel.log`
- **RFID Logs**: `storage/logs/rfid_reader.log`
- **Application Logs**: Check Laravel's log viewer

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter MemberProfileTest
```

### Test Coverage
- Member profile functionality
- RFID integration
- Payment processing
- Attendance tracking

## 🔒 Security

### Authentication
- Session-based authentication
- CSRF protection
- Input validation
- SQL injection prevention

### Data Protection
- Encrypted sensitive data
- Secure password hashing
- Input sanitization
- XSS protection

## 📈 Performance Optimization

### Database Optimization
- Indexed frequently queried columns
- Optimized queries with eager loading
- Pagination for large datasets
- Query result caching

### Application Optimization
- Route caching
- Configuration caching
- View compilation
- Asset minification

## 🔄 Maintenance

### Regular Tasks
```bash
# Clear old logs
php artisan log:clear

# Optimize database
php artisan optimize

# Update dependencies
composer update
npm update
```

### Backup
```bash
# Backup database
cp database/database.sqlite backup/database_$(date +%Y%m%d).sqlite

# Backup logs
tar -czf logs_backup_$(date +%Y%m%d).tar.gz storage/logs/
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Check the troubleshooting section
- Review error logs
- Create an issue on GitHub
- Contact the development team

## 🔄 Updates

### Version History
- **v1.0.0**: Initial release with core features
- **v1.1.0**: Added member profiles and RFID integration
- **v1.2.0**: Performance optimizations and error handling

### Upcoming Features
- Mobile app integration
- Advanced reporting
- Email notifications
- Multi-location support
