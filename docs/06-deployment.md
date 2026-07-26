# LeaderHub — Deployment Guide

> **Version**: 1.0 | **Status**: Draft | **Last Updated**: 2026-07-26
> **Target**: cPanel VPS (utama) + aaPanel (alternatif) + Dev lokal (Docker)

## 1. Environment Requirements

### 1.1 Server (Production — cPanel VPS)
| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| OS | CloudLinux / AlmaLinux 8+ | AlmaLinux 9 |
| RAM | 4 GB | 8 GB+ |
| Disk | 40 GB | 80 GB+ SSD |
| CPU | 2 core | 4 core+ |
| cPanel | Latest | — |

### 1.2 Software
| Komponen | Versi |
|----------|-------|
| cPanel | Latest |
| PHP | 8.2+ (8.3 disarankan) |
| MySQL | 8.0 (atau MariaDB 10.6+) |
| Apache/LiteSpeed | Apache 2.4+ / LiteSpeed |
| Redis | 6.x (opsional, untuk cache) |
| Composer | 2.x |
| Node.js | 20 LTS (untuk build assets — di lokal/CI, bukan server) |
| Git | 2.x |

### 1.3 PHP Extensions
```
mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl,
gd (atau imagick), fileinfo, bcmath, intl, zip, exif, opcache
```

### 1.4 Catatan cPanel Shared Hosting (JIKA dipakai sementara)
- Tanpa SSH penuh → deploy via Git + cPanel Terminal (jika tersedia)
- Tanpa Supervisor → queue worker pakai cron `* * * * * php artisan queue:work --stop-when-empty --max-time=50`
- Tanpa Redis → cache driver `file` atau `database`
- Resource terbatas → tidak ideal untuk ERP + multi-tenant gym
- **Saran**: pakai minimal cPanel VPS (bukan shared) untuk beban ERP

---

## 2. Dev Environment Setup (Lokal — Gratis/Open Source)

Karena belum langganan VPS, development & testing pakai tools lokal gratis.

### 2.1 Opsi A: Docker Compose (Rekomendasi — paling akurat replikasi production)

#### 2.1.1 Install Docker
```bash
# Linux (Arch)
sudo pacman -S docker docker-compose
sudo systemctl start docker

# Linux (Ubuntu/Debian)
sudo apt install docker.io docker-compose
sudo systemctl enable --now docker

# macOS: Docker Desktop (gratis)
# Windows: Docker Desktop (gratis) + WSL2
```

#### 2.1.2 docker-compose.yml (root project)
```yaml
version: '3.8'
services:
  app:
    image: php:8.2-fpm-alpine
    working_dir: /var/www/leaderhub
    volumes:
      - ./:/var/www/leaderhub
    depends_on: [mysql]
    networks: [leaderhub]

  nginx:
    image: nginx:alpine
    ports: ["80:80"]
    volumes:
      - ./:/var/www/leaderhub
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on: [app]
    networks: [leaderhub]

  mysql:
    image: mysql:8.0
    ports: ["3306:3306"]
    environment:
      MYSQL_DATABASE: leaderhub_db
      MYSQL_ROOT_PASSWORD: root
      MYSQL_USER: leaderhub
      MYSQL_PASSWORD: leaderhub
    volumes:
      - dbdata:/var/lib/mysql
    networks: [leaderhub]

  redis:
    image: redis:7-alpine
    ports: ["6379:6379"]
    networks: [leaderhub]

  node:
    image: node:20-alpine
    working_dir: /var/www/leaderhub
    volumes: [./:/var/www/leaderhub]
    command: sh -c "npm install -g pnpm && pnpm install && pnpm dev"
    networks: [leaderhub]

volumes:
  dbdata:

networks:
  leaderhub:
```

#### 2.1.3 docker/nginx.conf
```nginx
server {
    listen 80;
    server_name leaderhub.test erp.leaderhub.test gym.leaderhub.test *.gym.leaderhub.test;
    root /var/www/leaderhub/apps/website/public;
    index index.php;

    # Route per subdomain
    location / {
        if ($host ~* ^erp\.) { root /var/www/leaderhub/apps/hub/public; }
        if ($host ~* ^gym\.) { root /var/www/leaderhub/apps/gym/public; }
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 2.1.4 /etc/hosts (lokal)
```
127.0.0.1 leaderhub.test
127.0.0.1 erp.leaderhub.test
127.0.0.1 gym.leaderhub.test
127.0.0.1 demo.gym.leaderhub.test
```

#### 2.1.5 Jalankan
```bash
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

