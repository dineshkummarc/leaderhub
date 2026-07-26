# LeaderHub — Arsitektur Teknis

> **Version**: 1.0 | **Status**: Draft | **Last Updated**: 2026-07-26

## 1. Arsitektur Monorepo

LeaderHub menggunakan **monorepo dengan shared core package**. Satu repository
berisi tiga aplikasi Laravel 12 terpisah + satu shared package + satu design
tokens package.

```
leaderhub/                              ← monorepo root
│
├── core/                               ← SHARED PACKAGE (leaderhub/core)
│   ├── src/
│   │   ├── Models/                      ← ALL models (website + ERP + gym)
│   │   ├── Services/                    ← Business logic
│   │   ├── Enums/                       ← Status, role, type enums
│   │   ├── Traits/                      ← Reusable traits (BelongsToTenant, dll)
│   │   └── Contracts/                   ← Interfaces
│   ├── database/
│   │   ├── migrations/                 ← ALL migrations (website + ERP + gym)
│   │   ├── seeders/
│   │   └── factories/
│   └── composer.json
│
├── packages/
│   └── design-tokens/                  ← Shared design tokens (CSS/JS/SCSS/PHP)
│       ├── tokens.json                 ← Sumber kebenaran
│       ├── build.js
│       └── dist/                       ← tokens.css, tokens.js, filament-theme.php
│
├── apps/
│   ├── website/                        ← PUBLIC WEBSITE + Content Admin
│   │   ├── app/
│   │   │   ├── Http/Controllers/        ← Public controllers
│   │   │   ├── Filament/Resources/      ← Content admin (gallery, banner, settings, inquiry, pages, blog)
│   │   │   └── Providers/
│   │   ├── resources/
│   │   │   ├── views/                   ← Blade + Vue components
│   │   │   ├── js/                      ← Vue app, inquiry cart logic
│   │   │   └── css/
│   │   ├── routes/web.php
│   │   ├── public/                      ← leaderhub.id
│   │   ├── tailwind.config.js           ← import @leaderhub/design-tokens
│   │   └── composer.json
│   │
│   ├── hub/                            ← LEADERP — ERP ADMIN
│   │   ├── app/
│   │   │   ├── Filament/Resources/
│   │   │   │   ├── Inventory/           ← Products, warehouses, stock, suppliers
│   │   │   │   ├── Purchases/           ← Purchase orders (USD)
│   │   │   │   ├── Sales/               ← Sales orders, activities, installations, warranties
│   │   │   │   ├── Documents/           ← Invoicing, company documents
│   │   │   │   ├── Accounting/          ← Chart of accounts, journal, reports
│   │   │   │   ├── AfterSales/          ← Support tickets, maintenance, warranty claims
│   │   │   │   ├── CompanyAdmin/        ← Contacts, third parties, agreements
│   │   │   │   ├── HRM/                 ← Employees, departments
│   │   │   │   └── Licenses/            ← LeadGym license manager
│   │   │   ├── Services/                ← MultiCurrency, Ledger, InstallService
│   │   │   └── Providers/
│   │   ├── routes/
│   │   ├── public/                      ← erp.leaderhub.id
│   │   └── composer.json
│   │
│   └── gym/                            ← LEADGYM — GYM SOFTWARE (multi-tenant)
│       ├── app/
│       │   ├── Filament/Resources/
│       │   │   ├── Members/
│       │   │   ├── Plans/
│       │   │   ├── Subscriptions/
│       │   │   ├── Services/
│       │   │   ├── Invoices/
│       │   │   ├── Enquiries/
│       │   │   ├── Expenses/
│       │   │   └── CheckIn/
│       │   ├── Services/                ← LicenseValidator, TenantContext
│       │   └── Providers/
│       ├── routes/
│       ├── public/                      ← gym.leaderhub.id + {tenant}.gym.leaderhub.id
│       └── composer.json
│
├── storage/                            ← shared storage (symlinked)
├── pnpm-workspace.yaml                 ← pnpm monorepo config
├── composer.json                       ← root (path repositories)
├── package.json                        ← root (pnpm workspace)
└── .gitignore
```

### 1.1 Pnpm Workspace
```yaml
# pnpm-workspace.yaml
packages:
  - 'packages/*'
  - 'apps/website'
  # apps/hub & apps/gym: asset build via Vite, package.json masing-masing
```

Root `package.json`:
```json
{
  "name": "leaderhub",
  "private": true,
  "scripts": {
    "dev:website": "pnpm --filter ./apps/website dev",
    "build:website": "pnpm --filter ./apps/website build",
    "build:hub": "pnpm --filter ./apps/hub build",
    "build:gym": "pnpm --filter ./apps/gym build",
    "build:all": "pnpm -r --filter './apps/*' build",
    "build:tokens": "pnpm --filter @leaderhub/design-tokens build"
  }
}
```

