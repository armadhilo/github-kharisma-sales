# ==============================================================================
# Stage 1: Base PHP Runtime
# ==============================================================================
FROM php:8.1-fpm-alpine AS base

# Install system runtime dependencies
RUN apk add --no-cache \
    bash \
    curl \
    netcat-openbsd

# Install PHP extension installer helper
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/

# Install PHP extensions required by Laravel 8.83 and project dependencies:
# - pdo_mysql: MySQL database connection
# - bcmath: Arbitrary precision math (Passport/phpseclib)
# - gd: Image processing (DomPDF / PhpSpreadsheet)
# - zip: Zip archive handling (PhpSpreadsheet / Flysystem)
# - pcntl: Process control (Artisan queues/workers)
# - redis: Redis client support
# - intl: Internationalization
# - xmlrpc: Ripcord XML-RPC client for Odoo/ERP sync
RUN install-php-extensions \
    pdo_mysql \
    bcmath \
    gd \
    zip \
    pcntl \
    redis \
    intl \
    xmlrpc

# Copy PHP and PHP-FPM configurations tuned for 2 vCPU and 2 GB RAM
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-custom.ini
COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html

# ==============================================================================
# Stage 2: Composer Dependencies Builder
# Note: config/app.php hardcodes Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider,
# so dev packages from composer.lock are installed without --no-dev to prevent
# fatal runtime crashes without modifying existing application code.
# ==============================================================================
FROM base AS vendor

# Bring Composer CLI binary into the PHP 8.1 environment
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy Composer manifests
COPY composer.json composer.lock ./

# Install locked dependencies from composer.lock
RUN composer install \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# ==============================================================================
# Stage 3: Production Application Image (PHP-FPM)
# ==============================================================================
FROM base AS app

WORKDIR /var/www/html

# Copy vendor dependencies from vendor build stage
COPY --from=vendor /app/vendor ./vendor

# Copy application code
COPY . .

# Generate optimized autoloader using Composer binary
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer
RUN composer dump-autoload --optimize --classmap-authoritative \
    && rm /usr/bin/composer

# Copy container entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Prepare storage and bootstrap cache directories with correct permissions
RUN mkdir -p storage/app/public \
             storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/testing \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

# ==============================================================================
# Stage 4: Production Web Server (Nginx)
# ==============================================================================
FROM nginx:1.25-alpine AS web

# Copy Nginx web server configuration
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

# Copy public static assets from application image
COPY --from=app /var/www/html/public /var/www/html/public

WORKDIR /var/www/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
