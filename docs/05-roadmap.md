# LeaderHub — Roadmap

> **Version**: 1.0 | **Status**: Draft | **Last Updated**: 2026-07-26

## Timeline Overview

| Phase | Nama | Durasi | Status |
|-------|------|--------|--------|
| 0 | Monorepo Setup | 2 hari | Pending |
| 1 | Website Foundation | 2 minggu | Pending |
| 2 | Website Content | 2 minggu | Pending |
| 3 | Blog & Inquiry | 2 minggu | Pending |
| 4 | LeaderP (ERP) + LeadGym MVP | 3 minggu | Pending |
| 5 | Integration & Polish | 1 minggu | Pending |
| 6 | Deployment | 1 minggu | Pending |

**Total estimasi: ~11 minggu**

---

## Phase 0: Monorepo Setup (Hari 1-2)

### Tujuan
Membangun struktur monorepo + shared core package + design tokens package.

### Tasks
- [ ] Buat struktur folder `leaderhub/`
- [ ] Setup `core/` package (composer.json, autoload PSR-4 `Leaderhub\Core\`)
- [ ] Setup `packages/design-tokens/` (tokens.json, build.js, dist/)
- [ ] Setup `apps/website/` (Laravel 12 fresh install + Filament 5)
- [ ] Setup `apps/hub/` (Laravel 12 fresh install + Filament 5)
- [ ] Setup `apps/gym/` (fork laravel-gymie, upgrade ke Laravel 12 + Filament 5)
- [ ] Configure composer path repositories (root, website, hub, gym)
- [ ] Configure pnpm workspace (`pnpm-workspace.yaml`)
- [ ] Setup stancl/tenancy di apps/gym
- [ ] Test: Model di `core/` bisa dipanggil dari ketiga app
- [ ] Setup shared `storage/` folder + symlink
- [ ] Setup `.gitignore`, `README.md` root
- [ ] Initial git commit

### Deliverables
- Struktur folder monorepo berfungsi
- `composer install` berhasil di website, hub, gym
- `pnpm install` berhasil
- Design tokens build menghasilkan `tokens.css`, `tokens.js`, `filament-theme.php`
- Model test bisa diakses dari ketiga app

### Validation
```bash
# Website
cd apps/website && php artisan serve → http://localhost:8000

# Hub
cd apps/hub && php artisan serve → http://localhost:8001

# Gym
cd apps/gym && php artisan serve → http://localhost:8002

# Core model accessible
php artisan tinker → Leaderhub\Core\Models\Test::all()

# Design tokens
cd packages/design-tokens && pnpm build → dist/ terisi
```

---

## Phase 1: Website Foundation (Minggu 1-2)

### Tujuan
Membangun fondasi website: layout, design system, settings, auth admin.

### Tasks
- [ ] Setup Tailwind 4 dengan design tokens (`@leaderhub/design-tokens`)
- [ ] Setup Google Fonts (Inter)
- [ ] Buat base layout (`layouts/app.blade.php` + Vue components)
- [ ] Buat partials: header (nav + lang toggle + dark mode), footer
- [ ] Implement dark mode toggle (localStorage + class on html)
- [ ] Implement language toggle (ID/EN, session)
- [ ] Database migrations (core): users, settings, pages, translations, categories, brands
- [ ] Admin authentication (Filament panel di website untuk content admin)
- [ ] Settings module (CRUD key-value, grouped, Filament resource)
- [ ] Seeder: admin user, default settings
- [ ] Static pages (About, Contact, Terms, Privacy) — CMS managed
- [ ] Setup Vite + Vue 3 + Inertia (atau Blade + Vue islands)

### Deliverables
- Website bisa diakses dengan layout Modern Premium
- Dark mode & language toggle berfungsi
- Admin bisa login & atur settings
- Design system preview.html konsisten dengan implementasi

---

## Phase 2: Website Content (Minggu 3-4)

### Tujuan
Membangun halaman produk, gallery, homepage.

### Tasks
- [ ] Migrations (core): products, product_images, product_specs, warehouses, stock_levels, gallery_albums, gallery_items, banners
- [ ] Filament resources (website admin): Product, Category, Brand, Gallery, Banner
- [ ] Homepage: hero banner (Swiper), featured products, shop by category, latest stories
- [ ] Product listing page (grid, filter kategori/brand, search, sort, pagination)
- [ ] Product detail page (gallery, specs, related products, WA button, add to inquiry)
- [ ] Gallery page (album filter, masonry grid, lightbox GLightbox)
- [ ] Image optimization (WebP, lazy load, responsive srcset)
- [ ] SEO meta tags (per page), sitemap.xml generator, robots.txt
- [ ] Demo content seeder (30 produk, 2 album, 3 banner)

### Deliverables
- Homepage lengkap dengan banner & featured products
- Product listing & detail berfungsi
- Gallery page berfungsi
- SEO meta & sitemap

---

## Phase 3: Blog & Inquiry (Minggu 5-6)

### Tujuan
Membangun blog frontend & sistem inquiry.

### Tasks
- [ ] Migrations (core): blog_categories, blog_tags, blog_posts, blog_post_tags, inquiries, inquiry_items
- [ ] Filament resources: BlogPost, BlogCategory, BlogTag, Inquiry (view/respond)
- [ ] Blog listing page (editorial grid, filter kategori & tag)
- [ ] Blog detail page (rich content, featured image, reading time, related)
- [ ] Inquiry cart logic (`inquiry.js`, localStorage)
- [ ] Floating inquiry bar
- [ ] "Daftar Minat Saya" page
- [ ] Inquiry form (nama, WA, email, pesan)
- [ ] InquiryService: auto-numbering (INQ-YYYY-XXXXX)
- [ ] WhatsApp integration (wa.me link generator)
- [ ] Email notification ke admin (Mail)
- [ ] Rate limiting (5/IP/jam)
- [ ] Demo content: 10 blog post, 5 inquiry

### Deliverables
- Blog listing & detail berfungsi
- Inquiry cart: tambah, hapus, kirim via WA/email
- Inquiry tersimpan di DB dengan nomor otomatis

---

## Phase 4: LeaderP (ERP) + LeadGym MVP (Minggu 7-9)

### Tujuan
Membangun ERP distributor + software gym multi-tenant + license manager.

### Tasks — LeaderP (apps/hub)
- [ ] Configure Filament panel (HubPanelProvider, theme, dark default)
- [ ] Migrations (core): suppliers, purchase_orders, po_items, stock_movements, sales_orders, so_items, sales_activities, installations, warranties, warranty_claims, support_tickets, employees, departments, company_contacts, agreements
- [ ] Inventory module: ProductResource, WarehouseResource, StockLevelResource, SupplierResource, StockMovementResource
- [ ] Purchase module: PurchaseOrderResource (USD, receive, landed cost)
- [ ] Sales module: SalesOrderResource (IDR, from inquiry, delivery, invoice), SalesActivityResource
- [ ] Installation & Warranty: InstallationResource, WarrantyResource, WarrantyClaimResource
- [ ] After-sales: SupportTicketResource
- [ ] License Manager: LicenseCustomerResource, LicensePlanResource, LicenseResource, LicenseActivationResource
- [ ] License API: POST /api/license/validate (Sanctum, rate limited)
- [ ] Role & permission setup (Filament Shield)
- [ ] Dashboard widgets: stats overview, recent inquiries, low stock, expiring licenses
- [ ] DB Management resource (superadmin): backup, restore, optimize

### Tasks — LeadGym (apps/gym)
- [ ] Configure stancl/tenancy (single-DB, tenant_id, subdomain identification)
- [ ] Configure Filament panel (GymPanelProvider, per-tenant)
- [ ] Migrations (core): tenants, gym_users, members, plans, subscriptions, services, member_services, invoices, invoice_items, invoice_transactions, enquiries, follow_ups, expenses, check_ins
- [ ] Member module: MemberResource (CRUD, photo, QR, import/export)
- [ ] Plan module: PlanResource
- [ ] Subscription module: SubscriptionResource (active, renewal, freeze, transfer)
- [ ] Invoice module: InvoiceResource (numbering per-tenant, PDF, payment)
- [ ] Service module: ServiceResource, MemberServiceResource
- [ ] Enquiry & Follow-up: EnquiryResource, FollowUpResource
- [ ] Expense module: ExpenseResource
- [ ] Check-in module: CheckInResource (scan, validate, capacity)
- [ ] Dashboard: active members, revenue, retention, capacity, expiring soon
- [ ] Tenant Settings: branding, capacity, operating hours, license key input
- [ ] License validation service (call LeaderP API, daily sync cron)
- [ ] Role & permission per tenant (Filament Shield, scoped)
- [ ] Customize UI sesuai design system (Filament theme + design tokens)

### Deliverables
- LeaderP berjalan dengan semua modul ERP + license manager
- LeadGym berjalan multi-tenant dengan semua modul gym
- License validation flow berfungsi end-to-end
- Dashboard kedua app menampilkan stats

---

## Phase 5: Integration & Polish (Minggu 10)

### Tujuan
Connect website ↔ hub ↔ gym, polish, optimize.

### Tasks
- [ ] Verify shared DB: produk di hub → tampil di website
- [ ] Verify: inquiry dari website → muncul di hub → convert ke SO
- [ ] Verify: license issue di hub → LeadGym tenant bisa aktivasi
- [ ] Verify: license suspend di hub → LeadGym read-only
- [ ] Google Translate widget integration (website)
- [ ] Manual translation override (admin input EN, website pakai itu)
- [ ] SEO: structured data (JSON-LD Product, Article, Organization), canonical, og:image
- [ ] Performance: cache config, route, view; Redis (jika ada)
- [ ] Image optimization pipeline (WebP, responsive)
- [ ] Accessibility audit (WCAG 2.1 AA)
- [ ] Cross-browser testing
- [ ] Mobile responsive testing
- [ ] Error pages (404, 500, 503)
- [ ] Documentation: admin guide, user guide, gym owner guide

### Deliverables
- Website, hub, gym terintegrasi penuh
- Performance & SEO optimized
- Documentation lengkap

---

## Phase 6: Deployment (Minggu 11)

### Tujuan
Deploy ke cPanel VPS production.

### Tasks
- [ ] Setup cPanel VPS: 3 subdomain (leaderhub.id, erp.leaderhub.id, gym.leaderhub.id, *.gym.leaderhub.id)
- [ ] Configure PHP 8.2+ per subdomain
- [ ] Configure Apache/LiteSpeed (pretty URL, SSL)
- [ ] Setup MySQL 8.0 database
- [ ] Build assets lokal/CI → commit public/build/
- [ ] Deploy website: git pull, composer install, migrate, seed
- [ ] Deploy hub: git pull, composer install, migrate
- [ ] Deploy gym: git pull, composer install, migrate
- [ ] Setup storage symlink (shared)
- [ ] Setup cron (scheduler, queue:work --stop-when-empty, sitemap, license sync)
- [ ] Setup backup (database + files, spatie/laravel-backup)
- [ ] SSL AutoSSL (cPanel)
- [ ] Testing end-to-end di production
- [ ] Domain switching (dari domain sementara ke domain utama)

### Deliverables
- Website live di leaderhub.id
- LeaderP live di erp.leaderhub.id
- LeadGym live di gym.leaderhub.id + tenant subdomains
- Backup & monitoring berjalan

---

## Milestone Summary

| Milestone | Phase | Criteria |
|-----------|-------|----------|
| M0: Monorepo ready | Phase 0 | Struktur folder + core + design tokens berfungsi |
| M1: Layout ready | Phase 1 | Website layout + dark mode + settings |
| M2: Catalog ready | Phase 2 | Homepage + produk + gallery |
| M3: Inquiry ready | Phase 3 | Blog + inquiry cart + WA/email |
| M4: ERP + Gym ready | Phase 4 | LeaderP + LeadGym + license manager |
| M5: Integrated | Phase 5 | Website ↔ hub ↔ gym + polish |
| M6: Live | Phase 6 | Production deployment cPanel VPS |