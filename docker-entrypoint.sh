#!/bin/sh
set -e
APP_DIR="/var/www/html"
if [ -f "$APP_DIR/composer.json" ]; then
  if [ ! -d "$APP_DIR/vendor" ]; then
    composer install --no-dev --prefer-dist --no-progress --no-interaction || true
  fi
fi
if [ -f "$APP_DIR/SEH-APP/composer.json" ]; then
  cd "$APP_DIR/SEH-APP"
  if [ ! -d "vendor" ]; then
    composer install --no-dev --prefer-dist --no-progress --no-interaction || true
  fi
  cd "$APP_DIR"
fi
mkdir -p "$APP_DIR/public/uploads" "$APP_DIR/writable"
if [ -d "$APP_DIR/writable" ]; then
  chown -R www-data:www-data "$APP_DIR/writable"
fi
chown -R www-data:www-data "$APP_DIR/public/uploads"
exec apache2-foreground
