# QR Code Visit Logger - Linux Server Deployment Guide

## 🚀 Quick Start

This Laravel application is ready for instant deployment on any Linux shared hosting server with PHP 8.1+ and Composer.

### Prerequisites
- Linux shared hosting server
- PHP 8.1 or higher
- Composer installed
- Git access (optional)

### One-Command Deployment

```bash
# Clone and deploy
git clone https://github.com/yourusername/visit-logger.git
cd visit-logger
chmod +x deploy-linux.sh
./deploy-linux.sh
```

### Manual Deployment

If you don't have Git access:

1. **Upload Files**: Upload all project files to your server
2. **Run Deployment Script**:
   ```bash
   chmod +x deploy-linux.sh
   ./deploy-linux.sh
   ```

## 📋 What the Deployment Script Does

1. ✅ Checks PHP version (requires 8.1+)
2. ✅ Installs Composer dependencies
3. ✅ Creates `.env` file from `.env.example`
4. ✅ Generates Laravel application key
5. ✅ Creates SQLite database file
6. ✅ Runs database migrations
7. ✅ Creates storage symlinks
8. ✅ Sets proper file permissions
9. ✅ Clears and optimizes caches
10. ✅ Creates default admin user

## 🌐 Server Configuration

### Web Server Setup

Point your domain to the `/public` directory. If your hosting provider requires `public_html`, create a symlink:

```bash
ln -s /path/to/your/project/public /path/to/public_html
```

### Domain Configuration

Update your `.env` file:
```env
APP_URL=https://yourdomain.com
```

### File Permissions

The deployment script handles permissions, but if you need to set them manually:

```bash
chmod -R 775 storage bootstrap/cache
chmod 664 database/database.sqlite
chown -R www-data:www-data storage bootstrap/cache database/database.sqlite
```

## 🔐 Security Setup

### Change Default Admin Password

1. Login with default credentials:
   - Email: `admin@visitlogger.com`
   - Password: `admin123`

2. **IMMEDIATELY** change the password after first login

### Environment Variables

Update these in your `.env` file:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (SQLite - no changes needed)
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/your/project/database/database.sqlite

# Mail Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="QR Code Visit Logger"
```

## 🎯 Application Access

### Admin Panel
- URL: `https://yourdomain.com/admin`
- Features: Manage sponsors, designers, view reports, export data

### Designer Panel  
- URL: `https://yourdomain.com/designer`
- Features: QR scanner, visit history

### QR Code Format
Generated QR codes contain: `https://yourdomain.com/designer?sponsor=123`

## 📊 Features Overview

### Admin Panel Features
- **Sponsor Management**: Add, edit, view sponsors with auto QR generation
- **Designer Management**: Manage interior designer accounts
- **Visit Tracking**: View all visits with photos and timestamps
- **Reports**: Three comprehensive reports with Excel export
  - Sponsor Report: All sponsors with visitor counts
  - Designer Report: All designers with visit statistics
  - All Visits Report: Complete chronological visit log

### Designer Panel Features
- **QR Scanner**: Mobile-optimized camera scanner
- **Photo Capture**: Automatic photo capture after successful scan
- **Visit History**: Personal visit history

### Mobile Features
- **Responsive Design**: Works on all devices
- **Camera Access**: Rear camera preferred for QR scanning
- **Offline Capability**: Basic functionality when offline

## 🛠️ Maintenance

### Regular Tasks

```bash
# Clear caches (if needed)
php artisan cache:clear
php artisan view:clear

# Update application (future updates)
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup Database

```bash
# Create backup
cp database/database.sqlite database/backup-$(date +%Y%m%d).sqlite

# Restore backup
cp database/backup-20241201.sqlite database/database.sqlite
```

### Log Monitoring

```bash
# View application logs
tail -f storage/logs/laravel.log

# View web server logs (location varies by hosting)
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
```

## 🆘 Troubleshooting

### Common Issues

**Permission Errors**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chmod 664 database/database.sqlite
```

**Database Issues**
```bash
# Recreate database
rm database/database.sqlite
touch database/database.sqlite
chmod 664 database/database.sqlite
php artisan migrate --force
```

**Cache Issues**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

**500 Internal Server Error**
- Check file permissions
- Verify `.env` file exists
- Check error logs
- Ensure database file exists and is writable

### Support

For technical support:
1. Check application logs: `storage/logs/laravel.log`
2. Verify file permissions
3. Test with deployment script again
4. Contact your hosting provider for server-specific issues

## 🔄 Updates

To update the application:

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run any new migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Quick Reference

**Default Admin Login**: admin@visitlogger.com / admin123  
**Admin Panel**: /admin  
**Designer Panel**: /designer  
**Database**: SQLite (database/database.sqlite)  
**Photos**: storage/app/public/visit-photos/  
**Logs**: storage/logs/laravel.log  

**Important**: Change default admin password immediately after deployment!
