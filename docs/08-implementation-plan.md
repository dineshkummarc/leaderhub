# LeaderHub — Implementation Plan

> **Version**: 1.0 | **Status**: Draft | **Last Updated**: 2026-07-26
> **Referensi**: `05-roadmap.md` (fase), `02-architecture.md` (struktur)

## 1. Tujuan Dokumen
Task breakdown detail per fase, siap untuk eksekusi atau delegasi ke
CoderAgent. Setiap task punya: deskripsi, file terkait, dependency, validasi.

## 2. Konvensi Task
- Format ID: `P{phase}-T{nn}` (e.g. `P0-T01`)
- Status: pending, in_progress, completed, blocked
- Dependency: task ID yang harus selesai dulu
- Parallel: bisa jalan bersamaan (tidak ada dependency antar mereka)

---

## Phase 0: Monorepo Setup (Hari 1-2)

### P0-T01: Inisialisasi struktur folder & git
- **Desc**: Buat root folder `leaderhub/`, init git, `.gitignore`, `README.md` root
- **Files**: `.gitignore`, `README.md`, `composer.json` (root), `package.json` (root), `pnpm-workspace.yaml`
- **Dep**: —
- **Parallel**: ✅ (dengan T02, T03)
- **Validation**: `git status` clean, folder structure sesuai `02-architecture.md`

