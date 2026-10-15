# Panduan Lengkap Deployment & Operasional (Docker) - Kharisma Sales

Panduan ini berisi langkah-langkah **tuntas dan terperinci** untuk mendeploy aplikasi Laravel **Kharisma Sales** ke VPS baru menggunakan Docker, serta memastikan fitur **live code mirroring** (edit file di host langsung aktif tanpa rebuild) berjalan sempurna.

---

## 1. Arsitektur Layanan

Aplikasi berjalan di atas arsitektur container Docker:
1. **`caddy` (`caddy:2`)**: Reverse proxy utama di port 80 & 443 dengan fitur otomatis penerbitan sertifikat SSL (HTTPS) Let's Encrypt.
2. **`web` (`nginx:1.25-alpine`)**: Web server internal di port `127.0.0.1:8080`, melayani file statis (`public/`) dan meneruskan request PHP ke FastCGI (`app:9000`).
3. **`app` (`php:8.1-fpm-alpine`)**: Runtime PHP 8.1 FPM untuk aplikasi Laravel beserta ekstensi (`pdo_mysql`, `bcmath`, `gd`, `zip`, `xmlrpc`, `opcache`, dll).
4. **`mysql` (`mysql:8.0`)**: Database server terisolasi dalam jaringan internal Docker (`kharisma-internal`) dengan volume persisten.
5. **`worker` & `scheduler` (Opsional)**: Container background queue worker dan scheduler Laravel.

---

## 2. Kebutuhan Minimum VPS Baru

- **Sistem Operasi**: Ubuntu 20.04 / 22.04 / 24.04 LTS (atau Debian 11/12).
- **Spesifikasi Hardware**:
  - Minimum: 2 vCPU, 2 GB RAM + 2 GB Swap.
  - Rekomendasi: 2 vCPU, 4 GB RAM.
- **Port Terbuka (Firewall / Security Group VPS)**:
  - `80/tcp` (HTTP - Caddy)
  - `443/tcp` dan `443/udp` (HTTPS & HTTP/3 - Caddy)
  - `22/tcp` (SSH)

---

## 3. Langkah Demi Langkah Deployment di VPS Baru

### Langkah 1: Persiapan VPS & Install Docker
Masuk ke server via SSH dan install paket dasar serta Docker:

```bash
# Update repository & install paket pendukung
sudo apt-get update && sudo apt-get upgrade -y
sudo apt-get install -y git curl wget ufw ca-certificates gnupg

# Install Docker Engine & Docker Compose Plugin via script resmi
curl -fsSL https://get.docker.com | sudo sh

# Izinkan user saat ini menjalankan docker tanpa sudo (opsional)
sudo usermod -aG docker $USER
newgrp docker

# Aktifkan service docker
sudo systemctl enable --now docker
```

*(Opsional tapi sangat disarankan pada VPS 2 GB RAM: Pasang Swap 2 GB)*:
```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

---

### Langkah 2: Clone Repository
Clone project ke direktori tujuan (misal `/srv/gitlab-kharisma-sales`):

```bash
sudo mkdir -p /srv
cd /srv
sudo git clone <URL_REPOSITORY_GITLAB> gitlab-kharisma-sales
cd /srv/gitlab-kharisma-sales
sudo chown -R $USER:$USER /srv/gitlab-kharisma-sales
```

---

### Langkah 3: Konfigurasi File Environment (`.env`)
Buat file `.env` dari template `.env.example`:

```bash
cp .env.example .env
nano .env
```

Pastikan variabel-variabel kunci berikut diisi dengan benar:

```ini
APP_NAME="Kharisma Sales"
APP_ENV=production
APP_KEY=base64:xxx...  # Generate nanti atau salin dari .env lama
APP_DEBUG=false
APP_URL=https://kharismastationerykupang.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Database Docker
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=kharisma_sales
DB_USERNAME=sales_user
DB_PASSWORD=password_database_rahasia_anda
DB_ROOT_PASSWORD=password_root_rahasia_anda

# Konfigurasi Odoo (Wajib untuk integrasi API Odoo)
ODOO_URL="https://fresh.kharismastationery.com"
ODOO_DB="odoo_fresh"
ODOO_USERNAME="username_odoo"
ODOO_PASSWORD="password_odoo"
```

> [!IMPORTANT]
> - `DB_HOST` **harus** bernilai `mysql` (nama service internal di `compose.yaml`).
> - Variabel `ODOO_*` **wajib** dikonfigurasi agar endpoint integrasi seperti `api/odoo/try` tidak menghasilkan error 500.

---

### Langkah 4: Konfigurasi Domain & SSL ([docker/caddy/Caddyfile](docker/caddy/Caddyfile))
Buka file `docker/caddy/Caddyfile` dan sesuaikan dengan domain Anda:

```caddyfile
http://kharismastationerykupang.com, http://www.kharismastationerykupang.com {
    redir https://{host}{uri} permanent
}

