## Why

Semua fitur sudah jalan di development (Phase 1-5), tapi belum live di production. Phase 6 deploy LeaderHub ke cPanel VPS: setup subdomain, PHP, MySQL, deploy 3 aplikasi (website, hub, gym), configure cron, backup, SSL, dan testing end-to-end di production. Tanpa Phase 6, project hanya jalan di localhost — tidak bisa diakses oleh pengunjung, admin, atau customer gym.

## What Changes

### cPanel VPS Setup
- Install PHP 8.2+ per subdomain
- Install MySQL 8.0, create database `leaderhub_db`
- Create 3 subdomain: `leaderhub.id`, `erp.leaderhub.id`, `gym.leaderhub.id` + wildcard `*.gym.leaderhub.id`
- Configure PHP extensions (mbstring, openssl, pdo_mysql, gd, bcmath, intl, zip, exif, opcache)
- Configure `.htaccess` per subdomain (pretty URL, force HTTPS)

### Build & Deploy
- Build design tokens (`pnpm build:tokens`)
- Build assets semua app (`pnpm build:all`)
- Commit build artifacts (`public/build/`, `dist/`)
- Git clone/pull ke server
- `composer install --no-dev --optimize-autoloader` per app
- Configure `.env` per app (APP_URL, DB, queue, cache)
- `php artisan key:generate` per app
- `php artisan migrate --path=../../core/database/migrations --force` per app
- `php artisan db:seed --force` per app
- `php artisan storage:link` per app
- `php artisan config:cache`, `route:cache`, `view:cache`, `event:cache` per app

### Storage Symlink (Shared)
- Buat root `storage/app/public/`
- Symlink ke tiap app `storage/app/public/`
- Set permissions (chown, chmod 775)

### Cron Jobs
- Website: scheduler (per menit), sitemap generate (daily 02:00)
- Hub: scheduler (per menit), queue worker (per menit, `--stop-when-empty --max-time=50`)
- Gym: scheduler (per menit), queue worker (per menit), license sync (daily 00:00), expiry notification (daily 08:00)

### Backup
- Database backup: daily 03:00 (mysqldump + gzip)
- File backup: weekly Sunday 04:00 (tar gzip storage)
- Cleanup: weekly, keep 30 days

### SSL
- cPanel AutoSSL untuk semua subdomain
- Wildcard SSL untuk `*.gym.leaderhub.id` (DNS validation atau Cloudflare)

### End-to-End Testing
- Post-deployment checklist (06-deployment.md §11) all pass
- Test: website, hub, gym, license flow, inquiry flow, multi-tenant

## Capabilities

### New Capabilities
- `cpanel-setup`: cPanel VPS configuration (PHP, MySQL, subdomain, extensions)
- `production-deploy`: Deploy 3 apps ke cPanel VPS (git, composer, migrate, seed, optimize)
- `cron-jobs`: Cron jobs untuk scheduler, queue worker, sitemap, license sync, backup
- `ssl-setup`: SSL via cPanel AutoSSL untuk semua subdomain + wildcard
- `production-testing`: End-to-end testing di production environment

### Modified Capabilities
<!-- None — deployment phase, tidak ubah spec -->

## Impact

### Infrastructure
- cPanel VPS: 3 subdomain + wildcard, PHP 8.2+, MySQL 8.0
- Storage: shared symlink
- Cron: 8+ cron jobs
- SSL: AutoSSL + wildcard

### Code
- `.env` per app (production config)
- `public/build/` committed (assets pre-built)
- `.htaccess` per app (force HTTPS, pretty URL)

### Database
- Production MySQL `leaderhub_db` (sama schema seperti dev)

## Non-goals

- **CI/CD automation** (opsional, GitHub Actions — future)
- **Monitoring tools** (uptime, error tracking — future)
- **CDN** (Cloudflare — future)
- **Load balancing** (future, single server untuk MVP)
- **Feature expansion Tier 1** (Phase 7 — AI SEO, job portal, dll)

## Reference

- **Step-by-step detail**: `docs/tasks/phase-6-deployment.md`
- **Deployment guide**: `docs/06-deployment.md` (cPanel VPS utama, aaPanel appendix)