Akses: `http://leaderhub.test` (website), `http://erp.leaderhub.test` (hub), `http://gym.leaderhub.test` (gym).

### 2.2 Opsi B: Laravel Sail (wrapper Docker, lebih Laravel-idiomatic)
```bash
composer create-project laravel/laravel apps/website
cd apps/website
php artisan sail:install
./vendor/bin/sail up -d
```

### 2.3 Opsi C: Native (tanpa Docker)
| Komponen | Install |
|----------|---------|
| PHP 8.2+ | `sudo pacman -S php` (Arch) / `brew install php` (macOS) |
| PHP ext | `sudo pacman -S php-gd php-intl php-mbstring php-pdo php-xml php-curl php-zip php-bcmath` |
| MySQL | `sudo pacman -S mysql` atau `brew install mysql` |
| Redis | `sudo pacman -S redis` (opsional) |
| Composer | `sudo pacman -S composer` |
| Node.js | `nvm install 20` + `npm install -g pnpm` |

Jalankan tiap app di port berbeda:
```bash
cd apps/website && php artisan serve --port=8000
cd apps/hub && php artisan serve --port=8001
cd apps/gym && php artisan serve --port=8002
```

### 2.4 Opsi D: Laravel Herd (macOS/Windows — gratis, paling mudah)
Download dari https://herd.laravel.com — sudah include PHP + Nginx, otomatis
serve semua app via `.test` domain. Tidak perlu Docker.

---

## 3. Production Setup (cPanel VPS)

### 3.1 Login cPanel
Akses cPanel VPS Anda (biasanya `https://server-ip:2083`).

### 3.2 Install PHP 8.2+
```
cPanel → Software → MultiPHP Manager → Install PHP 8.2 (atau 8.3)
```

### 3.3 Install MySQL 8.0
```
cPanel → Software → MySQL Database Wizard
  DB name: leaderhub_db
  User: leaderhub
  Password: (strong password)
  Privileges: ALL PRIVILEGES
```

Atau via SSH:
```sql
CREATE DATABASE leaderhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'leaderhub'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON leaderhub_db.* TO 'leaderhub'@'localhost';
FLUSH PRIVILEGES;
```

### 3.4 Setup Subdomains
```
cPanel → Domains → Create Domain
  Subdomain: leaderhub.id (atau addon domain)
  Document Root: /home/leaderhub/apps/website/public

  Subdomain: erp.leaderhub.id
  Document Root: /home/leaderhub/apps/hub/public

  Subdomain: gym.leaderhub.id
  Document Root: /home/leaderhub/apps/gym/public

  Wildcard: *.gym.leaderhub.id
  Document Root: /home/leaderhub/apps/gym/public
```

### 3.5 Configure PHP per Subdomain
```
cPanel → Software → MultiPHP Manager
  Set PHP 8.2+ untuk ketiga subdomain
```

### 3.6 PHP Extensions (via cPanel → Software → PHP Extensions)
Aktifkan: mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl, gd, fileinfo, bcmath, intl, zip, exif, opcache.

---

## 4. Deploy Code

### 4.1 Clone Repository
```bash
cd /home/leaderhub
git clone <repo-url> .
# atau jika sudah ada di /home/leaderhub
git pull origin main
```

### 4.2 Setup Core Package
```bash
cd /home/leaderhub/core
composer install --no-dev --optimize-autoloader
```

### 4.3 Build Design Tokens
```bash
cd /home/leaderhub/packages/design-tokens
pnpm install
pnpm build
```

