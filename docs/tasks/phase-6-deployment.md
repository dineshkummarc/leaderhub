# Phase 6 — Deployment (Detail Task Step-by-Step)

> **Estimasi**: 1 minggu | **Tujuan**: Deploy ke cPanel VPS production
> **Prasyarat**: Phase 5 selesai (semua fitur jalan, tested, documented)

## Validasi Akhir Phase 6
1. Website live di `https://leaderhub.id`
2. LeaderP live di `https://erp.leaderhub.id`
3. LeadGym live di `https://gym.leaderhub.id` + tenant subdomain
4. SSL valid semua subdomain
5. Admin login berfungsi (hub & gym)
6. Semua flow end-to-end berfungsi di production
7. Backup & cron berjalan
8. Post-deployment checklist (06-deployment.md §11) all pass

---

## Task P6-T01: Setup cPanel VPS (subdomain, PHP, MySQL)

### Step-by-Step

**Step 1**: Login cPanel VPS (`https://server-ip:2083`)

**Step 2**: Install PHP 8.2+
```
cPanel → Software → MultiPHP Manager → Install PHP 8.2 (atau 8.3)
```

**Step 3**: Install MySQL 8.0
```
cPanel → Software → MySQL Database Wizard
  DB name: leaderhub_db
  User: leaderhub
  Password: (strong password)
  Privileges: ALL PRIVILEGES
```

**Step 4**: Setup subdomains
```
cPanel → Domains → Create Domain
  Subdomain: leaderhub.id → Document Root: /home/leaderhub/apps/website/public
  Subdomain: erp.leaderhub.id → Document Root: /home/leaderhub/apps/hub/public
  Subdomain: gym.leaderhub.id → Document Root: /home/leaderhub/apps/gym/public
  Wildcard: *.gym.leaderhub.id → Document Root: /home/leaderhub/apps/gym/public
```

**Step 5**: Configure PHP per subdomain
```
cPanel → Software → MultiPHP Manager → Set PHP 8.2+ untuk ketiga subdomain
```

**Step 6**: Aktifkan PHP extensions
```
cPanel → Software → PHP Extensions
  Aktifkan: mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl, gd, fileinfo, bcmath, intl, zip, exif, opcache
```

### Validasi P6-T01
- [ ] Subdomain resolve ke server IP
- [   ] PHP 8.2+ aktif per subdomain
- [ ] MySQL database `leaderhub_db` ada
- [ ] PHP extensions aktif

---

## Task P6-T02: Build assets (lokal/CI) & commit

### Step-by-Step

**Step 1**: Build design tokens
```bash
cd /run/media/salmon/data/Projects/leaderhub
pnpm build:tokens
```

**Step 2**: Build semua app
```bash
pnpm build:all
```

**Step 3**: Commit build artifacts
```bash
git add apps/*/public/build packages/design-tokens/dist
git commit -m "build: production assets"
git push origin main
```

### Validasi P6-T02
- [ ] `apps/website/public/build/` ada file CSS/JS
- [   ] `apps/hub/public/build/` ada
- [ ] `apps/gym/public/build/` ada
- [ ] `packages/design-tokens/dist/` ada

---

## Task P6-T03: Deploy website

### Step-by-Step

**Step 1**: SSH ke server & clone repo
```bash
ssh leaderhub@server-ip
cd /home/leaderhub
git clone <repo-url> .
# atau jika sudah ada
git pull origin main
```

**Step 2**: Setup core
```bash
cd /home/leaderhub/core
composer install --no-dev --optimize-autoloader
```

**Step 3**: Deploy website
```bash
cd /home/leaderhub/apps/website
composer install --no-dev --optimize-autoloader

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
php artisan migrate --path=../../core/database/migrations --force
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### Validasi P6-T03
- [ ] `https://leaderhub.id` load
- [   ] Homepage tampil
- [ ] `/products` berfungsi
- [ ] `/admin/login` accessible

---

## Task P6-T04: Deploy hub (LeaderP)

### Step-by-Step

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
php artisan event:cache
```

### Validasi P6-T04
- [ ] `https://erp.leaderhub.id/hub/login` accessible
- [   ] Login admin berhasil
- [ ] Dashboard menampilkan stats

---

## Task P6-T05: Deploy gym (LeadGym)

### Step-by-Step

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
php artisan event:cache
```

### Validasi P6-T05
- [ ] `https://gym.leaderhub.id/gym/login` accessible
- [   ] Tenant subdomain (e.g. `demo.gym.leaderhub.id`) resolve
- [ ] Login gym owner berhasil

---

## Task P6-T06: Setup cron (scheduler, queue, sitemap, license sync)

### Step-by-Step

**Step 1**: cPanel → Cron Jobs → Add

```bash
# Website scheduler
* * * * * cd /home/leaderhub/apps/website && php artisan schedule:run >> /dev/null 2>&1

# Website sitemap (daily 02:00)
0 2 * * * cd /home/leaderhub/apps/website && php artisan sitemap:generate >> /dev/null 2>&1

# Hub scheduler
* * * * * cd /home/leaderhub/apps/hub && php artisan schedule:run >> /dev/null 2>&1

# Hub queue worker (cron-based, tanpa Supervisor)
* * * * * cd /home/leaderhub/apps/hub && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1

# Gym scheduler
* * * * * cd /home/leaderhub/apps/gym && php artisan schedule:run >> /dev/null 2>&1

# Gym queue worker
* * * * * cd /home/leaderhub/apps/gym && php artisan queue:work --stop-when-empty --max-time=50 >> /dev/null 2>&1

# Gym license sync (daily 00:00)
0 0 * * * cd /home/leaderhub/apps/gym && php artisan gym:sync-licenses >> /dev/null 2>&1

# Gym subscription expiry notification (daily 08:00)
0 8 * * * cd /home/leaderhub/apps/gym && php artisan gym:notify-expiring >> /dev/null 2>&1
```

