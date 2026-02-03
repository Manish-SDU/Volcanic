#!/bin/sh
set -e

cd /var/www/html

# Ensure .env exists
if [ ! -f /var/www/html/.env ]; then
  if [ -f /var/www/html/.env.example ]; then
    cp /var/www/html/.env.example /var/www/html/.env
  else
    touch /var/www/html/.env
  fi
fi

# Configure Apache to listen on Render's PORT if provided
if [ -n "$PORT" ]; then
  sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
  sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
  if ! grep -q "^ServerName" /etc/apache2/apache2.conf; then
    echo "ServerName localhost" >> /etc/apache2/apache2.conf
  fi
fi

# Create SQLite database if needed
if [ "$DB_CONNECTION" = "sqlite" ]; then
  if [ -z "$DB_DATABASE" ]; then
    export DB_DATABASE=/var/www/html/database/database.sqlite
  fi
  mkdir -p "$(dirname "$DB_DATABASE")"
  if [ ! -f "$DB_DATABASE" ]; then
    touch "$DB_DATABASE"
  fi
  chown -R www-data:www-data "$(dirname "$DB_DATABASE")"
  chmod -R 775 "$(dirname "$DB_DATABASE")"
  chown www-data:www-data "$DB_DATABASE"
  chmod 664 "$DB_DATABASE"
fi

# Ensure Laravel writable directories at runtime
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Optional seeding
if [ "$RUN_SEED" = "true" ]; then
  php artisan db:seed --force
fi

exec "$@"
