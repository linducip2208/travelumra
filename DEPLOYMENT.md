# Deploy Travel Umrah — Production Setup

Panduan lengkap deployment aplikasi **Travel Umrah** ke server production.

---

## Prasyarat

| Komponen | Versi Minimum |
|----------|--------------|
| PHP | 8.3+ |
| MySQL | 8.0+ (atau MariaDB 10.6+) |
| Composer | 2.5+ |
| Node.js | 18.x / 20.x / 22.x LTS |
| NPM | 9+ |
| Web Server | Nginx 1.20+ (rekomendasi) atau Apache 2.4+ |
| Supervisor | 4.2+ (untuk queue worker & scheduler) |
| Git | 2.30+ |

**Ekstensi PHP wajib diaktifkan:**
```
sudo apt install php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-bcmath php8.3-zip php8.3-intl php8.3-gd \
  php8.3-opcache php8.3-sqlite3
```

---

## Step-by-Step Production Setup

### 1. Clone Repository

```bash
cd /var/www
git clone <repo-url> travelumrah
cd travelumrah
```

### 2. Install Dependencies (Production)

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Setup Environment File

```bash
cp .env.example .env
```

Edit file `.env` dan isi kredensial production:

```ini
APP_NAME="Travel Umrah"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://travelumrah.whitelabel.co.id

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travel_umrah
DB_USERNAME=<db_user_production>
DB_PASSWORD=<db_password_production>

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=<smtp_host>
MAIL_PORT=587
MAIL_USERNAME=<smtp_user>
MAIL_PASSWORD=<smtp_password>
MAIL_FROM_ADDRESS="noreply@travelumrah.whitelabel.co.id"
MAIL_FROM_NAME="${APP_NAME}"

LICENSE_SERVER_URL=https://whitelabel.co.id
LICENSE_DEV_BYPASS=false
LICENSE_HEARTBEAT_INTERVAL=86400
LICENSE_HEARTBEAT_GRACE=604800
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Setup Database

Pastikan database MySQL sudah dibuat:

```sql
CREATE DATABASE travel_umrah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER '<db_user>'@'localhost' IDENTIFIED BY '<password>';
GRANT ALL PRIVILEGES ON travel_umrah.* TO '<db_user>'@'localhost';
FLUSH PRIVILEGES;
```

### 6. Jalankan Migration (TANPA --seed di production)

```bash
php artisan migrate --force
```

> **PENTING:** Jangan jalankan `php artisan migrate --force --seed` di production.
> Seeder menanam akun demo (`admin@travelumrah.com/password`) + Tripay sandbox key.
> Untuk data awal saja:
> ```bash
> php artisan db:seed --class=SettingsSeeder --force
> # atau seeder CMS yang aman, JANGAN DatabaseSeeder penuh
> ```

### 7. Build Frontend Assets

```bash
npm install
npm run build
```

### 8. Set Storage Permission

```bash
# Ownership
sudo chown -R www-data:www-data /var/www/travelumrah

# Write permission untuk storage & cache
sudo chmod -R 775 /var/www/travelumrah/storage
sudo chmod -R 775 /var/www/travelumrah/bootstrap/cache
sudo chmod -R 775 /var/www/travelumrah/public/build

