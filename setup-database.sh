#!/bin/bash

# Manual SQLite Database Setup Script
# Use this if the main deployment script fails to create the database

echo "🗄️ Manual SQLite Database Setup"
echo "================================"

# Get current directory
CURRENT_DIR=$(pwd)
echo "Working directory: $CURRENT_DIR"

# Create database directory
echo "📁 Creating database directory..."
mkdir -p database
chmod 755 database

# Create SQLite database file
echo "📋 Creating SQLite database file..."

# Method 1: Using touch
touch database/database.sqlite
chmod 664 database/database.sqlite

# Method 2: Using sqlite3 if available
if command -v sqlite3 &> /dev/null; then
    echo "🔧 Initializing with sqlite3..."
    sqlite3 database/database.sqlite "PRAGMA journal_mode=WAL;"
fi

# Verify file creation
if [ -f "database/database.sqlite" ]; then
    echo "✅ SQLite database file created successfully"
    ls -la database/database.sqlite
else
    echo "❌ Failed to create SQLite database file"
    echo ""
    echo "Manual steps:"
    echo "1. mkdir -p database"
    echo "2. touch database/database.sqlite"
    echo "3. chmod 664 database/database.sqlite"
    exit 1
fi

# Update .env with absolute path if needed
echo "🔧 Updating .env file..."
if ! grep -q "DB_DATABASE=" .env; then
    echo "DB_CONNECTION=sqlite" >> .env
    echo "DB_DATABASE=$CURRENT_DIR/database/database.sqlite" >> .env
else
    # Update existing DB_DATABASE line
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=$CURRENT_DIR/database/database.sqlite|" .env
fi

echo ""
echo "✅ Database setup complete!"
echo ""
echo "🗄️ Running migrations..."

# Run migrations immediately
echo "Running: php artisan migrate --force"
if php artisan migrate --force; then
    echo "✅ Migrations completed successfully"
    
    echo ""
    echo "🌱 Seeding database..."
    echo "Running: php artisan db:seed --force"
    if php artisan db:seed --force; then
        echo "✅ Database seeding completed successfully"
        
        echo ""
        echo "🔐 Admin credentials:"
        echo "Email: admin@admin.com"
        echo "Password: admin123"
        echo "Login URL: your-domain.com/admin"
    else
        echo "⚠️  Database seeding may have issues"
    fi
else
    echo "❌ Migrations failed"
fi

echo ""
echo "✅ Setup process complete!"