### 4.4 Deploy Website
```bash
cd /home/leaderhub/apps/website

# Composer
composer install --no-dev --optimize-autoloader

# Environment
cp .env.example .env
# Edit .env:
#   APP_NAME=LeaderHub
#   APP_ENV=production
#   APP_DEBUG=false
#   APP_URL=https://leaderhub.id
#   DB_HOST=127.0.0.1
#   DB_DATABASE=leaderhub_db
#   DB_USERNAME=leaderhub
#   DB_PASSWORD=your_password

php artisan key:generate

# Migrations (core + website)
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
php artisan db:seed --force

# Build assets (di lokal/CI, lalu commit public/build/)
# Jika Node.js tersedia di server:
# pnpm install && pnpm build
# Jika tidak: pastikan public/build/ sudah di-commit

php artisan storage:link

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 4.5 Deploy Hub (LeaderP)
```bash
cd /home/leaderhub/apps/hub

composer install --no-dev --optimize-autoloader

cp .env.example .env
# Edit .env:
#   APP_NAME="LeaderP"
#   APP_URL=https://erp.leaderhub.id
#   DB_DATABASE=leaderhub_db
#   ...

php artisan key:generate
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
php artisan db:seed --force

php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4.6 Deploy Gym (LeadGym)
```bash
cd /home/leaderhub/apps/gym

composer install --no-dev --optimize-autoloader

cp .env.example .env
# Edit .env:
#   APP_NAME="LeadGym"
#   APP_URL=https://gym.leaderhub.id
#   DB_DATABASE=leaderhub_db
#   TENANCY_DOMAIN=gym.leaderhub.id
#   ...

php artisan key:generate
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
php artisan db:seed --force

php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Storage Symlink (Shared)

```bash
# Buat shared storage folder
mkdir -p /home/leaderhub/storage/app/public

# Remove default storage symlink di tiap app
rm /home/leaderhub/apps/website/storage/app/public
rm /home/leaderhub/apps/hub/storage/app/public
rm /home/leaderhub/apps/gym/storage/app/public

# Symlink ke shared storage
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/website/storage/app/public
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/hub/storage/app/public
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/gym/storage/app/public

# Permissions
chown -R leaderhub:leaderhub /home/leaderhub/storage
chmod -R 775 /home/leaderhub/storage
```

---

## 6. Apache Configuration (cPanel)

cPanel pakai Apache (atau LiteSpeed yang kompatibel Apache config).
Pretty URL sudah ditangani `.htaccess` default Laravel di `public/`.

### 6.1 .htaccess (default Laravel, pastikan ada di tiap app/public/)
```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 6.2 Force HTTPS (tambah di .htaccess)
```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 6.3 Wildcard Subdomain untuk LeadGym (tenant)
```
cPanel → Domains → Subdomains → Create
  Subdomain: *.gym.leaderhub.id
  Document Root: /home/leaderhub/apps/gym/public
```

Pastikan DNS wildcard `*.gym.leaderhub.id` → A record ke server IP.

---

## 7. SSL Setup (cPanel AutoSSL)

```
cPanel → SSL/TLS → Manage SSL Sites
  Pilih leaderhub.id → Run AutoSSL
  Pilih erp.leaderhub.id → Run AutoSSL
  Pilih gym.leaderhub.id → Run AutoSSL
  Pilih *.gym.leaderhub.id → Run AutoSSL (wildcard perlu special)
```

Untuk wildcard SSL, AutoSSL Let's Encrypt cPanel mendukung wildcard via
DNS validation. Alternatif: beli wildcard SSL atau pakai Cloudflare
(flexible SSL).

---

## 8. Queue Worker (cPanel — tanpa Supervisor)

cPanel shared hosting tidak punya Supervisor. Pakai cron:

### 8.1 Cron untuk Queue
```
cPanel → Cron Jobs → Add
  Minute: *
  Hour: *
  Day: *
  Month: *
  Weekday: *
  Command: cd /home/leaderhub/apps/hub && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

```
  Command: cd /home/leaderhub/apps/gym && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1
```

### 8.2 Jika VPS dengan akses SSH (Supervisor tersedia)
```ini
[program:leaderhub-hub-queue]
process_name=%(program_name)s_%(program_num)02d
command=php /home/leaderhub/apps/hub/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=leaderhub
numprocs=2
redirect_stderr=true
stdout_logfile=/home/leaderhub/apps/hub/storage/logs/queue.log
stopwaitsecs=3600

[program:leaderhub-gym-queue]
command=php /home/leaderhub/apps/gym/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=leaderhub
numprocs=2
```

