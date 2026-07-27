# Tasks — Phase 5: Integration & Polish

> **Detail step-by-step**: `docs/tasks/phase-5-integration-polish.md`
> **Estimasi**: 1 minggu | **Prasyarat**: Phase 4 selesai (LeaderP + LeadGym MVP jalan)

## Prerequisites Verification
- [ ] LeaderP (`/hub`) accessible, dashboard menampilkan stats
- [ ] LeadGym (`/gym`) accessible, tenant subdomain resolve
- [ ] License API berfungsi (`POST /api/license/validate`)
- [ ] Website: homepage, products, blog, inquiry berfungsi
- [ ] Shared DB: products di LeaderP tampil di website

## Tasks

### T01: Verify shared DB integration
- [ ] Test product sync (create di LeaderP → tampil di website)
- [ ] Test inquiry → SO (submit di website → muncul di LeaderP → convert)
- [ ] Test license flow (issue di LeaderP → aktivasi di LeadGym → suspend → read-only)
- [ ] Test multi-tenant isolation (Tenant A data tidak tampil di Tenant B)
- **Validation**: Semua integration test pass
- **Effort**: 4h

### T02: Google Translate widget + manual translation override
- [ ] Embed Google Translate widget di header
- [ ] Verify field_en dipakai jika locale=en di Product/BlogPost/Page controllers
- [ ] Verify Filament resources punya tab/section "English"
- **Validation**: Toggle EN → konten EN tampil jika ada, fallback Google Translate
- **Effort**: 3h

### T03: SEO structured data (JSON-LD BreadcrumbList)
- [ ] Tambah BreadcrumbList JSON-LD di layout (Home + breadcrumb_items section)
- [ ] Tambah breadcrumb items di product detail, blog detail, gallery
- [ ] Verify Product & Article JSON-LD sudah ada (Phase 2 & 3)
- **Validation**: View source → JSON-LD ada, valid di validator.schema.org
- **Effort**: 2h

### T04: Canonical, hreflang, Open Graph, Twitter Card
- [ ] Tambah `<link rel="canonical">` di layout
- [ ] Tambah hreflang alternate links (id, en)
- [ ] Tambah Open Graph meta tags (og:title, og:description, og:image, og:url, og:type)
- [ ] Tambah Twitter Card meta tags
- **Validation**: View source → semua meta tags ada, preview di Facebook/Twitter correct
- **Effort**: 2h

### T05: Performance optimization
- [ ] Run `php artisan config:cache`, `route:cache`, `view:cache`, `event:cache`
- [ ] Configure Redis (jika tersedia) atau file/database driver
- [ ] Verify image optimization (WebP, lazy load, srcset)
- **Validation**: PageSpeed Insights > 80, Core Web Vitals hijau
- **Effort**: 3h

### T06: Accessibility audit (WCAG 2.1 AA)
- [ ] Cek kontras warna (design system §9)
- [ ] Tambah `aria-label` di semua icon-only buttons
- [ ] Pastikan keyboard navigation (Tab order)
- [ ] Tambah `@media (prefers-reduced-motion: reduce)` di CSS
- **Validation**: Lighthouse accessibility > 90, axe DevTools no errors
- **Effort**: 3h

### T07: Cross-browser & responsive testing
- [ ] Test Chrome, Firefox, Safari, Edge
- [ ] Test mobile (320px), tablet (768px), desktop (1024px, 1280px)
- **Validation**: Semua browser render konsisten, responsive OK
- **Effort**: 2h

### T08: Error pages (404, 500, 503)
- [ ] Buat `resources/views/errors/404.blade.php` (404 accent, heading, CTA)
- [ ] Buat `resources/views/errors/500.blade.php`
- [ ] Buat `resources/views/errors/503.blade.php`
- **Validation**: `/nonexistent` → 404 page tampil sesuai design
- **Effort**: 1h

### T09: Documentation (admin guide, user guide, gym owner guide)
- [ ] `docs/guides/admin-leaderp.md` — panduan admin ERP
- [ ] `docs/guides/admin-website.md` — panduan admin konten website
- [ ] `docs/guides/gym-owner.md` — panduan gym owner LeadGym
- [ ] `docs/guides/superadmin.md` — panduan superadmin
- **Validation**: Dokumen lengkap & akurat
- **Effort**: 4h

## Final Validation

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan serve --port=8000 &
sleep 2

# Integration
curl -s http://localhost:8000/products | grep -q "produk" && echo "✓ Website OK" || echo "✗ FAIL"
curl -s http://localhost:8000/nonexistent | grep -q "404" && echo "✓ 404 OK" || echo "✗ 404 FAIL"

# SEO
curl -s http://localhost:8000 | grep -q "Organization" && echo "✓ JSON-LD OK" || echo "✗ FAIL"
curl -s http://localhost:8000 | grep -q "canonical" && echo "✓ Canonical OK" || echo "✗ FAIL"
curl -s http://localhost:8000 | grep -q "og:title" && echo "✓ OG OK" || echo "✗ FAIL"
curl -s http://localhost:8000/sitemap.xml | grep -q "<urlset" && echo "✓ Sitemap OK" || echo "✗ FAIL"

kill %1
cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 5: Integration & polish — SEO, performance, accessibility, docs"
```

**Jika semua ✓ → lanjut Phase 6 (Deployment).**