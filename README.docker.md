# Docker Deployment & Operations Guide

This guide details the Docker configuration, deployment instructions, and operational procedures for this Laravel 8.83.27 application.

---

## 1. Architecture & Resource Sizing

### Architecture Overview
The containerized environment consists of three core isolated services connected via an internal bridge network (`kharisma-internal`):
1. **`web` (`nginx:1.25-alpine`)**:
   - High-performance, lightweight reverse proxy.
   - Serves static assets directly (`public/assets/`, css, js, images) with client-side caching.
   - Forwards PHP requests to `app:9000` via FastCGI.
   - Direct alias routing for uploaded files under `/storage/` to the persistent uploads volume.
   - Exposes HTTP port `80` (or `${APP_PORT:-80}`).

2. **`app` (`php:8.1-fpm-alpine`)**:
   - Runs PHP 8.1 FPM runtime, fully compatible with Laravel 8.83.27 and locked Composer dependencies.
   - Compiled with required extensions: `pdo_mysql`, `bcmath`, `gd`, `zip`, `pcntl`, `redis`, `intl`, `xmlrpc`, and `opcache`.
   - Processes requests from `web` on internal port `9000`.
   - Starts only after the `mysql` service is reported healthy (`condition: service_healthy`).

3. **`mysql` (`mysql:8.0`)**:
   - Official MySQL 8.0 server with `mysql_native_password` authentication and `utf8mb4` charset.
   - Attached to a persistent Docker named volume (`kharisma_sales_mysql_data`).
   - **Internal only**: No host port publishing (not exposed publicly to the internet or host network).
   - Healthcheck monitors `mysqladmin ping`.

### Tuning for 2 vCPU / 2 GB RAM Server
To prevent Out-Of-Memory (OOM) kernel kills on a 2 GB RAM server, all components are tuned for low baseline memory:
- **MySQL (`docker/mysql/my.cnf`)**:
  - `performance_schema = OFF` (saves ~400 MB RAM).
  - `innodb_buffer_pool_size = 128M` (capped memory footprint).
  - `max_connections = 60`.
  - Baseline memory usage: ~150 MB - 200 MB.
- **PHP-FPM (`docker/php/php-fpm.d/zz-docker.conf`)**:
  - `pm = dynamic`, `pm.max_children = 10`, `pm.start_servers = 2`, `pm.min_spare_servers = 2`, `pm.max_spare_servers = 4`.
  - Memory consumption capped at ~500 MB under peak load, ~70 MB idle.
- **OPcache (`docker/php/php.ini`)**:
  - `opcache.memory_consumption = 64` MB.
- **Docker Compose limits (`compose.yaml`)**:
  - `app`: 512 MB hard memory limit.
  - `mysql`: 512 MB hard memory limit.
  - `web`: 128 MB hard memory limit.

---

## 2. Initial Setup & Configuration

### Step 1: Prepare Environment File (`.env`)
Create your production `.env` file from `.env.example` if it does not already exist:
```bash
cp .env.example .env
```

Ensure the following variables are configured in `.env`:
```ini
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:... # Generate using: php artisan key:generate
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_PORT=80

# Database Configuration (matches Compose service name)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=kharisma_sales
DB_USERNAME=sales_user
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_secure_root_password
```

> [!IMPORTANT]
> - Never commit `.env` or hardcode passwords in `compose.yaml`.
> - `DB_HOST` must be set to `mysql` (the Docker Compose internal service name).

### Step 2: Build the Container Images
Build the production images locally:
```bash
docker compose build
```

---

## 3. Starting the Services

To start the core services in the background:
```bash
docker compose up -d
```

Check the status of running containers:
```bash
docker compose ps
```

Verify logs:
```bash
docker compose logs -f
```

---

## 4. Safe Database Operations

### A. Initializing Database Schema (First-Time Setup)
Automatic database migrations are intentionally **disabled** to prevent accidental overwrites or schema destruction.

Once MySQL is running and healthy:
```bash
# Verify database connection from the application container
docker compose exec app php artisan db:monitor

# Run migrations safely
docker compose exec app php artisan migrate --force
```

### B. Safe Database Migration Procedures
Before running any schema migrations on an existing database:
1. Always create a full backup first (see section C).
2. Test migrations in a staging environment.
3. Run the migration command with `--pretend` if you want to inspect SQL queries:
   ```bash
   docker compose exec app php artisan migrate --pretend
   ```
4. Apply the migrations:
   ```bash
   docker compose exec app php artisan migrate --force
   ```

### C. Safe Database Backup Procedure
Because MySQL ports are not exposed publicly, run `mysqldump` directly via `docker compose exec`:

```bash
# Generate a timestamped compressed backup file
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
docker compose exec -T mysql mysqldump \
  -u root -p"${DB_ROOT_PASSWORD}" \
  --single-transaction \
  --quick \
  --routines \
  --triggers \
  "${DB_DATABASE}" | gzip > "backup_${DB_DATABASE}_${TIMESTAMP}.sql.gz"
```

### D. Safe Database Restore Procedure
To restore a backup into the running database:

```bash
# 1. Uncompress backup file (if gzipped)
gunzip -c backup_kharisma_sales_YYYYMMDD_HHMMSS.sql.gz > restore.sql

# 2. Restore into MySQL
docker compose exec -T mysql mysql \
  -u root -p"${DB_ROOT_PASSWORD}" \
  "${DB_DATABASE}" < restore.sql

# 3. Clean up uncompressed SQL file
rm restore.sql
```

---

## 5. Storage & Upload Persistence

Uploaded files, logs, and framework sessions are mounted to dedicated persistent Docker volumes:
- `kharisma_sales_mysql_data` -> `/var/lib/mysql`
- `kharisma_sales_storage_uploads` -> `/var/www/html/storage/app/public`
- `kharisma_sales_storage_logs` -> `/var/www/html/storage/logs`
- `kharisma_sales_storage_framework` -> `/var/www/html/storage/framework`

The container entrypoint automatically validates and creates the symlink:
`/var/www/html/public/storage` -> `/var/www/html/storage/app/public`

If permissions need to be refreshed manually:
```bash
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
docker compose exec app chmod -R 775 storage bootstrap/cache
```

---

## 6. Background Workers & Scheduled Tasks

### Queue Worker
To start an isolated queue worker using the defined `worker` profile:
```bash
docker compose --profile worker up -d
```

### Task Scheduler (Cron)
To run Laravel's scheduled commands (e.g. `sales-kunjungan-absence:cron`):

**Option 1: Docker Scheduler Service**
```bash
docker compose --profile scheduler up -d
```

**Option 2: Host Server Crontab**
Add this entry to your host system's `crontab -e`:
```cron
* * * * * cd /path/to/project && docker compose exec -T app php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Production Caching & Maintenance

For maximum performance in production:
```bash
# Cache configuration, routes, and views
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache

# Clear caches during code deployments
docker compose exec app php artisan optimize:clear
```
