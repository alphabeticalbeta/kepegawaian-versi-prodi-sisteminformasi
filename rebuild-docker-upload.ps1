# Script untuk Rebuild Docker dengan Konfigurasi Upload yang Diperbaiki
# Jalankan script ini setelah mengubah konfigurasi

Write-Host "🔄 Rebuilding Docker containers dengan konfigurasi upload yang diperbaiki..." -ForegroundColor Yellow

# Stop dan remove containers yang ada
Write-Host "⏹️  Stopping existing containers..." -ForegroundColor Blue
docker-compose down

# Remove images untuk memastikan rebuild
Write-Host "🗑️  Removing existing images..." -ForegroundColor Blue
docker-compose rm -f

# Build ulang dengan konfigurasi baru
Write-Host "🔨 Building new images..." -ForegroundColor Blue
docker-compose build --no-cache

# Start containers
Write-Host "🚀 Starting containers..." -ForegroundColor Blue
docker-compose up -d

# Wait for containers to be ready
Write-Host "⏳ Waiting for containers to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Check container status
Write-Host "📊 Container status:" -ForegroundColor Green
docker-compose ps

Write-Host "✅ Docker containers telah di-rebuild dengan konfigurasi upload yang diperbaiki!" -ForegroundColor Green
Write-Host "📝 Konfigurasi yang diubah:" -ForegroundColor Cyan
Write-Host "   - PHP: upload_max_filesize = 100M, post_max_size = 100M" -ForegroundColor White
Write-Host "   - Nginx: client_max_body_size = 100M, timeout = 600s" -ForegroundColor White
Write-Host "   - Memory limit: 512M" -ForegroundColor White
Write-Host ""
Write-Host "🔄 Sekarang coba upload file lagi!" -ForegroundColor Green