### 1.2 Composer Path Repositories
```json
// root composer.json
{
  "repositories": [
    { "type": "path", "url": "./core" },
    { "type": "path", "url": "./packages/design-tokens" }
  ]
}
```

Setiap app (`apps/website`, `apps/hub`, `apps/gym`) require:
```json
{
  "require": {
    "leaderhub/core": "@dev",
    "leaderhub/design-tokens": "@dev"
  }
}
```

## 2. Tech Stack Detail

### 2.1 Website (apps/website)

| Komponen | Teknologi | Versi | Catatan |
|----------|-----------|-------|---------|
| Framework | Laravel | 12.x | PHP 8.2+ |
| Admin panel | Filament | 5.x | Content admin (gallery, banner, settings, inquiry, blog) |
| Frontend | Vue | 3.x | SPA-like via Inertia atau Blade + Vue islands |
| CSS | Tailwind CSS | 4.x | Modern Premium design system |
| JS | Alpine.js | 3.x | Interaktivitas ringan |
| Slider | Swiper.js | 11.x | Hero banner |
| Lightbox | GLightbox | 3.x | Gallery zoom |
| Font | Google Fonts | — | Inter (semua) + ui-monospace |
| Build | Vite | 6.x | pnpm |

### 2.2 LeaderP / Hub (apps/hub)

| Komponen | Teknologi | Versi | Catatan |
|----------|-----------|-------|---------|
| Framework | Laravel | 12.x | PHP 8.2+ |
| Admin panel | Filament | 5.x | ERP admin |
| CSS | Tailwind CSS | 4.x | Modern Premium design system (Filament theme) |
| PDF | barryvdh/laravel-dompdf | 3.x | Invoice, document generation |
| Barcode | milon/barcode | — | Product barcode |
| Excel | maatwebsite/excel | 3.x | Export |
| Accounting | custom ledger | — | Simple double-entry ledger |
| Shield | bezhansalleh/filament-shield | 5.x | Role & permission |

### 2.3 LeadGym (apps/gym)

| Komponen | Teknologi | Versi | Catatan |
|----------|-----------|-------|---------|
| Framework | Laravel | 12.x | PHP 8.2+ |
| Admin panel | Filament | 5.x | Gym admin (per tenant) |
| Multi-tenant | stancl/tenancy | 3.x | Database-per-tenant ATAU single-DB + tenant_id |
| CSS | Tailwind CSS | 4.x | Modern Premium design system |
| PDF | barryvdh/laravel-dompdf | 3.x | Invoice member, membership card |
| Shield | bezhansalleh/filament-shield | 5.x | Role per tenant |
| Base | fork laravel-gymie | — | Members, Plans, Subscriptions, Services, Invoices |

### 2.4 Core (shared package)

