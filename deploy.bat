@echo off
echo 🚀 Starting Visit Logger deployment...

REM Copy environment file
if not exist .env (
    echo 📋 Creating .env file from example...
    copy .env.example .env
    
    echo 🔧 Configuring environment settings...
    
    REM Set production environment using PowerShell for better text replacement
    powershell -Command "(Get-Content .env) -replace 'APP_ENV=.*', 'APP_ENV=production' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'APP_DEBUG=.*', 'APP_DEBUG=false' | Set-Content .env"
    
    REM Set SQLite database configuration
    powershell -Command "(Get-Content .env) -replace 'DB_CONNECTION=.*', 'DB_CONNECTION=sqlite' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=.*', 'DB_DATABASE=\"database/database.sqlite\"' | Set-Content .env"
    
    REM Set session configuration for shared hosting
    powershell -Command "(Get-Content .env) -replace 'SESSION_DRIVER=.*', 'SESSION_DRIVER=database' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'SESSION_LIFETIME=.*', 'SESSION_LIFETIME=525600' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'SESSION_EXPIRE_ON_CLOSE=.*', 'SESSION_EXPIRE_ON_CLOSE=false' | Set-Content .env"
    
    REM Set cache and queue to database
    powershell -Command "(Get-Content .env) -replace 'CACHE_STORE=.*', 'CACHE_STORE=database' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'QUEUE_CONNECTION=.*', 'QUEUE_CONNECTION=database' | Set-Content .env"
    
    REM Set mail to log for shared hosting
    powershell -Command "(Get-Content .env) -replace 'MAIL_MAILER=.*', 'MAIL_MAILER=log' | Set-Content .env"
    
    REM Set filesystem to local
    powershell -Command "(Get-Content .env) -replace 'FILESYSTEM_DISK=.*', 'FILESYSTEM_DISK=local' | Set-Content .env"
    
    REM Set log configuration
    powershell -Command "(Get-Content .env) -replace 'LOG_CHANNEL=.*', 'LOG_CHANNEL=stack' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'LOG_STACK=.*', 'LOG_STACK=single' | Set-Content .env"
    powershell -Command "(Get-Content .env) -replace 'LOG_LEVEL=.*', 'LOG_LEVEL=error' | Set-Content .env"
    
    echo ✅ Environment configured for production
    echo ⚠️  Please update the following in .env before continuing:
    echo    - Set APP_URL to your domain (e.g., https://yourdomain.com^)
    echo    - Update APP_NAME if desired
    echo.
    echo Current APP_URL setting:
    findstr "APP_URL=" .env
    echo.
    echo To set your domain, run:
    echo powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=https://yourdomain.com' | Set-Content .env"
    echo.
    echo Then re-run: deploy.bat
    exit /b 1
)

REM Check if APP_URL is still set to default
findstr /C:"APP_URL=http://localhost" .env >nul
if %ERRORLEVEL% equ 0 (
    echo ⚠️  APP_URL is still set to localhost!
    echo Please update APP_URL in .env to your actual domain:
    echo Current setting:
    findstr "APP_URL=" .env
    echo.
    echo To fix this, run:
    echo powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=https://yourdomain.com' | Set-Content .env"
    echo Then re-run: deploy.bat
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

REM Ensure database directory exists
if not exist database (
    echo 📁 Creating database directory...
    mkdir database
)

REM Create SQLite database file
if not exist database\database.sqlite (
    echo 📋 Creating SQLite database file...
    type nul > database\database.sqlite
    echo.
echo ✅ Database file created successfully

REM Run migrations immediately after database creation
echo.
echo 🗄️ Running database migrations...
echo Running: php artisan migrate --force

php artisan migrate --force
if %ERRORLEVEL% equ 0 (
    echo ✅ Migrations completed successfully
    
    echo.
    echo 🌱 Seeding database...
    echo Running: php artisan db:seed --force
    
    php artisan db:seed --force
    if %ERRORLEVEL% equ 0 (
        echo ✅ Database seeding completed successfully
        
        echo.
        echo 🔐 Admin credentials:
        echo Email: admin@admin.com
        echo Password: admin123
        echo Admin URL: %APP_URL%/admin
        echo Designer URL: %APP_URL%/designer
    ) else (
        echo ⚠️  Database seeding may have issues
    )
) else (
    echo ❌ Database migrations failed
)
) else (
    echo ✅ SQLite database file already exists
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