---

## 9. Cron Jobs

### 9.1 Website Cron
```bash
# Laravel scheduler
* * * * * cd /home/leaderhub/apps/website && php artisan schedule:run >> /dev/null 2>&1

# Sitemap regenerate (daily)
0 2 * * * cd /home/leaderhub/apps/website && php artisan sitemap:generate >> /dev/null 2>&1
```

### 9.2 Hub Cron
```bash
* * * * * cd /home/leaderhub/apps/hub && php artisan schedule:run >> /dev/null 2>&1
```

### 9.3 Gym Cron
```bash
# Scheduler
* * * * * cd /home/leaderhub/apps/gym && php artisan schedule:run >> /dev/null 2>&1

# License sync (daily 00:00)
0 0 * * * cd /home/leaderhub/apps/gym && php artisan gym:sync-licenses >> /dev/null 2>&1

# Subscription expiry notification (daily 08:00)
0 8 * * * cd /home/leaderhub/apps/gym && php artisan gym:notify-expiring >> /dev/null 2>&1
```

---

## 10. Backup Strategy

### 10.1 Database Backup (cPanel Cron)
```
cPanel → Cron Jobs → Add
  Time: Daily 03:00
  Command: mysqldump -u leaderhub -p'password' leaderhub_db | gzip > /home/leaderhub/backups/db_$(date +\%Y\%m\%d).sql.gz
  Keep: 30 days (tambah cron cleanup mingguan)
```

### 10.2 File Backup
```
  Time: Weekly Sunday 04:00
  Command: tar -czf /home/leaderhub/backups/storage_$(date +\%Y\%m\%d).tar.gz /home/leaderhub/storage/app/public
  Keep: 4 weeks
```

### 10.3 spatie/laravel-backup (opsional, lebih robust)
Install di tiap app:
```bash
composer require spatie/laravel-backup
php artisan backup:run
```
Configure notifikasi email/Telegram saat backup gagal.

---

## 11. Post-Deployment Checklist

- [ ] Website accessible di https://leaderhub.id
- [ ] Hub accessible di https://erp.leaderhub.id
- [ ] Gym accessible di https://gym.leaderhub.id
- [ ] Tenant subdomain (e.g. demo.gym.leaderhub.id) berfungsi
- [ ] SSL valid (no warning) semua subdomain
- [ ] Admin login berfungsi (hub)
- [ ] Gym owner login berfungsi (gym, per-tenant)
- [ ] Homepage load < 3 detik
- [ ] Product listing & detail berfungsi
- [ ] Gallery berfungsi
- [ ] Blog berfungsi
- [ ] Inquiry cart → submit → WA/email berfungsi
- [ ] Inquiry muncul di hub dashboard
- [ ] Inquiry → convert ke Sales Order berfungsi
- [ ] Inventory: create product → tampil di website
- [ ] Purchase Order: create → receive → stock update
- [ ] License Manager: issue license → LeadGym tenant aktivasi
- [ ] License suspend → LeadGym read-only
- [ ] LeadGym: member CRUD, subscription, invoice, check-in
- [ ] Dark mode toggle berfungsi (website)
- [ ] Language toggle berfungsi (website)
- [ ] Mobile responsive
- [ ] 404 & 500 pages
- [ ] Sitemap.xml accessible
- [ ] robots.txt accessible
- [ ] Backup berjalan
- [ ] Queue worker running (cron)
- [ ] Cron jobs terjadwal

---

## 12. Update Procedure

### 12.1 Update Website
```bash
cd /home/leaderhub/apps/website
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
# Build assets di lokal/CI, commit public/build/, git pull sudah include
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

### 12.2 Update Hub
```bash
cd /home/leaderhub/apps/hub
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

### 12.3 Update Gym
```bash
cd /home/leaderhub/apps/gym
php artisan down
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```

### 12.4 Update Core
```bash
cd /home/leaderhub/core
git pull origin main
composer install --no-dev --optimize-autoloader

# Update symlink di ketiga app
cd /home/leaderhub/apps/website
composer update leaderhub/core
php artisan migrate --path=../../core/database/migrations --force

cd /home/leaderhub/apps/hub
composer update leaderhub/core

cd /home/leaderhub/apps/gym
composer update leaderhub/core
```