# Pastikan user `www-data` bisa baca semua file
sudo chmod -R o+r /var/www/travelumrah
```

### 9. Konfigurasi Web Server

#### Nginx (Rekomendasi)

Copy file konfigurasi dari `deploy/nginx.conf`:

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/travelumrah
sudo nano /etc/nginx/sites-available/travelumrah
# Sesuaikan `server_name` dengan domain Anda

sudo ln -s /etc/nginx/sites-available/travelumrah /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Apache

Pastikan `mod_rewrite` aktif. Aplikasi sudah menyertakan `.htaccess` di folder `public/`.

```bash
sudo a2enmod rewrite
sudo systemctl reload apache2
```

### 10. Setup Supervisor (Queue Worker saja)

```bash
sudo cp deploy/supervisor.conf /etc/supervisor/conf.d/travelumrah.conf
sudo nano /etc/supervisor/conf.d/travelumrah.conf
# Sesuaikan path dan user

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start travelumrah-queue:*
```

> **PENTING:** Pilih SATU scheduler saja — cron (direkomendasikan). Jangan aktifkan
> `travelumrah-scheduler` supervisor + cron bersamaan (job ganda).

### 11. Setup Cron untuk Scheduler (WAJIB, satu-satunya scheduler)

Tambahkan cron job untuk user `www-data`:

```bash
sudo crontab -u www-data -e
```

Isi dengan:

```
* * * * * cd /var/www/travelumrah && php artisan schedule:run >> /dev/null 2>&1
```

> **Catatan:** Cron ini menjalankan scheduler Laravel setiap menit. Queue worker yang persisten sudah dihandle oleh Supervisor di atas.

### 12. Optimasi Production Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 13. Hardening Checklist (wajib)

```bash
# Pastikan di .env production:
# APP_ENV=production APP_DEBUG=false LICENSE_DEV_BYPASS=false
# SESSION_ENCRYPT=true SESSION_SECURE_COOKIE=true
# Hapus file besar dari repo: rm -f travelumrahweb.zip
# Rotasi Tripay/Midtrans key yang pernah ter-commit di seeder
php artisan about
```

> **Catatan:** Jangan jalankan `route:cache` jika route Anda menggunakan closure. Semua route di project ini sudah menggunakan controller, sehingga aman.

---

## License Pairing

Aplikasi ini menggunakan **License v3** dari [whitelabel.co.id](https://whitelabel.co.id).

Setelah deploy, buka halaman pairing di browser:

```
https://domain-anda.com/__pair
```

1. Masukkan **License Key** yang dibeli dari whitelabel.co.id
2. Wizard akan mem-validasi key ke marketplace
3. Setelah pairing sukses, file `.license.lock` akan tertulis otomatis
4. Reload halaman — aplikasi siap digunakan

**Mode development:** Saat `LICENSE_DEV_BYPASS=true` di `.env`, license pairing dilewati. **Jangan aktifkan di production.**

**Heartbeat:** Aplikasi akan melakukan heartbeat ke license server setiap 24 jam. Jika server unreachable, aplikasi tetap berjalan hingga 7 hari (grace period).

---

## Submit Sitemap ke Google Search Console

1. Pastikan aplikasi sudah jalan di domain production
2. Buka [Google Search Console](https://search.google.com/search-console)
3. Tambahkan property domain Anda (jika belum)
4. Masuk ke menu **Sitemaps**
5. Masukkan URL sitemap: `https://domain-anda.com/sitemap.xml`
6. Klik **Submit**

Sitemap akan otomatis update setiap 24 jam (cached). Sitemap mencakup:
- Halaman statis (`/`, `/docs`)
- Halaman programmatic SEO (`/best-*`, `/alternatives-to-*`, `/compare/*`)
- Halaman paket umrah & destinasi

---

## Verifikasi Deploy

Cek semua komponen berjalan:

```bash
# Status aplikasi
php artisan about

# Status supervisor
sudo supervisorctl status travelumrah-queue:*
sudo supervisorctl status travelumrah-scheduler:*

# Queue jobs
php artisan queue:monitor
```

Pastikan semua checklist:
- [ ] `APP_DEBUG=false` di production
- [ ] `APP_URL` sesuai domain production
- [ ] Database terkoneksi
- [ ] Nginx/Apache serve dari folder `public/`
- [ ] Storage bisa ditulis
- [ ] License pairing berhasil
- [ ] Supervisor menangani queue worker & scheduler
- [ ] Cron scheduler jalan per menit
- [ ] Sitemap ter-submit ke Google Search Console
- [ ] SSL/HTTPS aktif (gunakan Let's Encrypt / Certbot)

---

## Troubleshooting

### 500 Internal Server Error
```bash
# Cek log
tail -f storage/logs/laravel.log
# Permission
sudo chmod -R 775 storage bootstrap/cache
```

### Queue tidak jalan
```bash
sudo supervisorctl restart travelumrah-queue:*
sudo supervisorctl tail travelumrah-queue:*
```

### Scheduler tidak jalan
```bash
# Cek cron
sudo crontab -u www-data -l
# Test manual
php artisan schedule:run
```

### License pairing gagal
```bash
# Cek koneksi ke license server
curl -I https://whitelabel.co.id
# Pastikan public key ada
ls -la public/marketplace.public.pem
```