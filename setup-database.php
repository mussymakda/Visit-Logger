<?php

echo "🗄️ PHP SQLite Database Setup\n";
echo "============================\n";

// Create database directory
$databaseDir = __DIR__ . '/database';
if (!is_dir($databaseDir)) {
    echo "📁 Creating database directory...\n";
    mkdir($databaseDir, 0755, true);
}

// Create SQLite database file
$databaseFile = $databaseDir . '/database.sqlite';
if (!file_exists($databaseFile)) {
    echo "📋 Creating SQLite database file...\n";
    
    // Create empty file
    file_put_contents($databaseFile, '');
    
    // Set permissions (Unix/Linux only)
    if (function_exists('chmod')) {
        chmod($databaseFile, 0664);
    }
    
    if (file_exists($databaseFile)) {
        echo "✅ SQLite database file created successfully\n";
        echo "File: $databaseFile\n";
        echo "Size: " . filesize($databaseFile) . " bytes\n";
    } else {
        echo "❌ Failed to create SQLite database file\n";
        exit(1);
    }
} else {
    echo "✅ SQLite database file already exists\n";
}

// Update .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "🔧 Updating .env file with production settings...\n";
    
    $envContent = file_get_contents($envFile);
    
    // Set production environment
    $envContent = preg_replace('/APP_ENV=.*/', 'APP_ENV=production', $envContent);
    $envContent = preg_replace('/APP_DEBUG=.*/', 'APP_DEBUG=false', $envContent);
    
    // Set SQLite database configuration
    $envContent = preg_replace('/DB_CONNECTION=.*/', 'DB_CONNECTION=sqlite', $envContent);
    if (strpos($envContent, 'DB_DATABASE=') !== false) {
        $envContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE="database/database.sqlite"', $envContent);
    } else {
        $envContent .= "\nDB_DATABASE=\"database/database.sqlite\"\n";
    }
    
    // Set session configuration for shared hosting
    $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=database', $envContent);
    $envContent = preg_replace('/SESSION_LIFETIME=.*/', 'SESSION_LIFETIME=525600', $envContent);
    $envContent = preg_replace('/SESSION_EXPIRE_ON_CLOSE=.*/', 'SESSION_EXPIRE_ON_CLOSE=false', $envContent);
    
    // Set cache and queue to database
    $envContent = preg_replace('/CACHE_STORE=.*/', 'CACHE_STORE=database', $envContent);
    $envContent = preg_replace('/QUEUE_CONNECTION=.*/', 'QUEUE_CONNECTION=database', $envContent);
    
    // Set mail to log for shared hosting
    $envContent = preg_replace('/MAIL_MAILER=.*/', 'MAIL_MAILER=log', $envContent);
    
    // Set filesystem to local
    $envContent = preg_replace('/FILESYSTEM_DISK=.*/', 'FILESYSTEM_DISK=local', $envContent);
    
    // Set log configuration
    $envContent = preg_replace('/LOG_CHANNEL=.*/', 'LOG_CHANNEL=stack', $envContent);
    $envContent = preg_replace('/LOG_STACK=.*/', 'LOG_STACK=single', $envContent);
    $envContent = preg_replace('/LOG_LEVEL=.*/', 'LOG_LEVEL=error', $envContent);
    
    file_put_contents($envFile, $envContent);
    echo "✅ Updated .env with production configuration\n";
    echo "✅ Database configured: database/database.sqlite\n";
} else {
    echo "⚠️  .env file not found. Creating from example...\n";
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', $envFile);
        echo "✅ Created .env from example\n";
        echo "⚠️  Please configure APP_URL and other settings in .env\n";
    } else {
        echo "❌ .env.example file not found\n";
    }
}

echo "\n✅ Database setup complete!\n";
echo "\n🗄️ Running migrations...\n";

// Run migrations immediately
echo "Running: php artisan migrate --force\n";
$migrateOutput = shell_exec('php artisan migrate --force 2>&1');
echo $migrateOutput;

if (strpos($migrateOutput, 'Migration table created successfully') !== false || 
    strpos($migrateOutput, 'Nothing to migrate') !== false || 
    strpos($migrateOutput, 'Migrated:') !== false) {
    echo "✅ Migrations completed successfully\n";
    
    echo "\n🌱 Seeding database...\n";
    echo "Running: php artisan db:seed --force\n";
    $seedOutput = shell_exec('php artisan db:seed --force 2>&1');
    echo $seedOutput;
    
    if (strpos($seedOutput, 'Database seeding completed successfully') !== false ||
        strpos($seedOutput, 'Seeding:') !== false) {
        echo "✅ Database seeding completed successfully\n";
        
        echo "\n🔐 Admin credentials:\n";
        echo "Email: admin@admin.com\n";
        echo "Password: admin123\n";
        echo "Login URL: " . (isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/admin' : 'your-domain.com/admin') . "\n";
    } else {
        echo "⚠️  Database seeding may have issues. Check output above.\n";
    }
} else {
    echo "❌ Migrations failed. Error output:\n";
    echo $migrateOutput;
}

echo "\n✅ Setup process complete!\n";