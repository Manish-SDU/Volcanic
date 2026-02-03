# Build frontend assets
FROM node:20-alpine AS node-build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

# Install PHP dependencies
FROM composer:2.7.9 AS composer-build
WORKDIR /app
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

# Runtime image
FROM php:8.2-apache

# System deps and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        pkg-config \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libzip-dev \
        sqlite3 \
        libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring zip gd \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy vendor and built assets from build stages
COPY --from=composer-build /app/vendor /var/www/html/vendor
COPY --from=node-build /app/public/build /var/www/html/public/build

# Ensure storage and database paths are writable
RUN mkdir -p storage bootstrap/cache database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
