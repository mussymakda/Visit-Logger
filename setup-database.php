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
    echo "🔧 Updating .env file...\n";
    
    $envContent = file_get_contents($envFile);
    $absolutePath = realpath($databaseFile);
    
    // Update or add DB_DATABASE
    if (strpos($envContent, 'DB_DATABASE=') !== false) {
        $envContent = preg_replace('/DB_DATABASE=.*/', "DB_DATABASE=$absolutePath", $envContent);
    } else {
        $envContent .= "\nDB_DATABASE=$absolutePath\n";
    }
    
    file_put_contents($envFile, $envContent);
    echo "✅ Updated .env with database path: $absolutePath\n";
}

echo "\n✅ Database setup complete!\n";
echo "\nNext steps:\n";
echo "1. php artisan migrate --force\n";
echo "2. php artisan db:seed --force\n";