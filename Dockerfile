# Production-ready PHP-FPM image for KaajAsse
FROM php:8.2-fpm

# Install system deps and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libzip-dev \
        zip \
        unzip \
        git \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql mbstring xml zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install composer (copy from official composer image)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer manifests first to leverage Docker cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader || true

# Copy application code
COPY . /var/www/html

# Ensure www-data owns files
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
