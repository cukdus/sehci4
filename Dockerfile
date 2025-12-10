FROM composer:2 AS vendor
WORKDIR /app
# Copy only composer files first for better layer caching
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction

FROM php:8.1-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
WORKDIR /var/www/html

# System deps for PHP extensions (GD, ZIP, PDO MySQL)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev \
       libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mysqli pdo pdo_mysql zip \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!Directory /var/www/!Directory ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

# Copy application source
COPY . /var/www/html

# Copy vendor from builder stage
COPY --from=vendor /app/vendor /var/www/html/vendor

# Ensure writable directories for CodeIgniter
RUN mkdir -p public/uploads \
    && chown -R www-data:www-data /var/www/html/writable /var/www/html/public/uploads \
    && find /var/www/html/writable -type d -exec chmod 775 {} \; \
    && find /var/www/html/writable -type f -exec chmod 664 {} \;

EXPOSE 80
CMD ["apache2-foreground"]

