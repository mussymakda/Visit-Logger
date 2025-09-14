@echo off
echo 🚀 Starting Visit Logger deployment...

REM Copy environment file
if not exist .env (
    echo 📋 Creating .env file from example...
    copy .env.example .env
    echo ⚠️  Please edit .env file with your server details before continuing!
    echo    - Set APP_URL to your domain
    echo    - Configure database settings
    echo    - Set APP_KEY (use: php artisan key:generate^)
    exit /b 1
)

REM Check if composer is available
where composer >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo ❌ Composer not found. Please install dependencies manually.
    echo    Download composer.phar and run: php composer.phar install --no-dev --optimize-autoloader
    exit /b 1
)

REM Install dependencies
echo 📦 Installing dependencies...
call composer install --no-dev --optimize-autoloader

REM Generate application key if not set
findstr /C:"APP_KEY=base64:" .env >nul
if %ERRORLEVEL% neq 0 (
    echo 🔑 Generating application key...
    php artisan key:generate --force
)

REM Create SQLite database file if it doesn't exist
echo 🗄️  Setting up database...
if not exist database\database.sqlite (
    echo 📋 Creating SQLite database file...
    type nul > database\database.sqlite
)

REM Run database migrations
echo 🗄️  Running database migrations...
php artisan migrate --force

REM Create storage link
echo 🔗 Creating storage link...
php artisan storage:link

REM Clear and cache configuration
echo ⚡ Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

REM Set permissions for shared hosting
echo 🔒 Setting file permissions...
icacls . /reset /T /Q

REM Create admin user if none exists
echo 👤 Checking for admin user...
php artisan tinker --execute="if (App\Models\User::where('role', 'admin')->count() === 0) { App\Models\User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password123'), 'role' => 'admin']); echo 'Admin user created: admin@example.com / password123' . PHP_EOL; echo 'Please change the password after first login!' . PHP_EOL; } else { echo 'Admin user already exists.' . PHP_EOL; }"

echo ✅ Deployment completed successfully!
echo.
echo 📋 Post-deployment checklist:
echo    1. Update .env file with your domain and database details
echo    2. Visit your site to verify it's working
echo    3. Login to /admin with admin@example.com / password123
echo    4. Change the admin password immediately
echo    5. Create interior designer users through the admin panel
echo.
echo 🌐 Your Visit Logger application is ready!
