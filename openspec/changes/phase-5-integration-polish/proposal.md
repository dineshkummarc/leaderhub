## Why

Setelah LeaderP ERP dan LeadGym MVP jalan (Phase 4), ketiga aplikasi belum terintegrasi penuh: inquiry dari website belum bisa dikonversi ke Sales Order di LeaderP, license yang di-issue di LeaderP belum bisa divalidasi oleh LeadGym, dan website belum punya SEO advanced, performance optimization, atau dokumentasi. Phase 5 menghubungkan semua, polish UI/UX, optimasi performance & SEO, audit accessibility, dan menulis dokumentasi admin/user. Tanpa Phase 5, project jalan tapi belum siap production.

## What Changes

### Integration Verification
- Verify shared DB: product di LeaderP → tampil di website
- Verify: inquiry dari website → muncul di LeaderP → convert ke Sales Order
- Verify: license issue di LeaderP → LeadGym tenant bisa aktivasi → suspend → read-only
- Verify: multi-tenant isolation (Tenant A tidak bisa akses data Tenant B)

### Multi-Bahasa Enhancement
- Google Translate widget embed di website (fallback untuk konten yang belum diterjemahkan manual)
- Manual translation override: field `_en` di Product, BlogPost, Page sudah ada di migration (Phase 1-3), sekarang aktifkan di frontend (jika locale=en dan field_en ada → pakai field_en)
- Admin Filament: tab "English" di Product/BlogPost/Page resources untuk input EN

### SEO Advanced
- BreadcrumbList JSON-LD di semua halaman (Home > Section > Detail)
- Canonical URL per halaman (hindari duplicate content)
- hreflang untuk multi-bahasa (ID default, EN alternate)
- Open Graph + Twitter Card meta tags per halaman
- Sitemap: include blog posts (dari Phase 3, verify)

### Performance Optimization
- Cache config, route, view, event (`php artisan config:cache`, dll)
- Redis untuk cache & queue (jika tersedia, opsional — file driver untuk cPanel)
- Image optimization pipeline verify (WebP, lazy load, srcset dari Phase 2)
- PageSpeed target > 80

### Accessibility Audit (WCAG 2.1 AA)
- Cek kontras warna (sudah sesuai design system §9)
- Tambah `aria-label` di semua icon-only buttons
- Pastikan keyboard navigation (Tab order logical)
- Respect `prefers-reduced-motion`

### Cross-Browser & Responsive Testing
- Test: Chrome, Firefox, Safari, Edge (desktop)
- Test: mobile (320px), tablet (768px), desktop (1024px, 1280px)

### Error Pages
- Custom 404, 500, 503 pages sesuai design system Modern Premium

### Documentation
- Admin guide LeaderP (cara kelola inventory, PO, SO, license)
- Admin guide Website (cara kelola produk, blog, inquiry, settings)
- Gym owner guide LeadGym (cara kelola member, subscription, invoice, check-in)
- Superadmin guide (DB management, users, license)

## Capabilities

### New Capabilities
- `integration-verification`: Verifikasi end-to-end integration website ↔ LeaderP ↔ LeadGym
- `multilanguage-enhancement`: Google Translate widget + manual override aktif di frontend
- `seo-advanced`: BreadcrumbList JSON-LD, canonical, hreflang, Open Graph, Twitter Card
- `performance-optimization`: Cache config/route/view, Redis opsional, image pipeline verify
- `accessibility-audit`: WCAG 2.1 AA compliance, aria-label, keyboard nav, reduced motion
- `error-pages`: Custom 404, 500, 503 pages
- `user-documentation`: Admin guide, gym owner guide, superadmin guide

### Modified Capabilities
- `website-layout`: Tambah Google Translate widget, breadcrumb, canonical, hreflang, OG/Twitter meta
- `blog-system`: Verify blog posts di sitemap, JSON-LD BreadcrumbList di blog detail

## Impact

### Code Affected
- `apps/website/resources/views/layouts/app.blade.php`: tambah OG/Twitter meta, canonical, hreflang, Google Translate widget
- `apps/website/resources/views/errors/`: 3 error pages baru
- `apps/website/app/Http/Middleware/`: verify SetLocale middleware
- `docs/guides/`: 4 dokumentasi baru

### Database
- Tidak ada tabel baru

### Dependencies
- Tidak ada package baru

## Non-goals

- **AI Meta Generator** (Tier 1 expansion — Phase 7)
- **AI Content Suggestion** (Tier 1 expansion — Phase 7)
- **Job Portal** (Tier 1 expansion — Phase 7)
- **Media Library centralized** (Tier 1 expansion — Phase 7)
- **Form Builder** (Tier 1 expansion — Phase 7)
- **Production deployment** (Phase 6)

## Reference

- **Step-by-step detail**: `docs/tasks/phase-5-integration-polish.md`
- **Feature expansion**: `docs/09-feature-expansion.md` §2 (SEO), §3 (content admin)