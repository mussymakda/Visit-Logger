#!/bin/bash

# QR Code Visit Logger - Linux Server Deployment Script
# This script sets up the Laravel application on a Linux shared hosting server

echo "🚀 Starting QR Code Visit Logger deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}===================================================${NC}"
    echo -e "${BLUE} $1${NC}"
    echo -e "${BLUE}===================================================${NC}"
}

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_error "artisan file not found. Please run this script from the Laravel project root."
    exit 1
fi

print_header "QR Code Visit Logger - Linux Deployment"

# Step 1: Check PHP version
print_status "Checking PHP version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;" 2>/dev/null)
if [ $? -eq 0 ]; then
    print_status "PHP Version: $PHP_VERSION"
    if [ "$(printf '%s\n' "8.1" "$PHP_VERSION" | sort -V | head -n1)" = "8.1" ]; then
        print_status "✅ PHP version is compatible (8.1+)"
    else
        print_warning "⚠️  PHP version might be too old. Recommend PHP 8.1+"
    fi
else
    print_error "PHP not found. Please install PHP 8.1+ first."
    exit 1
fi

# Step 2: Check Composer
print_status "Checking Composer..."
if command -v composer &> /dev/null; then
    print_status "✅ Composer found"
    composer --version
else
    print_error "Composer not found. Please install Composer first."
    print_status "Install Composer: https://getcomposer.org/download/"
    exit 1
fi

# Step 3: Install Dependencies
print_header "Installing Dependencies"
print_status "Running composer install..."
composer install --optimize-autoloader --no-dev

if [ $? -eq 0 ]; then
    print_status "✅ Composer dependencies installed successfully"
else
    print_error "❌ Composer install failed"
    exit 1
fi

# Step 4: Environment Setup
print_header "Environment Configuration"
if [ ! -f ".env" ]; then
    print_status "Creating .env file from .env.example..."
    cp .env.example .env
    print_status "✅ .env file created"
else
    print_warning ".env file already exists, skipping creation"
fi

# Step 5: Generate Application Key
print_status "Generating application key..."
php artisan key:generate --force
print_status "✅ Application key generated"

# Step 6: Create SQLite Database
print_header "Database Setup"
if [ ! -f "database/database.sqlite" ]; then
    print_status "Creating SQLite database file..."
    touch database/database.sqlite
    chmod 664 database/database.sqlite
    print_status "✅ SQLite database file created"
else
    print_warning "SQLite database file already exists"
fi

# Step 7: Run Migrations
print_status "Running database migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_status "✅ Database migrations completed"
else
    print_error "❌ Database migrations failed"
    exit 1
fi

# Step 8: Create Storage Link
print_status "Creating storage symlink..."
php artisan storage:link
print_status "✅ Storage symlink created"

# Step 9: Set Permissions
print_header "Setting Permissions"
print_status "Setting proper permissions..."

# Storage and cache directories
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 664 database/database.sqlite

# If running as www-data or apache user
if command -v chown &> /dev/null; then
    print_status "Setting ownership (if you have sudo access)..."
    echo "Run these commands if you have sudo access:"
    echo "  sudo chown -R www-data:www-data storage bootstrap/cache database/database.sqlite"
    echo "  sudo chmod -R 775 storage bootstrap/cache"
    echo "  sudo chmod 664 database/database.sqlite"
fi

print_status "✅ Permissions set"

# Step 10: Clear Caches
print_header "Clearing Caches"
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_status "✅ Caches cleared"

# Step 11: Optimize for Production
print_header "Production Optimization"
print_status "Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_status "✅ Application optimized"

# Step 12: Create Admin User (if needed)
print_header "Admin User Setup"
print_status "Creating admin user..."
php artisan make:filament-user --name="Admin" --email="admin@visitlogger.com" --password="admin123"
print_status "✅ Admin user created (admin@visitlogger.com / admin123)"

# Final Status
print_header "Deployment Complete! 🎉"
echo ""
print_status "✅ QR Code Visit Logger has been successfully deployed!"
echo ""
echo -e "${BLUE}📋 DEPLOYMENT SUMMARY:${NC}"
echo "   • Application Key: Generated"
echo "   • Database: SQLite (database/database.sqlite)"
echo "   • Storage: Symlinked and permissions set"
echo "   • Caches: Cleared and optimized"
echo "   • Admin User: admin@visitlogger.com / admin123"
echo ""
echo -e "${BLUE}🌐 ACCESS YOUR APPLICATION:${NC}"
echo "   • Admin Panel: http://yoursite.com/admin"
echo "   • Designer Panel: http://yoursite.com/designer"
echo ""
echo -e "${BLUE}⚙️  NEXT STEPS:${NC}"
echo "   1. Update .env file with your domain and settings"
echo "   2. Change admin password after first login"
echo "   3. Configure your web server to point to /public directory"
echo "   4. Ensure your domain points to the public folder"
echo ""
echo -e "${YELLOW}📱 QR Scanner URL Format:${NC}"
echo "   http://yoursite.com/designer?sponsor=SPONSOR_ID"
echo ""
print_status "Deployment script completed successfully!"
echo ""