kharismastationerykupang.com, www.kharismastationerykupang.com {
    encode gzip
    reverse_proxy web:80
}
```

> [!NOTE]
> Pastikan DNS Domain (A Record) sudah diarahkan ke alamat IP Publik VPS baru Anda sebelum menjalankan container agar Caddy berhasil menerbitkan sertifikat SSL.

---

### Langkah 5: Build Image Docker
Jalankan proses build container:

```bash
docker compose build app web
```

---

### Langkah 6: Ekstrak Folder `vendor` ke Host (PENTING untuk Live Mirroring)
Karena `compose.yaml` menggunakan volume mount live mirroring (`./:/var/www/html`), folder `vendor` di host **wajib berisi file dependency** yang telah diinstall di container agar tidak menimpa `/var/www/html/vendor` dengan folder kosong:

```bash
# Buat container sementara dan salin direktori vendor ke host
docker create --name temp-app kharisma-sales-app:latest
docker cp temp-app:/var/www/html/vendor ./vendor
docker rm temp-app

# Atur kepemilikan folder vendor
sudo chown -R $USER:$USER ./vendor
```

---

### Langkah 7: Jalankan Semua Container
Jalankan container utama di latar belakang:

```bash
docker compose up -d
```

Periksa status container:
```bash
docker compose ps
```
Pastikan seluruh service (`kharisma-sales-app`, `kharisma-sales-web`, `kharisma-sales-mysql`, `kharisma-sales-caddy`) berstatus `Up` / `Healthy`.

---

### Langkah 8: Generate Application Key (Jika Baru)
Jika `.env` Anda belum memiliki `APP_KEY`, generate kuncinya:
```bash
docker exec -it kharisma-sales-app php artisan key:generate
```

---

### Langkah 9: Restore Database Data
Jika Anda memiliki backup database SQL (misal file `u250108656_d_sales.sql` atau dump database sebelumnya):

```bash
# Tunggu beberapa detik sampai MySQL ready, lalu import dump SQL:
docker exec -i kharisma-sales-mysql mysql -u sales_user -p"password_database_rahasia_anda" kharisma_sales < /path/to/backup_database.sql
```

*(Atau jika memulai dari migrasi kosong)*:
```bash
docker exec -it kharisma-sales-app php artisan migrate --seed
```

---

### Langkah 10: Generate Kunci Passport OAuth2 (KRUSIAL)
Aplikasi menggunakan **Laravel Passport** untuk autentikasi API/Sales. Kunci enkripsi OAuth2 **wajib** dibuat agar tidak muncul error `LogicException: Invalid key supplied`:

```bash
# 1. Generate encryption keys
docker exec -it kharisma-sales-app php artisan passport:keys

# 2. Atur kepemilikan dan hak akses kunci
docker exec -it kharisma-sales-app chown www-data:www-data storage/oauth-private.key storage/oauth-public.key
docker exec -it kharisma-sales-app chmod 660 storage/oauth-private.key storage/oauth-public.key
```

---

### Langkah 11: Setup Storage Symlink & Permissions
Hubungkan direktori storage publik dan pastikan permission storage benar:

```bash
# Buat storage link jika belum ada
docker exec -it kharisma-sales-app php artisan storage:link

# Pastikan permission folder storage dan bootstrap cache dapat ditulis oleh web server
docker exec -it kharisma-sales-app chown -R www-data:www-data storage bootstrap/cache
docker exec -it kharisma-sales-app chmod -R 775 storage bootstrap/cache
```

---

### Langkah 12: Menjalankan Background Worker & Scheduler (Opsional)
Jika aplikasi menggunakan antrian (queue) atau tugas terjadwal (cron schedule):

```bash
# Jalankan container worker dan scheduler dengan profile Docker Compose
docker compose --profile worker --profile scheduler up -d
```

---

## 4. Cara Kerja Live Mirroring (Editing Kode Langsung)

Konfigurasi Docker sudah dioptimalkan agar:
- **Setiap file yang diedit di host VPS** (misal folder `app/`, `routes/`, `resources/`, `public/`) langsung tersinkronisasi ke dalam container secara instan.
- **OPcache timestamp validation diaktifkan** (`opcache.validate_timestamps = 1`), sehingga perubahan logika PHP langsung terbaca tanpa perlu merestart PHP-FPM atau menjalankan `docker compose build`.

---

## 5. Cheat Sheet Perintah Berguna

### Cek Log Realtime
```bash
# Log aplikasi Laravel
docker exec -it kharisma-sales-app tail -f storage/logs/laravel.log

# Log web server Caddy (SSL & Access)
docker compose logs -f caddy

# Log Nginx Web
docker compose logs -f web

# Log PHP-FPM
docker compose logs -f app
```

### Menjalankan Perintah Artisan
```bash
docker exec -it kharisma-sales-app php artisan <command>

# Contoh: Clear cache aplikasi
docker exec -it kharisma-sales-app php artisan optimize:clear
```

### Backup Database Cepat
```bash
docker exec kharisma-sales-mysql mysqldump -u sales_user -p"password_database_rahasia_anda" kharisma_sales > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restart Layanan
```bash
# Restart container aplikasi PHP saja
docker compose restart app

# Restart semua layanan
docker compose restart
```
