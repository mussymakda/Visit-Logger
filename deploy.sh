#!/bin/bash

# Visit Logger - Shared Hosting Deployment Script
# Run this script after uploading files to your shared hosting

echo "🚀 Starting Visit Logger deployment..."

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

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

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
php artisan tinker --execute="
if (App\Models\User::where('role', 'admin')->count() === 0) {
    App\Models\User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password123'),
        'role' => 'admin'
    ]);
    echo 'Admin user created: admin@example.com / password123' . PHP_EOL;
    echo 'Please change the password after first login!' . PHP_EOL;
} else {
    echo 'Admin user already exists.' . PHP_EOL;
}
"

echo "✅ Deployment completed successfully!"
echo ""
echo "📋 Post-deployment checklist:"
echo "   1. Update .env file with your domain and database details"
echo "   2. Visit your site to verify it's working"
echo "   3. Login to /admin with admin@example.com / password123"
echo "   4. Change the admin password immediately"
echo "   5. Create interior designer users through the admin panel"
echo ""
echo "🌐 Your Visit Logger application is ready!"
