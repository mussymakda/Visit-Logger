#!/bin/bash

# Visit Logger - Shared Hosting Deployment Script
# Run this script after uploading files to your shared hosting

echo "🚀 Starting Visit Logger deployment..."

# Check for required build files
if [ ! -f "public/build/manifest.json" ]; then
    echo "❌ Build files missing! You need to:"
    echo "   1. Run 'npm run build' on your local machine"
    echo "   2. Upload the entire 'public/build/' directory to the server"
    echo "   3. Re-run this deployment script"
    exit 1
fi

echo "✅ Build files found"

# Copy environment file
if [ ! -f .env ]; then
    echo "📋 Creating .env file from example..."
    cp .env.example .env
    echo "⚠️  Please edit .env file with your server details before continuing!"
    echo "   - Set APP_URL to your domain"
    echo "   - Configure database settings"
    echo "   - Set APP_KEY (use: php artisan key:generate)"
    exit 1
fi

# Check if composer is available
if ! command -v composer &> /dev/null; then
    echo "❌ Composer not found. Please install dependencies manually."
    echo "   Download composer.phar and run: php composer.phar install --no-dev --optimize-autoloader"
    exit 1
fi

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Create SQLite database file if it doesn't exist
echo "🗄️  Setting up database..."

# Ensure database directory exists
if [ ! -d "database" ]; then
    echo "📁 Creating database directory..."
    mkdir -p database
    chmod 755 database
fi

# Create SQLite database file
if [ ! -f "database/database.sqlite" ]; then
    echo "📋 Creating SQLite database file..."
    # Try different methods to create the file
    if command -v sqlite3 &> /dev/null; then
        sqlite3 database/database.sqlite "VACUUM;"
    else
        touch database/database.sqlite
    fi
    
    # Set proper permissions
    chmod 664 database/database.sqlite
    
    # Verify file was created
    if [ -f "database/database.sqlite" ]; then
        echo "✅ SQLite database file created successfully"
    else
        echo "❌ Failed to create SQLite database file"
        echo "ℹ️  You may need to create it manually:"
        echo "   mkdir -p database"
        echo "   touch database/database.sqlite"
        echo "   chmod 664 database/database.sqlite"
        exit 1
    fi
else
    echo "✅ SQLite database file already exists"
fi

# Run database migrations
echo "🗄️  Running database migrations..."

# If SQLite file still doesn't exist, try absolute path
if [ ! -f "database/database.sqlite" ]; then
    echo "⚠️  Trying absolute path for SQLite database..."
    CURRENT_DIR=$(pwd)
    export DB_DATABASE="$CURRENT_DIR/database/database.sqlite"
    echo "DB_DATABASE=$DB_DATABASE" >> .env
    
    # Create with absolute path
    touch "$DB_DATABASE"
    chmod 664 "$DB_DATABASE"
fi

php artisan migrate --force

# Seed the database with initial data
echo "🌱 Seeding database with initial data..."
php artisan db:seed --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear and cache configuration
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions for shared hosting
echo "🔒 Setting file permissions..."
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache

# Create admin user if none exists
echo "👤 Checking for admin user..."
php artisan db:seed --class=AdminUserSeeder --force

echo "✅ Deployment completed successfully!"
echo ""
echo "📋 Post-deployment checklist:"
echo "   1. Update .env file with your domain and database details"
echo "   2. Visit your site to verify it's working"
echo "   3. Login to /admin with admin@admin.com / admin123"
echo "   4. Change the admin password immediately"
echo "   5. Create interior designer users through the admin panel"
echo ""
echo "🌐 Your Visit Logger application is ready!"