---

## 13. Troubleshooting

### 13.1 Website blank / 500 error
```bash
tail -f /home/leaderhub/apps/website/storage/logs/laravel.log
cd /home/leaderhub/apps/website
php artisan optimize:clear
```

### 13.2 Hub 500 error
```bash
tail -f /home/leaderhub/apps/hub/storage/logs/laravel.log
cd /home/leaderhub/apps/hub
php artisan optimize:clear
```

### 13.3 Gym tenant tidak ter-resolve
```bash
# Cek DNS wildcard
dig demo.gym.leaderhub.id

# Cek .env TENANCY_DOMAIN
# Cek stancl/tenancy config
cd /home/leaderhub/apps/gym
php artisan tenants:run
```

### 13.4 Permission issue
```bash
chown -R leaderhub:leaderhub /home/leaderhub
chmod -R 775 /home/leaderhub/storage
chmod -R 775 /home/leaderhub/bootstrap/cache
```

### 13.5 Storage symlink broken
```bash
rm /home/leaderhub/apps/website/storage/app/public
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/website/storage/app/public
```

### 13.6 Queue not processing
```bash
# Cron-based: cek cron job running
cPanel → Cron Jobs → view logs

# VPS Supervisor:
supervisorctl restart leaderhub-hub-queue
# atau
cd /home/leaderhub/apps/hub && php artisan queue:restart
```

### 13.7 License validation gagal (LeadGym)
```bash
# Cek koneksi ke LeaderP API
curl -X POST https://erp.leaderhub.id/api/license/validate \
  -H "Authorization: Bearer <token>" \
  -d "license_key=XXX"

# Cek log LeadGym
tail -f /home/leaderhub/apps/gym/storage/logs/laravel.log
```

---

## 14. Appendix: aaPanel Setup (Alternatif)

Jika memilih aaPanel (gratis, lebih fleksibel dari cPanel):

### 14.1 Install aaPanel
```bash
# CentOS/AlmaLinux
yum install -y wget && wget -O install.sh https://www.aapanel.com/script/install_7.0_en.sh && bash install.sh aapanel

# Ubuntu/Debian
wget -O install.sh https://www.aapanel.com/script/install-ubuntu_7.0_en.sh && sudo bash install.sh aapanel
```

### 14.2 Setup
- aaPanel → App Store → Install PHP 8.2, MySQL 8.0, Redis (opsional)
- aaPanel → Website → Add Site (3 site: leaderhub.id, erp.leaderhub.id, gym.leaderhub.id)
- aaPanel → Website → Site → Config → Nginx config (pretty URL, SSL)
- aaPanel → Cron → Add backup & scheduler
- aaPanel → App Store → Supervisor (jika ingin queue worker persistent)

Keunggulan aaPanel: gratis, Nginx (lebih cepat), Supervisor built-in, akses root penuh.

---

## 15. CI/CD Opsional (GitHub Actions)

Untuk automasi build & deploy (mengurangi manual SSH):

### .github/workflows/deploy.yml
```yaml
name: Deploy
on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: pnpm/action-setup@v2
        with: { version: 9 }
      - uses: actions/setup-node@v4
        with: { node-version: 20, cache: pnpm }
      - run: pnpm install --frozen-lockfile
      - run: pnpm build:tokens
      - run: pnpm build:all
      - name: Commit build artifacts
        run: |
          git config user.name "CI"
          git add apps/*/public/build packages/design-tokens/dist
          git commit -m "build: assets [skip ci]" || echo "no changes"
          git push
      - name: Deploy via SSH
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USER }}
          key: ${{ secrets.SSH_KEY }}
          script: |
            cd /home/leaderhub
            git pull origin main
            cd apps/website && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize:clear && php artisan config:cache
            cd ../hub && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize:clear && php artisan config:cache
            cd ../gym && composer install --no-dev --optimize-autoloader && php artisan migrate --force && php artisan optimize:clear && php artisan config:cache
```

Setup secrets di GitHub: `HOST`, `USER`, `SSH_KEY`.