### Validasi P6-T06
- [ ] Cron jobs terdaftar di cPanel
- [   ] Scheduler berjalan (cek `storage/logs/laravel.log`)
- [ ] Queue worker process jobs

---

## Task P6-T07: Setup backup (DB + files)

### Step-by-Step

**Step 1**: cPanel → Cron Jobs → Add

```bash
# Database backup (daily 03:00)
0 3 * * * mysqldump -u leaderhub -p'password' leaderhub_db | gzip > /home/leaderhub/backups/db_$(date +\%Y\%m\%d).sql.gz

# File backup (weekly Sunday 04:00)
0 4 * * 0 tar -czf /home/leaderhub/backups/storage_$(date +\%Y\%m\%d).tar.gz /home/leaderhub/storage/app/public

# Cleanup old backups (weekly, keep 30 days)
0 5 * * 0 find /home/leaderhub/backups -type f -mtime +30 -delete
```

**Step 2**: (Opsional) Install spatie/laravel-backup
```bash
cd /home/leaderhub/apps/hub
composer require spatie/laravel-backup
php artisan backup:run
```

### Validasi P6-T07
- [ ] Backup DB ter-generate di `/home/leaderhub/backups/`
- [   ] Backup files ter-generate
- [ ] Cleanup berjalan

---

## Task P6-T08: SSL (cPanel AutoSSL)

### Step-by-Step

```
cPanel → SSL/TLS → Manage SSL Sites
  Pilih leaderhub.id → Run AutoSSL
  Pilih erp.leaderhub.id → Run AutoSSL
  Pilih gym.leaderhub.id → Run AutoSSL
  Pilih *.gym.leaderhub.id → Run AutoSSL (wildcard perlu DNS validation)
```

Untuk wildcard SSL, alternatif: Cloudflare (flexible SSL) atau beli wildcard SSL.

### Validasi P6-T08
- [ ] `https://leaderhub.id` valid (no warning)
- [   ] `https://erp.leaderhub.id` valid
- [ ] `https://gym.leaderhub.id` valid
- [ ] `https://demo.gym.leaderhub.id` valid (wildcard)

---

## Task P6-T09: End-to-end testing di production

### Step-by-Step

Jalankan post-deployment checklist (`06-deployment.md` §11):

```bash
# Test website
curl -s https://leaderhub.id | grep -q "LeaderHub" && echo "✓ Website" || echo "✗"
curl -s https://leaderhub.id/products | grep -q "produk" && echo "✓ Products" || echo "✗"
curl -s https://leaderhub.id/blog | grep -q "Blog" && echo "✓ Blog" || echo "✗"
curl -s https://leaderhub.id/gallery | grep -q "Gallery" && echo "✓ Gallery" || echo "✗"
curl -s https://leaderhub.id/sitemap.xml | grep -q "<urlset" && echo "✓ Sitemap" || echo "✗"
curl -s https://leaderhub.id/robots.txt | grep -q "Sitemap" && echo "✓ Robots" || echo "✗"

# Test hub
curl -s https://erp.leaderhub.id/hub/login | grep -q "Filament" && echo "✓ Hub login" || echo "✗"

# Test gym
curl -s https://gym.leaderhub.id/gym/login | grep -q "Filament" && echo "✓ Gym login" || echo "✗"

# Test license API
curl -s -X POST https://erp.leaderhub.id/api/license/validate -H "Content-Type: application/json" -d '{"license_key":"test","tenant_id":1}' | grep -q "valid" && echo "✓ License API" || echo "✗"
```

Manual test:
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
- [ ] Backup berjalan
- [ ] Queue worker running (cron)
- [ ] Cron jobs terjadwal

### Validasi P6-T09
- [ ] Semua checklist pass
- [   ] Tidak ada error di `storage/logs/laravel.log`

---

## Checklist Akhir Phase 6

```bash
# Final verification
echo "=== Production Checklist ==="
echo "Website: $(curl -s -o /dev/null -w '%{http_code}' https://leaderhub.id)"
echo "Hub: $(curl -s -o /dev/null -w '%{http_code}' https://erp.leaderhub.id/hub/login)"
echo "Gym: $(curl -s -o /dev/null -w '%{http_code}' https://gym.leaderhub.id/gym/login)"
echo "SSL: $(curl -s -o /dev/null -w '%{ssl_verify_result}' https://leaderhub.id)"
echo "Sitemap: $(curl -s -o /dev/null -w '%{http_code}' https://leaderhub.id/sitemap.xml)"
echo "=== Done ==="
```

**Jika semua ✓ → project LIVE! 🎉**

```bash
cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 6: Production deployment — cPanel VPS"
```

---

## Post-Deployment: Monitoring & Maintenance

### Regular Tasks
| Task | Frequency | Command |
|------|-----------|---------|
| Cek log error | Daily | `tail -f storage/logs/laravel.log` |
| Cek backup | Daily | `ls -la backups/` |
| Update dependencies | Monthly | `composer update && pnpm update` |
| Security patch | As needed | `composer audit` |
| Performance audit | Quarterly | PageSpeed Insights, Lighthouse |
| SEO audit | Quarterly | Google Search Console |
| Database optimize | Monthly | `php artisan db:optimize` (opsional) |

### Update Procedure
Lihat `06-deployment.md` §12 (Update Procedure) untuk langkah update website, hub, gym, dan core.