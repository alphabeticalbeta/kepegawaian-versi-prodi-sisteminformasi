# PowerShell Script untuk Fix Laravel Permissions di Docker
# Author: AI Assistant
# Date: 2025-08-24

Write-Host "🔧 Fixing Laravel Permissions in Docker..." -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green

# Fix storage directory ownership
Write-Host "📁 Fixing storage directory ownership..." -ForegroundColor Yellow
docker-compose exec app chown -R www-data:www-data /var/www/html/storage

# Fix bootstrap/cache directory ownership
Write-Host "📁 Fixing bootstrap/cache directory ownership..." -ForegroundColor Yellow
docker-compose exec app chown -R www-data:www-data /var/www/html/bootstrap/cache

# Fix storage directory permissions
Write-Host "🔐 Fixing storage directory permissions..." -ForegroundColor Yellow
docker-compose exec app chmod -R 775 /var/www/html/storage

# Fix bootstrap/cache directory permissions
Write-Host "🔐 Fixing bootstrap/cache directory permissions..." -ForegroundColor Yellow
docker-compose exec app chmod -R 775 /var/www/html/bootstrap/cache

# Clear Laravel caches
Write-Host "🧹 Clearing Laravel caches..." -ForegroundColor Yellow
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear

# Restart containers
Write-Host "🔄 Restarting containers..." -ForegroundColor Yellow
docker-compose restart app nginx

Write-Host ""
Write-Host "✅ Permission fix completed!" -ForegroundColor Green
Write-Host "🚀 Laravel should now work without permission errors." -ForegroundColor Green
Write-Host ""
Write-Host "💡 If you still get permission errors, run this script again." -ForegroundColor Cyan
