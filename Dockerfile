# Gunakan image dasar PHP-FPM
FROM php:8.2-fpm

# Set direktori kerja
WORKDIR /var/www/html

# Install dependensi sistem & ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libhiredis-dev \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Copy custom PHP configuration
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Salin semua file proyek dari komputer Anda ke dalam image
COPY . .
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Ubah perizinan
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Jalankan Composer Install DI DALAM kontainer
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
