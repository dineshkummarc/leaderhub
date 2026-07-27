# Design — Phase 6: Deployment

## Context
- **Deployment guide**: `docs/06-deployment.md` (cPanel VPS utama, Docker dev, aaPanel appendix)
- **Step-by-step**: `docs/tasks/phase-6-deployment.md`

## Approach

### cPanel VPS Setup

**Subdomain & Document Root**:
| Subdomain | Document Root | App |
|-----------|---------------|-----|
| leaderhub.id | /home/leaderhub/apps/website/public | Website |
| erp.leaderhub.id | /home/leaderhub/apps/hub/public | LeaderP |
| gym.leaderhub.id | /home/leaderhub/apps/gym/public | LeadGym |
| *.gym.leaderhub.id | /home/leaderhub/apps/gym/public | LeadGym (tenant) |

**PHP**: 8.2+ per subdomain via MultiPHP Manager
**MySQL**: 8.0, database `leaderhub_db`, user `leaderhub`, utf8mb4_unicode_ci
**Extensions**: mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl, gd, fileinfo, bcmath, intl, zip, exif, opcache

### Build & Deploy Sequence

**PENTING untuk AI agent**: Build assets di LOKAL (atau CI), commit `public/build/` ke repo, lalu git pull di server. cPanel sering tidak punya Node.js di server.

1. Build lokal: `pnpm build:tokens && pnpm build:all`
2. Commit: `git add apps/*/public/build packages/design-tokens/dist && git commit && git push`
3. SSH ke server: `cd /home/leaderhub && git pull origin main`
4. Setup core: `cd core && composer install --no-dev --optimize-autoloader`
5. Deploy per app:
   - `cd apps/{website,hub,gym}`
   - `composer install --no-dev --optimize-autoloader`
   - `cp .env.example .env` → edit (APP_URL, DB, queue, cache)
   - `php artisan key:generate`
   - `php artisan migrate --path=../../core/database/migrations --force`
   - `php artisan migrate --force` (app-specific)
   - `php artisan db:seed --force`
   - `php artisan storage:link`
   - `php artisan config:cache && route:cache && view:cache && event:cache`

### Storage Symlink

```bash
mkdir -p /home/leaderhub/storage/app/public
rm /home/leaderhub/apps/website/storage/app/public
rm /home/leaderhub/apps/hub/storage/app/public
rm /home/leaderhub/apps/gym/storage/app/public
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/website/storage/app/public
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/hub/storage/app/public
ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/gym/storage/app/public
chown -R leaderhub:leaderhub /home/leaderhub/storage
chmod -R 775 /home/leaderhub/storage
```

### .htaccess (per app, di `public/.htaccess`)

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

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Cron Jobs

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

# Gym expiry notification (daily 08:00)
0 8 * * * cd /home/leaderhub/apps/gym && php artisan gym:notify-expiring >> /dev/null 2>&1

# DB backup (daily 03:00)
0 3 * * * mysqldump -u leaderhub -p'password' leaderhub_db | gzip > /home/leaderhub/backups/db_$(date +\%Y\%m\%d).sql.gz

# File backup (weekly Sunday 04:00)
0 4 * * 0 tar -czf /home/leaderhub/backups/storage_$(date +\%Y\%m\%d).tar.gz /home/leaderhub/storage/app/public

# Cleanup old backups (weekly, keep 30 days)
0 5 * * 0 find /home/leaderhub/backups -type f -mtime +30 -delete
```

**PENTING untuk AI agent**: cPanel shared hosting tidak punya Supervisor. Queue worker pakai cron `* * * * *` dengan `--stop-when-empty --max-time=50`. Ini jalan 1 menit, proses semua job, lalu stop. Cron jalan lagi menit berikutnya. Tidak ideal tapi cukup untuk volume distributor fitness.

### SSL

cPanel → SSL/TLS → Manage SSL Sites → Run AutoSSL per subdomain.
Wildcard `*.gym.leaderhub.id` perlu DNS validation atau Cloudflare flexible SSL.

### .env Production Config

**Website**:
```env
APP_NAME=LeaderHub
APP_ENV=production
APP_DEBUG=false
APP_URL=https://leaderhub.id
DB_HOST=127.0.0.1
DB_DATABASE=leaderhub_db
DB_USERNAME=leaderhub
DB_PASSWORD={strong password}
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

**Hub**:
```env
APP_NAME="LeaderP"
APP_URL=https://erp.leaderhub.id
# DB sama, CACHE/SESSION/QUEUE sama
SANCTUM_STATEFUL_DOMAINS=erp.leaderhub.id
```

**Gym**:
```env
APP_NAME="LeadGym"
APP_URL=https://gym.leaderhub.id
TENANCY_DOMAIN=gym.leaderhub.id
LEADERP_URL=https://erp.leaderhub.id
LEADERP_API_TOKEN={token}
```

## Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| cPanel tanpa SSH | Tidak bisa git pull / composer install | Pakai cPanel Terminal (jika ada) atau Git Version Control feature cPanel |
| Node.js tidak ada di server | Assets tidak bisa build di server | Build di lokal/CI, commit public/build/ ke repo |
| Queue worker via cron tidak ideal | Job delay sampai 1 menit | Acceptable untuk volume distributor. Upgrade ke VPS dengan Supervisor jika perlu |
| Wildcard SSL sulit | Tenant subdomain tidak HTTPS | Cloudflare flexible SSL atau beli wildcard SSL |
| .env manual edit | Human error | Document exact values, verify setelah deploy |

## Dependencies
- Phase 0-5 selesai (semua fitur jalan, tested, documented)
- cPanel VPS dengan akses SSH/Terminal
- Domain `leaderhub.id` dengan DNS A record ke server IP + wildcard `*.gym.leaderhub.id`