### P0-T02: Setup core package
- **Desc**: Buat `core/` dengan composer.json (PSR-4 `Leaderhub\Core\`), src/Models, Services, Enums, Traits, Contracts, database/migrations, seeders, factories
- **Files**: `core/composer.json`, `core/src/Models/.gitkeep`, `core/database/migrations/.gitkeep`
- **Dep**: —
- **Parallel**: ✅
- **Validation**: `cd core && composer install` berhasil

### P0-T03: Setup design-tokens package
- **Desc**: Buat `packages/design-tokens/` dengan tokens.json (sumber), build.js (Style Dictionary), dist/ (tokens.css, tokens.js, filament-theme.php)
- **Files**: `packages/design-tokens/tokens.json`, `build.js`, `package.json`, `dist/*`
- **Dep**: —
- **Parallel**: ✅
- **Validation**: `cd packages/design-tokens && pnpm install && pnpm build` menghasilkan dist/

### P0-T04: Setup apps/website (Laravel 12 fresh)
- **Desc**: `composer create-project laravel/laravel apps/website`, install Filament 5, require `leaderhub/core` + `leaderhub/design-tokens` (path repo), setup tailwind.config import tokens
- **Files**: `apps/website/*`
- **Dep**: T01, T02, T03
- **Parallel**: ✅ (dengan T05, T06)
- **Validation**: `cd apps/website && php artisan serve` → localhost:8000 load

### P0-T05: Setup apps/hub (Laravel 12 fresh + Filament 5)
- **Desc**: `composer create-project laravel/laravel apps/hub`, install Filament 5 + Shield, require core + design-tokens, configure HubPanelProvider (theme, dark default)
- **Files**: `apps/hub/*`
- **Dep**: T01, T02, T03
- **Parallel**: ✅
- **Validation**: `cd apps/hub && php artisan serve` → localhost:8001 load, Filament panel accessible

### P0-T06: Setup apps/gym (fork laravel-gymie)
- **Desc**: Copy laravel-gymie ke `apps/gym/`, upgrade ke Laravel 12 + Filament 5 (jika belum), install stancl/tenancy, require core + design-tokens, configure GymPanelProvider (per-tenant)
- **Files**: `apps/gym/*`
- **Dep**: T01, T02, T03
- **Parallel**: ✅
- **Validation**: `cd apps/gym && php artisan serve` → localhost:8002 load, Filament panel accessible

### P0-T07: Configure pnpm workspace & root composer
- **Desc**: Setup `pnpm-workspace.yaml`, root `package.json` scripts (dev:website, build:all, build:tokens), root `composer.json` path repositories
- **Files**: `pnpm-workspace.yaml`, `package.json`, `composer.json`
- **Dep**: T04, T05, T06
- **Parallel**: ❌
- **Validation**: `pnpm install` berhasil di root, `composer install` berhasil di tiap app

### P0-T08: Test core model accessible dari ketiga app
- **Desc**: Buat test model di core, migrate, akses dari tiap app via tinker
- **Files**: `core/src/Models/Test.php`, `core/database/migrations/0001_create_tests_table.php`
- **Dep**: T04, T05, T06, T07
- **Parallel**: ❌
- **Validation**: `php artisan tinker` di tiap app → `Leaderhub\Core\Models\Test::all()` berfungsi

### P0-T09: Setup shared storage symlink
- **Desc**: Buat `storage/` di root, symlink ke tiap app `storage/app/public`
- **Files**: `storage/app/public/.gitkeep`
- **Dep**: T04, T05, T06
- **Parallel**: ✅
- **Validation**: `php artisan storage:link` di tiap app → symlink ke shared storage

### P0-T10: Initial git commit
- **Desc**: Commit semua, push ke remote (jika ada)
- **Files**: semua
- **Dep**: T01-T09
- **Parallel**: ❌
- **Validation**: `git log` ada 1 commit

---

## Phase 1: Website Foundation (Minggu 1-2)

### P1-T01: Setup Tailwind 4 + design tokens
- **Desc**: Configure tailwind.config.js import `@leaderhub/design-tokens/tokens.js`, setup Google Fonts (Inter), setup CSS variables (dark/light)
- **Files**: `apps/website/tailwind.config.js`, `apps/website/resources/css/app.css`
- **Dep**: P0-T04
- **Parallel**: ✅ (dengan T02)
- **Validation**: Tailwind build berhasil, tokens ter-apply

### P1-T02: Migrations — users, settings, pages, translations
- **Desc**: Buat migrations di `core/database/migrations/` untuk users, settings, pages, translations, categories, brands
- **Files**: `core/database/migrations/0002_create_users_table.php`, `0003_create_settings_table.php`, `0004_create_pages_table.php`, `0005_create_translations_table.php`, `0006_create_categories_table.php`, `0007_create_brands_table.php`
- **Dep**: P0-T02
- **Parallel**: ✅
- **Validation**: `php artisan migrate` berhasil

### P1-T03: Base layout & partials
- **Desc**: Buat `layouts/app.blade.php`, partials: header (nav + lang toggle + dark mode + inquiry count), footer. Vue components untuk interactivity.
- **Files**: `apps/website/resources/views/layouts/app.blade.php`, `partials/header.blade.php`, `partials/footer.blade.php`, `resources/js/app.js`
- **Dep**: T01
- **Parallel**: ❌
- **Validation**: Load homepage (dummy) → layout tampil, dark/light toggle berfungsi

### P1-T04: Dark mode & language toggle
- **Desc**: Implement dark mode (localStorage + class on html), language toggle (session + cookie)
- **Files**: `apps/website/resources/js/toggle.js`, `app/Http/Middleware/SetLocale.php`
- **Dep**: T03
- **Parallel**: ❌
- **Validation**: Toggle dark/light persist setelah refresh, toggle ID/EN ganti session

### P1-T05: Admin auth (Filament panel website)
- **Desc**: Configure Filament panel di website untuk content_admin, install Shield, setup roles
- **Files**: `apps/website/app/Providers/Filament/WebsitePanelProvider.php`
- **Dep**: P0-T04, T02
- **Parallel**: ❌
- **Validation**: Login ke `/admin` berfungsi, role ter-assign

### P1-T06: Settings module (Filament resource)
- **Desc**: SettingsResource (CRUD key-value, grouped: general, contact, seo, social, currency)
- **Files**: `apps/website/app/Filament/Resources/SettingResource.php`
- **Dep**: T02, T05
- **Parallel**: ❌
- **Validation**: CRUD settings berfungsi

### P1-T07: Seeder (admin user, default settings)
- **Desc**: Seeder untuk super_admin, content_admin, default settings (site name, contact, seo)
- **Files**: `core/database/seeders/UserSeeder.php`, `SettingSeeder.php`
- **Dep**: T02, T05
- **Parallel**: ✅
- **Validation**: `php artisan db:seed` berhasil, admin bisa login

### P1-T08: Static pages (About, Contact, Terms, Privacy)
- **Desc**: PageController untuk render pages dari DB, PageResource (Filament) untuk CRUD
- **Files**: `apps/website/app/Http/Controllers/PageController.php`, `app/Filament/Resources/PageResource.php`, `routes/web.php`
- **Dep**: T02, T03, T05
- **Parallel**: ❌
- **Validation**: `/about`, `/contact`, `/terms`, `/privacy` load dari DB, admin bisa edit

---

## Phase 2: Website Content (Minggu 3-4)

### P2-T01: Migrations — products, images, specs, warehouses, stock, gallery, banners
- **Desc**: Migrations di core untuk products, product_images, product_specs, warehouses, stock_levels, gallery_albums, gallery_items, banners
- **Files**: `core/database/migrations/0008_*` s/d `0015_*`
- **Dep**: P1-T02
- **Parallel**: ✅ (dengan T02)
- **Validation**: `php artisan migrate` berhasil

### P2-T02: Filament resources — Product, Category, Brand, Gallery, Banner
- **Desc**: CRUD resources untuk content admin
- **Files**: `apps/website/app/Filament/Resources/ProductResource.php`, `CategoryResource.php`, `BrandResource.php`, `GalleryAlbumResource.php`, `GalleryItemResource.php`, `BannerResource.php`
- **Dep**: T01, P1-T05
- **Parallel**: ❌
- **Validation**: CRUD semua resource berfungsi

### P2-T03: Homepage (hero, featured, category, blog, CTA)
- **Desc**: HomeController, homepage view dengan Swiper hero, featured products, shop by category, latest stories, CTA
- **Files**: `apps/website/app/Http/Controllers/HomeController.php`, `resources/views/home.blade.php`, `resources/js/home.js`
- **Dep**: T01, T02, P1-T03
- **Parallel**: ❌
- **Validation**: Homepage load, semua section tampil

### P2-T04: Product listing page
- **Desc**: ProductController index, listing view dengan filter (category, brand, price), search, sort, pagination
- **Files**: `apps/website/app/Http/Controllers/ProductController.php`, `resources/views/products/index.blade.php`
- **Dep**: T01, T02
- **Parallel**: ✅ (dengan T05)
- **Validation**: `/products` load, filter/search/sort/pagination berfungsi

### P2-T05: Product detail page
- **Desc**: ProductController show, detail view dengan gallery, specs, related, WA button, add to inquiry
- **Files**: `apps/website/resources/views/products/show.blade.php`
- **Dep**: T01, T02, T04
- **Parallel**: ✅
- **Validation**: `/products/{slug}` load, semua info tampil

### P2-T06: Gallery page
- **Desc**: GalleryController, masonry grid, GLightbox, album filter
- **Files**: `apps/website/app/Http/Controllers/GalleryController.php`, `resources/views/gallery.blade.php`
- **Dep**: T01, T02
- **Parallel**: ✅
- **Validation**: `/gallery` load, lightbox berfungsi

### P2-T07: Image optimization (WebP, lazy load, srcset)
- **Desc**: Intervention Image untuk WebP conversion, lazy load attribute, responsive srcset
- **Files**: `apps/website/app/Services/ImageService.php`
- **Dep**: T02, T04, T05, T06
- **Parallel**: ❌
- **Validation**: Upload image → WebP generated, srcset di HTML

### P2-T08: SEO meta + sitemap + robots
- **Desc**: SEO meta per page, sitemap.xml generator, robots.txt
- **Files**: `apps/website/app/Http/Controllers/SitemapController.php`, `resources/views/seo/*.blade.php`
- **Dep**: T03, T04, T05
- **Parallel**: ❌
- **Validation**: `/sitemap.xml` accessible, meta tags di view source

### P2-T09: Demo content seeder
- **Desc**: Seeder 30 produk, 2 album gallery, 3 banner, 5 kategori, 5 brand
- **Files**: `core/database/seeders/ProductSeeder.php`, `GallerySeeder.php`, `BannerSeeder.php`
- **Dep**: T01, T02
- **Parallel**: ✅
- **Validation**: `php artisan db:seed` berhasil, data tampil di website

---

## Phase 3: Blog & Inquiry (Minggu 5-6)

### P3-T01: Migrations — blog & inquiry
- **Desc**: Migrations untuk blog_categories, blog_tags, blog_posts, blog_post_tags, inquiries, inquiry_items
- **Files**: `core/database/migrations/0016_*` s/d `0021_*`
- **Dep**: P2-T01
- **Parallel**: ✅ (dengan T02)
- **Validation**: `php artisan migrate` berhasil

### P3-T02: Filament resources — Blog & Inquiry
- **Desc**: BlogPostResource (dengan TipTap rich text), BlogCategoryResource, BlogTagResource, InquiryResource (view/respond)
- **Files**: `apps/website/app/Filament/Resources/BlogPostResource.php`, dll
- **Dep**: T01, P1-T05
- **Parallel**: ✅
- **Validation**: CRUD blog & view inquiry berfungsi

### P3-T03: Blog listing & detail
- **Desc**: BlogController index (editorial grid, filter, search, pagination), show (rich content, related)
- **Files**: `apps/website/app/Http/Controllers/BlogController.php`, `resources/views/blog/*.blade.php`
- **Dep**: T01, T02
- **Parallel**: ✅ (dengan T04)
- **Validation**: `/blog` load, `/blog/{slug}` load, filter berfungsi

### P3-T04: Inquiry cart logic (localStorage)
- **Desc**: `inquiry.js` — add/remove/qty, floating inquiry bar, "Daftar Minat Saya" page
- **Files**: `apps/website/resources/js/inquiry.js`, `resources/views/inquiry.blade.php`
- **Dep**: P1-T03
- **Parallel**: ✅
- **Validation**: Add to cart → bar muncul, view page → list tampil, edit/hapus berfungsi

### P3-T05: Inquiry form & submit (WA + email)
- **Desc**: InquiryController submit, InquiryService (auto-numbering INQ-YYYY-XXXXX), wa.me link, email Mailable, rate limit, honeypot
- **Files**: `apps/website/app/Http/Controllers/InquiryController.php`, `core/src/Services/InquiryService.php`, `app/Mail/InquiryReceived.php`
- **Dep**: T01, T04
- **Parallel**: ❌
- **Validation**: Submit → inquiry tersimpan, WA link benar, email terkirim, rate limit 5/jam

### P3-T06: Demo content (blog + inquiry)
- **Desc**: Seeder 10 blog post, 5 inquiry
- **Files**: `core/database/seeders/BlogSeeder.php`, `InquirySeeder.php`
- **Dep**: T01, T02
- **Parallel**: ✅
- **Validation**: Data tampil di website

---

## Phase 4: LeaderP (ERP) + LeadGym MVP (Minggu 7-9)

### Batch 4A: LeaderP (apps/hub) — parallel dengan 4B

#### P4-T01: Configure Hub Filament panel
- **Desc**: HubPanelProvider (theme, dark default, navigation groups, Shield)
- **Files**: `apps/hub/app/Providers/Filament/HubPanelProvider.php`
- **Dep**: P0-T05
- **Parallel**: ✅
- **Validation**: Panel load, theme ter-apply

#### P4-T02: Migrations — ERP (suppliers, PO, SO, stock, warranty, dll)
- **Desc**: Migrations untuk suppliers, purchase_orders, po_items, stock_movements, sales_orders, so_items, sales_activities, installations, warranties, warranty_claims, support_tickets, employees, departments, company_contacts, agreements
- **Files**: `core/database/migrations/0022_*` s/d `0036_*`
- **Dep**: P0-T02
- **Parallel**: ✅
- **Validation**: `php artisan migrate` berhasil

#### P4-T03: Inventory module
- **Desc**: ProductResource, WarehouseResource, StockLevelResource, SupplierResource, StockMovementResource
- **Files**: `apps/hub/app/Filament/Resources/Inventory/*`
- **Dep**: T01, T02
- **Parallel**: ✅ (dengan T04, T05, T06)
- **Validation**: CRUD inventory berfungsi

#### P4-T04: Purchase module
- **Desc**: PurchaseOrderResource (USD, receive goods → stock in, landed cost calc)
- **Files**: `apps/hub/app/Filament/Resources/Purchases/PurchaseOrderResource.php`
- **Dep**: T01, T02, T03
- **Parallel**: ✅
- **Validation**: Create PO, receive, stock update, landed cost calc

#### P4-T05: Sales module
- **Desc**: SalesOrderResource (IDR, from inquiry, delivery, invoice), SalesActivityResource
- **Files**: `apps/hub/app/Filament/Resources/Sales/*`
- **Dep**: T01, T02, T03
- **Parallel**: ✅
- **Validation**: Create SO, convert from inquiry, delivery → stock out

#### P4-T06: Installation & Warranty & After-sales
- **Desc**: InstallationResource, WarrantyResource, WarrantyClaimResource, SupportTicketResource
- **Files**: `apps/hub/app/Filament/Resources/AfterSales/*`
- **Dep**: T05
- **Parallel**: ✅
- **Validation**: Schedule install, auto-create warranty, claim, ticket

#### P4-T07: License Manager module
- **Desc**: LicenseCustomerResource, LicensePlanResource, LicenseResource, LicenseActivationResource
- **Files**: `apps/hub/app/Filament/Resources/Licenses/*`
- **Dep**: T02 (migrations license)
- **Parallel**: ✅
- **Validation**: Issue license, suspend, expire

#### P4-T08: License API
- **Desc**: POST /api/license/validate (Sanctum, rate limited), LicenseValidationService
- **Files**: `apps/hub/app/Http/Controllers/Api/LicenseController.php`, `routes/api.php`
- **Dep**: T07
- **Parallel**: ❌
- **Validation**: API return valid/status/features

#### P4-T09: Dashboard widgets (LeaderP)
- **Desc**: Stats overview, recent inquiries, low stock, expiring licenses, charts
- **Files**: `apps/hub/app/Filament/Widgets/*`
- **Dep**: T03, T05, T07
- **Parallel**: ❌
- **Validation**: Dashboard menampilkan stats

#### P4-T10: Role & permission (Shield)
- **Desc**: Setup roles (super, content, sales, inventory, accounting, hr, company, license), policies per resource
- **Files**: `apps/hub/app/Filament/Resources/*Policy.php`, config shield
- **Dep**: T01
- **Parallel**: ❌
- **Validation**: Role-based access berfungsi

### Batch 4B: LeadGym (apps/gym) — parallel dengan 4A

#### P4-T11: Configure stancl/tenancy + Gym Filament panel
- **Desc**: Setup tenancy (single-DB, tenant_id, subdomain identification), GymPanelProvider (per-tenant, theme)
- **Files**: `apps/gym/config/tenancy.php`, `app/Providers/Filament/GymPanelProvider.php`
- **Dep**: P0-T06
- **Parallel**: ✅
- **Validation**: Tenant subdomain resolve, panel load per-tenant

#### P4-T12: Migrations — LeadGym (tenants, gym_users, members, plans, subs, invoices, dll)
- **Desc**: Migrations untuk tenants, gym_users, members, plans, subscriptions, services, member_services, invoices, invoice_items, invoice_transactions, enquiries, follow_ups, expenses, check_ins
- **Files**: `core/database/migrations/0037_*` s/d `0050_*`
- **Dep**: P0-T02
- **Parallel**: ✅
- **Validation**: `php artisan migrate` berhasil

#### P4-T13: Member module
- **Desc**: MemberResource (CRUD, photo, QR code, import/export, history)
- **Files**: `apps/gym/app/Filament/Resources/MemberResource.php`
- **Dep**: T11, T12
- **Parallel**: ✅ (dengan T14, T15, T16)
- **Validation**: CRUD member, QR generate, tenant-scoped

#### P4-T14: Plan & Subscription module
- **Desc**: PlanResource, SubscriptionResource (create, renewal, freeze, transfer, expiry notification)
- **Files**: `apps/gym/app/Filament/Resources/PlanResource.php`, `SubscriptionResource.php`
- **Dep**: T11, T12
- **Parallel**: ✅
- **Validation**: CRUD plan, subscription lifecycle berfungsi

#### P4-T15: Invoice module
- **Desc**: InvoiceResource (numbering per-tenant, PDF, payment record)
- **Files**: `apps/gym/app/Filament/Resources/InvoiceResource.php`
- **Dep**: T11, T12, T14
- **Parallel**: ✅
- **Validation**: Create invoice, PDF generate, payment record

#### P4-T16: Service, Enquiry, Expense, Check-in modules
- **Desc**: ServiceResource, MemberServiceResource, EnquiryResource, FollowUpResource, ExpenseResource, CheckInResource
- **Files**: `apps/gym/app/Filament/Resources/*`
- **Dep**: T11, T12
- **Parallel**: ✅
- **Validation**: CRUD semua berfungsi, check-in validate subscription

#### P4-T17: Dashboard (LeadGym)
- **Desc**: Stats (active members, revenue, retention, capacity), charts
- **Files**: `apps/gym/app/Filament/Widgets/*`
- **Dep**: T13, T14, T15
- **Parallel**: ❌
- **Validation**: Dashboard menampilkan stats & charts

#### P4-T18: Tenant Settings & License validation
- **Desc**: TenantSettingsResource (branding, capacity, hours, license key), LicenseValidatorService (call LeaderP API), daily sync cron
- **Files**: `apps/gym/app/Filament/Resources/TenantSettingResource.php`, `app/Services/LicenseValidatorService.php`, `app/Console/Commands/SyncLicenses.php`
- **Dep**: T08, T11
- **Parallel**: ❌
- **Validation**: Input license → validate via API, sync cron berjalan, read-only mode if expired

#### P4-T19: Role & permission per-tenant (Shield)
- **Desc**: Setup roles (gym_owner, gym_staff, gym_trainer), scoped per-tenant
- **Files**: `apps/gym/app/Filament/Resources/*Policy.php`
- **Dep**: T11
- **Parallel**: ❌
- **Validation**: Role-based access per-tenant berfungsi

#### P4-T20: Customize UI sesuai design system
- **Desc**: Override Filament theme dengan design tokens (accent merah, Inter, radius lembut, focus ring)
- **Files**: `apps/gym/resources/views/filament/theme-styles.blade.php`, `app/Providers/Filament/GymPanelProvider.php`
- **Dep**: T11
- **Parallel**: ✅
- **Validation**: UI konsisten dengan design system preview

---

## Phase 5: Integration & Polish (Minggu 10)

### P5-T01: Verify shared DB integration
- **Desc**: Test: product di hub → tampil di website, inquiry → hub, license → gym
- **Files**: —
- **Dep**: P4 (semua)
- **Parallel**: ❌
- **Validation**: Semua integration test pass

### P5-T02: Google Translate widget + manual translation override
- **Desc**: Embed Google Translate, implement field_en di product/blog/page, admin input EN
- **Files**: `apps/website/resources/views/partials/translate-widget.blade.php`, Filament resource tab English
- **Dep**: P3
- **Parallel**: ✅
- **Validation**: Toggle EN, manual override dipakai jika ada, fallback GT

### P5-T03: SEO structured data (JSON-LD)
- **Desc**: Product, Article, Organization, Breadcrumb JSON-LD
- **Files**: `apps/website/resources/views/seo/*.blade.php`
- **Dep**: P2-T08
- **Parallel**: ✅
- **Validation**: View source → JSON-LD ada, valid di Schema.org validator

### P5-T04: Performance optimization
- **Desc**: Cache config/route/view, Redis (jika ada), image optimization pipeline
- **Files**: config cache, .env
- **Dep**: P4
- **Parallel**: ✅
- **Validation**: PageSpeed Insights > 80

### P5-T05: Accessibility audit (WCAG 2.1 AA)
- **Desc**: Audit kontras, keyboard nav, ARIA, reduced motion
- **Files**: —
- **Dep**: P4
- **Parallel**: ✅
- **Validation**: Lighthouse accessibility > 90

### P5-T06: Cross-browser & responsive testing
- **Desc**: Test Chrome, Firefox, Safari, Edge; mobile, tablet, desktop
- **Files**: —
- **Dep**: P4
- **Parallel**: ✅
- **Validation**: Semua browser & breakpoint OK

### P5-T07: Error pages (404, 500, 503)
- **Desc**: Custom error pages sesuai design system
- **Files**: `apps/website/resources/views/errors/*.blade.php`
- **Dep**: P1-T03
- **Parallel**: ✅
- **Validation**: 404/500/503 tampil sesuai design

### P5-T08: Documentation (admin guide, user guide, gym owner guide)
- **Desc**: Dokumentasi penggunaan untuk admin LeaderP, admin website, gym owner LeadGym
- **Files**: `docs/guides/*.md`
- **Dep**: P4
- **Parallel**: ✅
- **Validation**: Dokumen lengkap & akurat

---

## Phase 6: Deployment (Minggu 11)

### P6-T01: Setup cPanel VPS (subdomain, PHP, MySQL)
- **Desc**: Create 3 subdomain + wildcard, install PHP 8.2+, MySQL 8.0, extensions
- **Files**: —
- **Dep**: P5
- **Parallel**: ❌
- **Validation**: Subdomain resolve, PHP/MySQL ready

### P6-T02: Build assets (lokal/CI) & commit
- **Desc**: `pnpm build:all`, commit `public/build/` + `dist/`
- **Files**: `apps/*/public/build/*`
- **Dep**: P5
- **Parallel**: ✅
- **Validation**: Build artifacts di repo

### P6-T03: Deploy website
- **Desc**: Git pull, composer install, migrate, seed, storage:link, optimize
- **Files**: —
- **Dep**: T01, T02
- **Parallel**: ✅ (dengan T04, T05)
- **Validation**: https://leaderhub.id live

### P6-T04: Deploy hub (LeaderP)
- **Desc**: Git pull, composer install, migrate, storage:link, optimize
- **Files**: —
- **Dep**: T01, T02
- **Parallel**: ✅
- **Validation**: https://erp.leaderhub.id live

### P6-T05: Deploy gym (LeadGym)
- **Desc**: Git pull, composer install, migrate, storage:link, optimize
- **Files**: —
- **Dep**: T01, T02
- **Parallel**: ✅
- **Validation**: https://gym.leaderhub.id live, tenant subdomain resolve

### P6-T06: Setup cron (scheduler, queue, sitemap, license sync)
- **Desc**: Cron jobs untuk scheduler, queue:work --stop-when-empty, sitemap, license sync
- **Files**: —
- **Dep**: T03, T04, T05
- **Parallel**: ❌
- **Validation**: Cron berjalan, queue process

### P6-T07: Setup backup (DB + files)
- **Desc**: Cron backup DB + files, atau spatie/laravel-backup
- **Files**: —
- **Dep**: T03, T04, T05
- **Parallel**: ❌
- **Validation**: Backup ter-generate, restore test

### P6-T08: SSL (cPanel AutoSSL)
- **Desc**: Run AutoSSL untuk semua subdomain + wildcard
- **Files**: —
- **Dep**: T01
- **Parallel**: ✅
- **Validation**: HTTPS valid, no warning

### P6-T09: End-to-end testing di production
- **Desc**: Test semua flow di production (website, hub, gym, license)
- **Files**: —
- **Dep**: T03-T08
- **Parallel**: ❌
- **Validation**: Post-deployment checklist (06-deployment.md §11) all pass

---

## 3. Dependency Graph Summary

```
P0 (Setup) → P1 (Foundation) → P2 (Content) → P3 (Blog+Inquiry)
                                                    ↓
                              P4 (ERP + Gym, parallel 4A+4B)
                                          ↓
                                    P5 (Integration)
                                          ↓
                                    P6 (Deployment)
```

## 4. Parallel Execution Batches

| Batch | Tasks | Kapan |
|-------|-------|-------|
| 1 | P0-T01, T02, T03 | Phase 0 awal |
| 2 | P0-T04, T05, T06 | Phase 0 (setelah T01-T03) |
| 3 | P1-T01, T02 | Phase 1 awal |
| 4 | P2-T01, T02 | Phase 2 awal |
| 5 | P2-T04, T05, T06 | Phase 2 (product pages) |
| 6 | P3-T01, T02 | Phase 3 awal |
| 7 | P3-T03, T04 | Phase 3 (blog + inquiry cart) |
| 8 | P4-T03, T04, T05, T06, T07 (LeaderP) + P4-T13, T14, T15, T16 (LeadGym) | Phase 4 (parallel ERP + Gym) |
| 9 | P5-T02, T03, T04, T05, T06, T07, T08 | Phase 5 (polish, parallel) |
| 10 | P6-T03, T04, T05 | Phase 6 (deploy parallel) |