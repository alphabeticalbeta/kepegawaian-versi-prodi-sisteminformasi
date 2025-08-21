#!/bin/bash

# Script untuk memperbaiki permission Laravel yang berulang
# Author: AI Assistant
# Date: 2025-08-18

echo "🔧 Fixing Laravel Permissions..."
echo "=================================="

# Fix storage directory ownership
echo "📁 Fixing storage directory ownership..."
docker exec -it laravel-app chown -R www-data:www-data /var/www/html/storage

# Fix bootstrap/cache directory ownership
echo "📁 Fixing bootstrap/cache directory ownership..."
docker exec -it laravel-app chown -R www-data:www-data /var/www/html/bootstrap/cache

# Fix storage directory permissions
echo "🔐 Fixing storage directory permissions..."
docker exec -it laravel-app chmod -R 775 /var/www/html/storage

# Fix bootstrap/cache directory permissions
echo "🔐 Fixing bootstrap/cache directory permissions..."
docker exec -it laravel-app chmod -R 775 /var/www/html/bootstrap/cache

# Clear Laravel caches
echo "🧹 Clearing Laravel caches..."
docker exec -it laravel-app php artisan view:clear
docker exec -it laravel-app php artisan cache:clear
docker exec -it laravel-app php artisan config:clear
docker exec -it laravel-app php artisan route:clear

# Verify specific problematic files
echo "🔍 Checking for problematic files..."
docker exec -it laravel-app find /var/www/html/storage -user root -ls

echo ""
echo "✅ Permission fix completed!"
echo "🚀 Laravel should now work without permission errors."
echo ""
echo "💡 If you still get permission errors, run this script again."
