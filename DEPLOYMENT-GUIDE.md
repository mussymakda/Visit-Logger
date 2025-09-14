# Visit Logger - Deployment Guide

A comprehensive guide for deploying the Visit Logger Laravel application to production serv3. **🔑 Application Key**
   - Generates `APP_KEY` if not set
   - Ensures application encryption is secure

4. **🗄️ Database Setup**
   - Creates SQLite database file if it doesn't exist
   - Runs `php artisan migrate --force`
   - Seeds database with initial data
   - Creates all necessary database tables

5. **🔗 Storage Configuration**# 🎯 Table of Contents

1. [Server Requirements](#server-requirements)
2. [Pre-Deployment Setup](#pre-deployment-setup)
3. [Deployment Methods](#deployment-methods)
4. [Using the Deployment Script](#using-the-deployment-script)
5. [Manual Deployment](#manual-deployment)
6. [Post-Deployment Configuration](#post-deployment-configuration)
7. [Creating Admin Users](#creating-admin-users)
8. [Troubleshooting](#troubleshooting)

---

## 📋 Server Requirements

### Minimum Requirements
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Database**: SQLite (included) or MySQL 8.0+
- **Node.js**: 18+ (for building assets)
- **Web Server**: Apache/Nginx with mod_rewrite
- **Storage**: 500MB free space
- **Memory**: 512MB RAM minimum

### PHP Extensions Required
```bash
php-sqlite3     # For SQLite database
php-gd          # For image processing
php-curl        # For external API calls
php-zip         # For file operations
php-xml         # For Laravel
php-mbstring    # For string handling
php-openssl     # For encryption
php-json        # For JSON handling
php-fileinfo    # For file operations
```

---

## 🚀 Pre-Deployment Setup

### 1. Prepare Your Local Environment

```bash
# Clone the repository
git clone https://github.com/mussymakda/Visit-Logger.git
cd Visit-Logger

# Install dependencies
composer install
npm install

# Build production assets
npm run build

# Ensure you have the latest code
git pull origin master
```

### 2. Verify Build Files

Make sure these files exist before deployment:
- `public/build/manifest.json`
- `public/build/assets/` directory with CSS and JS files

---

## 🛠️ Deployment Methods

### Method 1: Automatic Deployment Script (Recommended)

The easiest way to deploy using the included deployment script.

### Method 2: cPanel Git Deployment

If your hosting supports Git deployment via cPanel.

### Method 3: Manual File Upload

Traditional FTP/File Manager upload method.

---

## 🎯 Using the Deployment Script

### Step 1: Upload Files

Upload **all files** from your local project to your server, including:
- All PHP files and directories
- `public/build/` directory (with built assets)
- `deploy.sh` and `deploy.bat` scripts
- `.env.example` file

### Step 2: Run the Deployment Script

**For Linux/Unix servers:**
```bash
cd /path/to/your/application
chmod +x deploy.sh
./deploy.sh
```

**For Windows servers:**
```batch
cd C:\path\to\your\application
deploy.bat
```

### Step 3: What the Script Does Automatically

The deployment script performs these actions:

1. **✅ Checks for Build Files**
   - Verifies `public/build/manifest.json` exists
   - Ensures frontend assets are properly built

2. **📋 Environment Setup**
   - Creates `.env` from `.env.example` if needed
   - Prompts you to configure environment variables

3. **📦 Install Dependencies**
   - Runs `composer install --no-dev --optimize-autoloader`
   - Installs only production dependencies

4. **🔑 Application Key**
   - Generates `APP_KEY` if not set
   - Ensures application encryption is secure

5. **🗄️ Database Setup**
   - Runs `php artisan migrate --force`
   - Creates all necessary database tables

6. **🔗 Storage Configuration**
   - Creates storage symlink with `php artisan storage:link`
   - Links public storage for file uploads

7. **⚡ Performance Optimization**
   - Caches configuration: `php artisan config:cache`
   - Caches routes: `php artisan route:cache`
   - Caches views: `php artisan view:cache`

8. **🔒 File Permissions**
   - Sets correct file permissions (644 for files, 755 for directories)
   - Makes storage and cache directories writable (775)

9. **👤 Admin User Creation**
   - Creates default admin user if none exists
   - Email: `admin@admin.com`
   - Password: `admin123`

---

## 🔧 Manual Deployment

If you prefer to deploy manually or the script doesn't work:

### Step 1: Upload Files
```bash
# Upload all files via FTP or file manager
# Ensure you include the public/build/ directory
```

### Step 2: Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
# Edit .env file with your settings
php artisan key:generate --force
```

### Step 4: Database Setup
```bash
# Create SQLite database file
touch database/database.sqlite
chmod 664 database/database.sqlite

# Run migrations and seeders
php artisan migrate --force
php artisan db:seed --force
```

### Step 5: Storage and Optimization
```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

---

## ⚙️ Post-Deployment Configuration

### 1. Update .env File

Edit your `.env` file with production settings:

```env
APP_NAME="Visit Logger"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (SQLite - default)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# OR MySQL if preferred
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Session settings
SESSION_DRIVER=database
SESSION_LIFETIME=525600
```

### 2. Verify Application Access

- **Main App**: `https://yourdomain.com`
- **Admin Panel**: `https://yourdomain.com/admin`
- **Designer Panel**: `https://yourdomain.com/designer`

### 3. Test Core Functionality

1. **Admin Login**: Use default credentials to access admin panel
2. **Create Sponsors**: Add sponsors and verify QR generation
3. **Download QR Codes**: Test QR image and PDF downloads
4. **Designer Access**: Create designer users and test QR scanning

---

## 👤 Creating Admin Users

### Method 1: Using Seeder (Recommended)
```bash
php artisan db:seed --class=AdminUserSeeder
```

### Method 2: Using Artisan Command
```bash
php artisan make:filament-user
```

### Method 3: Default Credentials (if script ran)
- **Email**: `admin@admin.com`
- **Password**: `admin123`
- **⚠️ Change this password immediately after login!**

---

## 🐛 Troubleshooting

### Common Issues and Solutions

#### ❌ "Database file does not exist" Error
**Problem**: SQLite database file not created
**Solution**:
```bash
# Create the database file manually
touch database/database.sqlite
chmod 664 database/database.sqlite
php artisan migrate --force
php artisan db:seed --force
```

#### ❌ "Build files missing" Error
**Problem**: Frontend assets not built
**Solution**:
```bash
# On your local machine:
npm install
npm run build
# Re-upload the public/build/ directory
```

#### ❌ "Class 'SQLite3' not found"
**Problem**: SQLite extension not installed
**Solution**:
```bash
# Enable php-sqlite3 extension
# OR switch to MySQL database
```

#### ❌ "Permission denied" Errors
**Problem**: Incorrect file permissions
**Solution**:
```bash
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

#### ❌ "Storage link failed"
**Problem**: Public directory structure
**Solution**:
```bash
# Manually create symlink
ln -sf /path/to/storage/app/public /path/to/public/storage
```

#### ❌ Admin Login Not Working
**Problem**: Admin user doesn't exist or wrong password
**Solution**:
```bash
# Create new admin user
php artisan db:seed --class=AdminUserSeeder
```

#### ❌ QR Codes Not Generating
**Problem**: External QR service not accessible
**Solution**:
- Check internet connectivity
- Verify `allow_url_fopen` is enabled in PHP
- Test QR service URL manually

#### ❌ 500 Internal Server Error
**Problem**: Various configuration issues
**Solution**:
```bash
# Check error logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Debug Mode (Temporary)

If you need to debug issues, temporarily enable debug mode:

```env
# In .env file
APP_DEBUG=true
APP_ENV=local
```

**⚠️ Don't forget to disable debug mode in production!**

---

## 🔄 Updating the Application

### Method 1: Using Git (if supported)
```bash
git pull origin master
composer install --no-dev --optimize-autoloader
npm run build  # If frontend changes
php artisan migrate --force
php artisan config:cache
```

### Method 2: Manual Update
1. Download latest code
2. Build assets locally: `npm run build`
3. Upload changed files
4. Run deployment script again

---

## 📞 Support

If you encounter issues not covered in this guide:

1. **Check Laravel Logs**: `storage/logs/laravel.log`
2. **Verify Server Requirements**: Ensure all PHP extensions are installed
3. **Test Locally**: Confirm the application works in local environment
4. **Check File Permissions**: Ensure proper read/write permissions

---

## 📋 Deployment Checklist

- [ ] Server meets minimum requirements
- [ ] Built frontend assets (`npm run build`)
- [ ] Uploaded all application files
- [ ] Run deployment script or manual steps
- [ ] Updated `.env` with production settings
- [ ] Verified admin panel access
- [ ] Changed default admin password
- [ ] Created interior designer users
- [ ] Tested QR code generation and scanning
- [ ] Tested file uploads and downloads
- [ ] Verified all functionality works

---

**🎉 Congratulations! Your Visit Logger application is now deployed and ready for use!**
