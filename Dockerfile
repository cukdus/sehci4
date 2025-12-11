FROM php:8.2-apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
WORKDIR /var/www/html

# System deps for PHP extensions (GD, ZIP, PDO MySQL)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev \
       libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd mysqli pdo pdo_mysql zip intl \
    && a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!Directory /var/www/!Directory ${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

# Provide composer inside the image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application source
COPY . /var/www/html

# Install PHP dependencies if composer.json is present
RUN if command -v composer >/dev/null 2>&1; then \
      if [ -f composer.json ]; then \
        composer install --no-dev --prefer-dist --no-progress --no-interaction || true; \
      elif [ -f SEH-APP/composer.json ]; then \
        cd SEH-APP && composer install --no-dev --prefer-dist --no-progress --no-interaction || true; \
      else \
        echo "composer.json not found, skipping vendor install"; \
      fi; \
    else \
      echo "composer not available, skipping vendor install"; \
    fi

# Ensure writable directories for CodeIgniter (guard if missing)
RUN mkdir -p public/uploads writable \
    && chown -R www-data:www-data /var/www/html/public/uploads \
    && if [ -d /var/www/html/writable ]; then \
         chown -R www-data:www-data /var/www/html/writable && \
         find /var/www/html/writable -type d -exec chmod 775 {} \; && \
         find /var/www/html/writable -type f -exec chmod 664 {} \; ; \
       fi \
    && if [ -d /var/www/html/SEH-APP/writable ]; then \
         chown -R www-data:www-data /var/www/html/SEH-APP/writable && \
         find /var/www/html/SEH-APP/writable -type d -exec chmod 775 {} \; && \
         find /var/www/html/SEH-APP/writable -type f -exec chmod 664 {} \; ; \
       fi

EXPOSE 80
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
