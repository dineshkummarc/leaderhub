# Tasks — Phase 6: Deployment

> **Detail step-by-step**: `docs/tasks/phase-6-deployment.md`
> **Estimasi**: 1 minggu | **Prasyarat**: Phase 5 selesai (semua fitur jalan, tested, documented)

## Prerequisites Verification
- [ ] Website load di dev (localhost:8000) tanpa error
- [ ] LeaderP load di dev (localhost:8001) tanpa error
- [ ] LeadGym load di dev (localhost:8002) tanpa error
- [ ] Integration test pass (Phase 5 T01)
- [ ] Documentation lengkap (Phase 5 T09)
- [ ] cPanel VPS dengan akses SSH/Terminal
- [ ] Domain `leaderhub.id` dengan DNS A record ke server IP
- [ ] Wildcard DNS `*.gym.leaderhub.id` → server IP

## Tasks

### T01: Setup cPanel VPS (subdomain, PHP, MySQL)
- [ ] Login cPanel VPS
- [ ] Install PHP 8.2+ via MultiPHP Manager
- [ ] Create MySQL database `leaderhub_db` (utf8mb4_unicode_ci) + user `leaderhub` + grant ALL PRIVILEGES
- [ ] Create 3 subdomain + wildcard via Domains → Create Domain:
  - `leaderhub.id` → `/home/leaderhub/apps/website/public`
  - `erp.leaderhub.id` → `/home/leaderhub/apps/hub/public`
  - `gym.leaderhub.id` → `/home/leaderhub/apps/gym/public`
  - `*.gym.leaderhub.id` → `/home/leaderhub/apps/gym/public`
- [ ] Set PHP 8.2+ per subdomain via MultiPHP Manager
- [ ] Enable PHP extensions via PHP Extensions: mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl, gd, fileinfo, bcmath, intl, zip, exif, opcache
- **Validation**: Subdomain resolve ke server IP, PHP 8.2+ aktif, MySQL database ada, extensions aktif
- **Effort**: 2h

### T02: Build assets (lokal/CI) & commit
- [ ] `cd /run/media/salmon/data/Projects/leaderhub`
- [ ] `pnpm build:tokens`
- [ ] `pnpm build:all`
- [ ] `git add apps/*/public/build packages/design-tokens/dist`
- [ ] `git commit -m "build: production assets"`
- [ ] `git push origin main`
- **Validation**: `apps/website/public/build/` ada file CSS/JS, `apps/hub/public/build/` ada, `apps/gym/public/build/` ada, `packages/design-tokens/dist/` ada
- **Effort**: 1h

### T03: Deploy website
- [ ] SSH ke server: `ssh leaderhub@server-ip`
- [ ] `cd /home/leaderhub && git clone <repo-url> .` (atau `git pull origin main` jika sudah ada)
- [ ] `cd core && composer install --no-dev --optimize-autoloader`
- [ ] `cd apps/website && composer install --no-dev --optimize-autoloader`
- [ ] `cp .env.example .env` → edit: APP_NAME=LeaderHub, APP_ENV=production, APP_DEBUG=false, APP_URL=https://leaderhub.id, DB_*, CACHE_STORE=file, SESSION_DRIVER=file, QUEUE_CONNECTION=database
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate --path=../../core/database/migrations --force`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --force`
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache`
- **Validation**: `https://leaderhub.id` load, homepage tampil, `/products` berfungsi, `/admin/login` accessible
- **Effort**: 2h

### T04: Deploy hub (LeaderP)
- [ ] `cd /home/leaderhub/apps/hub && composer install --no-dev --optimize-autoloader`
- [ ] `cp .env.example .env` → edit: APP_NAME="LeaderP", APP_URL=https://erp.leaderhub.id, DB_* (sama), CACHE/SESSION/QUEUE sama
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate --path=../../core/database/migrations --force`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --force`
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache`
- **Validation**: `https://erp.leaderhub.id/hub/login` accessible, login admin berhasil, dashboard menampilkan stats
- **Effort**: 2h

### T05: Deploy gym (LeadGym)
- [ ] `cd /home/leaderhub/apps/gym && composer install --no-dev --optimize-autoloader`
- [ ] `cp .env.example .env` → edit: APP_NAME="LeadGym", APP_URL=https://gym.leaderhub.id, DB_* (sama), TENANCY_DOMAIN=gym.leaderhub.id, LEADERP_URL=https://erp.leaderhub.id, LEADERP_API_TOKEN={token}
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate --path=../../core/database/migrations --force`
- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --force`
- [ ] `php artisan storage:link`
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan event:cache`
- **Validation**: `https://gym.leaderhub.id/gym/login` accessible, tenant subdomain resolve, login gym owner berhasil
- **Effort**: 2h

