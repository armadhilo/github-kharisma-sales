#!/bin/sh
set -e

# Ensure storage and bootstrap/cache directory structure exists
mkdir -p /var/www/html/storage/app/public \
         /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/testing \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Fix permissions for web server user (www-data)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create public storage symlink if missing
if [ ! -L /var/www/html/public/storage ] && [ ! -d /var/www/html/public/storage ]; then
    echo "Creating public storage symlink..."
    php artisan storage:link || true
fi

# Execute main container process
exec "$@"