| Komponen | Teknologi | Catatan |
|----------|-----------|---------|
| Package type | Composer path repository | Local symlink |
| Namespace | `Leaderhub\Core\` | PSR-4 autoload |
| Models | Eloquent | ALL models (website + ERP + gym) |
| Migrations | Laravel migrations | ALL migrations di core |
| Services | PHP classes | InquiryService, MultiCurrencyService, LedgerService, LicenseService |

### 2.5 Database

| Komponen | Teknologi | Catatan |
|----------|-----------|---------|
| DBMS | MySQL | 8.0 (kompatibel MariaDB 10.6+) |
| Charset | utf8mb4 | Support emoji & unicode |
| Collation | utf8mb4_unicode_ci | — |
| Engine | InnoDB | Foreign key support |
| Management | Filament resource (superadmin) | Backup, restore, data management |

### 2.6 Dev Environment (Lokal — Gratis/Open Source)

Karena belum langganan VPS, development & testing pakai tools lokal gratis:

| Komponen | Tool | Catatan |
|----------|------|---------|
| PHP | Native (8.2+) atau Herd (gratis, macOS) / XAMPP | — |
| Database | MySQL 8 via Docker / MariaDB native / SQLite (testing) | — |
| Redis | Docker (opsional) | Cache |
| Node.js | nvm + Node 20 LTS | pnpm |
| Composer | 2.x | — |
| **Alternatif all-in-one** | **Docker Compose** (Laravel Sail) | 1 perintah, replikasi production |
| **Alternatif desktop** | **Laravel Herd** (gratis, macOS/Windows) | PHP + Nginx, tanpa Docker |

Rekomendasi: **Docker Compose** (Sail) — replikasi environment production paling
akurat, gratis, open source. Detail setup: lihat `06-deployment.md` bagian Dev.

## 3. Role-Based Access Control

### 3.1 Roles

#### Website & LeaderP (shared auth, satu user table)
| Role | Domain | Akses |
|------|--------|-------|
| `super_admin` | All | Semua modul + user management + DB management + license manager |
| `content_admin` | Website | Gallery, banners, blog, pages, settings, translations |
| `sales_admin` | Website + Hub | Inquiries, Sales, After Sales, Documents |
| `inventory_admin` | Hub | Inventory, Purchases, Warehouses, Suppliers |
| `accounting_admin` | Hub | Accounting, Invoicing, Financial reports |
| `hr_admin` | Hub | HRM, Employees, Departments |
| `company_admin` | Hub | Company admin, Third parties, Agreements |
| `license_admin` | Hub | License manager (LeadGym) |

#### LeadGym (per-tenant auth, terpisah)
| Role | Domain | Akses |
|------|--------|-------|
| `gym_owner` | Tenant | Semua modul gym + settings tenant |
| `gym_staff` | Tenant | Members, subscriptions, check-in, invoices (view) |
| `gym_trainer` | Tenant | Members assigned, services (PT sessions) |

### 3.2 Module → Role Matrix (LeaderP)

| Modul | super | content | sales | inventory | accounting | hr | company | license |
|-------|-------|---------|-------|-----------|------------|----|---------|---------|
| Gallery/Banner/Pages | ✅ | ✅ | | | | | | |
| Blog | ✅ | ✅ | | | | | | |
| Settings | ✅ | ✅ | | | | | | |
| Inquiries | ✅ | | ✅ | | | | | |
| DB Management | ✅ | | | | | | | |
| Inventory | ✅ | | | ✅ | | | | |
| Purchases | ✅ | | | ✅ | | | | |
| Sales | ✅ | | ✅ | | | | | |
| After Sales | ✅ | | ✅ | | | | | |
| Documents | ✅ | | ✅ | | ✅ | | | |
| Accounting | ✅ | | | | ✅ | | | |
| HRM | ✅ | | | | | ✅ | | |
| Company Admin | ✅ | | | | | | ✅ | |
| License Manager | ✅ | | | | | | | ✅ |

## 4. ERP Module Structure (LeaderP)

### 4.1 Inventory (Priority A)
```
Inventory Module:
├── Products (name, SKU, barcode, brand, material, description, purchase price USD, sale price IDR, serial, warranty)
├── Warehouses (multi-warehouse, location, stock levels)
├── Stock Movements (in: receive/return/transfer; out: delivery/damage/transfer)
├── Suppliers (contact, currency USD, purchase history)
└── Purchase Orders (PO USD, receive → stock in, landed cost USD→IDR)
```

### 4.2 Sales (Priority B)
```
Sales Module:
├── Sales Orders (IDR, from inquiry or direct, delivery → stock out, invoice)
├── Sales Activities (Lead → Contact → Demo → Quote → Order, activity log, follow-up)
├── Installation (schedule, technician, checklist, sign-off)
├── Warranty (auto-create on install, period tracking, claims, repair/replacement)
└── After Sales / Support (tickets, maintenance schedule, follow-up, resolution)
```

### 4.3 Documents (Priority C)
```
Documents Module:
├── Invoicing (sales invoice from SO, purchase invoice from PO, numbering, PDF)
├── Company Documents (surat, kontrak, sertifikat/warranty card, templates)
└── Document Tracking (issued log, status, digital archive)
```

### 4.4 Accounting (Priority D)
```
Accounting Module:
├── Chart of Accounts
├── Journal Entries (double-entry)
├── Multi-currency (USD/IDR)
├── Reports (Laba Rugi, Neraca, Arus Kas)
└── Bank Reconciliation
```

### 4.5 Company Administration (Priority E)
```
Company Admin Module:
├── Company Contacts (pabrik/supplier, third-party, service, government)
├── Agreements (contract, MOU, renewal reminders)
└── Company Profile (info, legal, compliance)
```

### 4.6 License Manager (Priority B — untuk LeadGym)
```
License Manager Module:
├── Customers (gym owner contacts, company info)
├── Licenses (issue key, expiry, feature toggle, status: active/suspended/expired)
├── Activations (activation log, device/instance, deactivation)
├── Subscription Plans (monthly/yearly, feature tiers: basic/pro/enterprise)
└── Sync (API endpoint untuk LeadGym validate license)
```

## 5. LeadGym Module Structure (Multi-tenant)

### 5.1 Multi-Tenancy Strategy
**Single database + `tenant_id` column** (bukan database-per-tenant) untuk
efisiensi maintenance di shared hosting cPanel. Alasan:
- cPanel shared hosting terbatas jumlah database
- Maintenance/update sekali untuk semua tenant
- Backup satu database
- Isolasi data via `tenant_id` + global scope

Pakai `stancl/tenancy` 3.x dengan mode **single-database multi-tenancy**:
- Tenant identified via subdomain (`{tenant}.gym.leaderhub.id`) atau path
- `BelongsToTenant` trait + global scope auto-filter
- Tenant settings per gym (name, logo, branding override)

### 5.2 Modules (per tenant)
```
LeadGym:
├── Members (profil, foto, membership card, emergency contact)
├── Plans (monthly, quarterly, annual, PT sessions, custom)
├── Subscriptions (active, renewal, freeze, transfer, history)
├── Services (personal training, kelas, locker, towel)
├── Invoices (membership payment, transactions, PDF)
├── Enquiries (gym lead, walk-in)
├── Follow-ups (lead follow-up tracking)
├── Expenses (operational expenses)
├── Check-in (barcode/QR attendance, capacity tracking)
├── Dashboard (active members, revenue, retention, capacity)
└── Settings (tenant branding, plan config, staff)
```

### 5.3 License Validation Flow
```
1. Gym owner login ke LeadGym (tenant subdomain)
2. LeadGym cek license key (stored di tenant settings)
3. LeadGym call LeaderP API: POST /api/license/validate
   - LeaderP cek: key valid? expiry? status? feature tier?
   - Return: { valid: true, features: [...], expiry: ... }