### T06: Setup shared storage symlink
- [ ] `mkdir -p /home/leaderhub/storage/app/public`
- [ ] `rm /home/leaderhub/apps/website/storage/app/public && ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/website/storage/app/public`
- [ ] `rm /home/leaderhub/apps/hub/storage/app/public && ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/hub/storage/app/public`
- [ ] `rm /home/leaderhub/apps/gym/storage/app/public && ln -s /home/leaderhub/storage/app/public /home/leaderhub/apps/gym/storage/app/public`
- [ ] `chown -R leaderhub:leaderhub /home/leaderhub/storage && chmod -R 775 /home/leaderhub/storage`
- **Validation**: `ls -la apps/website/storage/app/public` → symlink ke shared storage, file upload dari satu app bisa diakses app lain
- **Effort**: 1h

### T07: Setup cron (scheduler, queue, sitemap, license sync, backup)
- [ ] cPanel → Cron Jobs → Add semua cron jobs (lihat design.md untuk list lengkap):
  - Website scheduler, sitemap
  - Hub scheduler, queue worker
  - Gym scheduler, queue worker, license sync, expiry notification
  - DB backup, file backup, cleanup
- **Validation**: Cron jobs terdaftar di cPanel, scheduler berjalan (cek log), queue process jobs, backup ter-generate
- **Effort**: 1h

### T08: Setup backup (DB + files)
- [ ] `mkdir -p /home/leaderhub/backups`
- [ ] Verify cron backup jobs (dari T07) berjalan
- [ ] Test restore: download backup, import ke test database, verify data intact
- **Validation**: Backup DB ter-generate di `/home/leaderhub/backups/`, backup files ter-generate, restore test berhasil
- **Effort**: 1h

### T09: SSL (cPanel AutoSSL)
- [ ] cPanel → SSL/TLS → Manage SSL Sites → Run AutoSSL untuk: leaderhub.id, www.leaderhub.id, erp.leaderhub.id, gym.leaderhub.id
- [ ] Untuk wildcard `*.gym.leaderhub.id`: AutoSSL dengan DNS validation, atau Cloudflare flexible SSL, atau beli wildcard SSL
- [ ] Verify `.htaccess` force HTTPS redirect aktif di tiap app
- **Validation**: `https://leaderhub.id` valid (no warning), `https://erp.leaderhub.id` valid, `https://gym.leaderhub.id` valid, `https://demo.gym.leaderhub.id` valid (wildcard)
- **Effort**: 1h

### T10: End-to-end testing di production
- [ ] Test website: homepage, products, blog, gallery, inquiry, dark mode, language toggle
- [ ] Test LeaderP: login, dashboard, create product → tampil di website, create PO → receive, inquiry → SO → deliver, issue license
- [ ] Test LeadGym: login (tenant subdomain), dashboard, member CRUD, subscription, invoice, check-in, license validation
- [ ] Test multi-tenant isolation: Tenant A data tidak tampil di Tenant B
- [ ] Test integration: inquiry → SO, license → LeadGym, suspend → read-only
- [ ] Test SSL: semua URL HTTPS valid
- [ ] Test sitemap & robots: `/sitemap.xml`, `/robots.txt` accessible
- [ ] Test backup: backup files ada setelah 24h
- [ ] Test cron: scheduler & queue running (cek log)
- [ ] Run post-deployment checklist (`docs/06-deployment.md` §11)
- **Validation**: Semma checklist pass, tidak ada error di `storage/logs/laravel.log`
- **Effort**: 4h

## Final Validation

```bash
# Final verification
echo "=== Production Checklist ==="
echo "Website: $(curl -s -o /dev/null -w '%{http_code}' https://leaderhub.id)"
echo "Hub: $(curl -s -o /dev/null -w '%{http_code}' https://erp.leaderhub.id/hub/login)"
echo "Gym: $(curl -s -o /dev/null -w '%{http_code}' https://gym.leaderhub.id/gym/login)"
echo "SSL: $(curl -s -o /dev/null -w '%{ssl_verify_result}' https://leaderhub.id)"
echo "Sitemap: $(curl -s -o /dev/null -w '%{http_code}' https://leaderhub.id/sitemap.xml)"
echo "Robots: $(curl -s -o /dev/null -w '%{http_code}' https://leaderhub.id/robots.txt)"
echo "=== Done ==="
```

**Jika semua ✓ → project LIVE! 🎉**

```bash
cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 6: Production deployment — cPanel VPS"
```

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
Lihat `docs/06-deployment.md` §12 (Update Procedure) untuk langkah update website, hub, gym, dan core.