4. Jika valid → akses sesuai feature tier
5. Jika expired/suspended → read-only mode + banner "Lisensi berakhir"
6. LeaderP bisa suspend/expire dari License Manager → sync ke LeadGym
```

## 6. Data Flow

### 6.1 Product Flow
```
LeaderP: Admin create product (inventory module)
  └→ Save ke products table (core migration)
  └→ Set stock per warehouse

Website: ProductController
  └→ Query products table (via core Model)
  └→ Render ke Vue (Modern Premium style)
```

### 6.2 Sales Flow
```
Website: Visitor submit inquiry
  └→ Save ke inquiries table

LeaderP: Sales admin view inquiry
  └→ Convert inquiry → Sales Order
  └→ Schedule installation
  └→ Auto-create warranty
  └→ After-sales support tracking
```

### 6.3 Purchase Flow
```
LeaderP: Inventory admin create Purchase Order (USD)
  └→ Receive goods → stock in
  └→ Landed cost calculation (USD → IDR)
  └→ Accounting journal entry (auto)
```

### 6.4 LeadGym License Flow
```
LeaderP: License admin issue license key untuk gym customer
  └→ Save ke licenses table (core)
  └→ Customer deploy LeadGym / diberi tenant subdomain

LeadGym: Gym owner input license key
  └→ LeadGym validate via LeaderP API
  └→ Activate tenant, set feature tier

LeadGym: Daily sync (cron)
  └→ Cek status license (active/suspended/expired)
  └→ Update tenant access accordingly
```

## 7. Deployment Architecture

### 7.1 Dev (Lokal — Docker Compose / Sail)
```
Local:
  PHP 8.2 (Docker) → localhost:8000 (website), localhost:8001 (hub), localhost:8002 (gym)
  MySQL 8 (Docker) → leaderhub_db
  Redis (Docker, opsional) → cache
  Vite dev server → HMR untuk Vue
```

### 7.2 Production (cPanel VPS)
```
Site 1: leaderhub.id → apps/website/public (PHP 8.2+)
Site 2: erp.leaderhub.id → apps/hub/public (PHP 8.2+)
Site 3: gym.leaderhub.id → apps/gym/public (PHP 8.2+)
  + wildcard *.gym.leaderhub.id untuk tenant subdomain
Database: leaderhub_db (MySQL 8.0)
Storage: shared symlink
SSL: Let's Encrypt (cPanel AutoSSL)
Queue: cron-based (tanpa Supervisor di shared, Supervisor di VPS)
```

Detail: lihat `06-deployment.md`

## 8. Security Architecture

| Layer | Mechanism |
|-------|-----------|
| Transport | HTTPS (Let's Encrypt / cPanel AutoSSL) |
| Auth | Filament auth, role-based (Filament Shield) |
| Multi-tenant isolation | Global scope `tenant_id`, BelongsToTenant trait |
| License API | API token (Laravel Sanctum), rate limited |
| CSRF | Laravel CSRF token |
| XSS | Vue escaping, purified HTML for rich text |
| SQL injection | Eloquent parameter binding |
| File upload | Validate mime, size, store outside webroot |
| Rate limit | Inquiry: 5/IP/jam, login: 5/menit, license API: 60/min |
| DB management | super_admin only, confirmation for destructive ops |

## 9. Caching Strategy

| Cache Key | Driver | TTL | Invalidation |
|-----------|--------|-----|--------------|
| Product list | file/redis | 60 min | Product update |
| Product detail | file/redis | 60 min | Product update |
| Settings | file/redis | 1440 min | Settings update |
| Blog list | file/redis | 30 min | Blog update |
| Exchange rate | file/redis | 60 min | Manual update |
| License status (LeadGym) | file | 1440 min | Daily sync / manual |
| Route/Config/View | file | permanent | Deploy |