# LeaderHub — Project Overview

> **Version**: 1.0 | **Status**: Planning | **Last Updated**: 2026-07-26

## 1. Ringkasan Proyek

**LeaderHub** adalah platform digital terintegrasi untuk usaha distributor
alat fitness. Platform ini menyatukan tiga kebutuhan dalam satu monorepo:

1. **Website pemasaran** (`leaderhub.id`) — katalog produk, gallery, blog,
   sistem inquiry (lead generation), multi-bahasa.
2. **LeaderP — ERP** (`erp.leaderhub.id`) — mengelola operasional perusahaan
   distributor: inventory, pembelian (USD), penjualan (IDR), akuntansi,
   HRM, after-sales, dokumen, lisensi LeadGym.
3. **LeadGym — Software Gym** (`gym.leaderhub.id` + per-customer subdomain) —
   produk komersial yang dijual/bundling dengan paket alat gym. Multi-tenant:
   satu instance melayani banyak gym pelanggan dengan data terisolasi.

Ketiga aplikasi berbagi satu database (MySQL 8.0) via shared `core` package
(models, services, migrations) sehingga data produk di ERP langsung tampil di
website, dan inquiry dari website langsung masuk ke ERP.

## 2. Visi & Misi

| Aspek | Detail |
|-------|--------|
| **Visi** | Menjadi etalase digital premium sekaligus sistem operasi terintegrasi untuk distributor alat fitness kelas atas |
| **Misi** | Menyajikan pengalaman browsing yang elegan, memberikan admin kendali penuh atas operasional, dan menyediakan software gym sebagai value-add product yang dapat dijual |
| **Brand promise** | "Modern Premium" — bersih, tenang, terkurasi di setiap interaksi |

## 3. Stakeholder

| Role | Deskripsi |
|------|-----------|
| **Owner** | Pemilik bisnis, pengambil keputusan final |
| **Admin konten** | Mengelola produk, gallery, blog, banner, settings website |
| **Admin sales** | Menangani inquiry, sales order, after-sales, instalasi |
| **Admin ERP** | Mengelola inventory, purchase, accounting, HRM, dokumen |
| **Admin lisensi** | Mengelola lisensi & tenant LeadGym (customer gym) |
| **Gym owner (customer)** | Pelanggan yang beli LeadGym — admin gym mereka sendiri |
| **Gym staff** | Staff gym customer — manage member, subscription, check-in |
| **Pengunjung** | Calon pembeli alat fitness yang browse katalog & kirim inquiry |

## 4. Ruang Lingkup

### 4.1 In-Scope (Dibangun)

**Website publik (leaderhub.id)**
- Katalog produk, gallery, blog, inquiry, static pages
- Sistem inquiry dengan penomoran otomatis (INQ-YYYY-XXXXX)
- Multi-bahasa: Indonesia (utama) + EN toggle
- Manajemen konten via admin (Filament di LeaderP atau panel website)
- Integrasi WhatsApp & email untuk inquiry

**LeaderP — ERP (erp.leaderhub.id)**
- Inventory: products, warehouses, stock movements, suppliers
- Purchases: PO dalam USD, receive goods, landed cost
- Sales: SO dalam IDR (dari inquiry/direct), activities, instalasi, warranty
- Documents: invoicing, surat/kontrak/sertifikat, PDF generation
- Accounting: chart of accounts, double-entry journal, multi-currency, laporan
- After-sales: support tickets, maintenance, warranty claims
- Company admin: contacts, third parties, agreements
- HRM: employees, departments
- **License Manager**: issue/track lisensi LeadGym per customer
- DB management (superadmin): backup, restore, optimize

**LeadGym — Software Gym (gym.leaderhub.id + per-customer)**
- Multi-tenant: satu instance, banyak gym, data terisolasi via `tenant_id`
- Members: profil, foto, membership card
- Plans: tipe membership (monthly, quarterly, annual, PT sessions)
- Subscriptions: aktif membership, renewal, freeze, transfer
- Services: personal training, kelas, locker, dll
- Invoices: pembayaran membership, transaksi
- Enquiries & Follow-ups: lead management gym
- Expenses: pencatatan pengeluaran gym
- Check-in: absensi member (barcode/QR)
- Dashboard: stats member aktif, revenue, retention
- License validation: sync ke LeaderP (status lisensi, expiry)

### 4.2 Out-of-Scope (Tidak Dibangun)
- Online payment gateway di website (lead generation, bukan e-commerce)
- Customer login di website (guest only)
- Shopping cart dengan checkout pembayaran
- Mobile app native (responsive web saja)
- Real-time chat (cukup WA/email)

### 4.3 Future / Nice-to-Have
- Mobile app via NativePHP / Capacitor
- AR product preview
- Room planner tool (seperti Technogym)
- LeadGym marketplace add-ons (class booking, nutrition tracking)
- White-label branding per tenant gym

## 5. Arsitektur Tingkat Tinggi

```
┌──────────────────────────────────────────────────────────────────┐
│                        leaderhub.id                              │
│  ┌────────────────┐  ┌────────────────┐  ┌───────────────────┐   │
│  │  apps/website  │  │   apps/hub     │  │   apps/gym        │   │
│  │  (Laravel 12)  │  │  LeaderP (ERP) │  │  LeadGym (multi-  │   │
│  │  Vue + Tailwind│  │  Filament 5    │  │  tenant, Filament)│   │
│  │  Public catalog│  │  Admin + ERP   │  │  Gym management   │   │
│  └───────┬────────┘  └───────┬────────┘  └────────┬──────────┘   │
│          │                   │                    │               │
│          └───────────┬───────┴────────────────────┘               │
│                      │                                           │
│              ┌───────┴────────┐                                  │
│              │   core/         │  Shared package                  │
│              │   Models        │  (Inquiry, Product, Gallery,      │
│              │   Services      │   Banner, Tenant, License)        │
│              │   Migrations    │                                  │
│              └───────┬────────┘                                  │
│                      │                                           │
│              ┌───────┴────────┐                                  │
│              │  MySQL 8.0      │  Shared database                │
│              └────────────────┘                                  │
└──────────────────────────────────────────────────────────────────┘
```

Detail lengkap: lihat `02-architecture.md`

## 6. Tech Stack Summary

| Layer | Teknologi | Versi |
|-------|-----------|-------|
| **Website** | Laravel 12 + Vue 3 + Tailwind 4 + Alpine.js | PHP 8.2+ |
| **LeaderP (ERP)** | Laravel 12 + Filament 5 | PHP 8.2+ |
| **LeadGym** | Laravel 12 + Filament 5 (multi-tenant) | PHP 8.2+ |
| **Core (shared)** | Composer path package | — |
| **Database** | MySQL | 8.0 (kompatibel MariaDB 10.6+) |
| **Package manager** | pnpm (workspace monorepo) | 9.x |
| **Deployment** | cPanel VPS (utama) / aaPanel (alternatif) | — |
| **Multi-bahasa** | Hybrid (manual + Google Translate) | — |
| **Multi-tenant** | Stancl/Tenancy (LeadGym) | 3.x |

Detail lengkap: lihat `02-architecture.md`

## 7. Glossary

| Istilah | Definisi |
|---------|----------|
| **Inquiry** | Daftar minat produk yang dikirim pengunjung ke admin via WA/email |
| **Inquiry Cart** | Daftar sementara produk (localStorage) |
| **Inquiry Number** | Format `INQ-YYYY-XXXXX` |
| **LeaderP** | ERP distributor di `erp.leaderhub.id` |
| **LeadGym** | Software gym multi-tenant, produk komersial |
| **Tenant** | Satu gym customer di LeadGym (data terisolasi via `tenant_id`) |
| **License** | Lisensi LeadGym per tenant — issue dari LeaderP, expiry, feature toggle |
| **Core** | Shared package berisi Model, Service, Migration |
| **Featured Product** | Produk yang ditampilkan di homepage |
| **Lead generation** | Model bisnis website: kumpulkan minat → follow-up manual |
| **Monorepo** | 1 repository berisi multiple apps + shared core |
| **Modern Premium** | Design direction: netral-first, aksen merah secukupnya, tipografi rapat |
| **Landed cost** | Total biaya produk impor (harga USD + shipping + bea + konversi IDR) |

## 8. Dokumentasi Terkait

| Dokumen | Deskripsi |
|---------|-----------|
| [01-product-requirements.md](01-product-requirements.md) | PRD lengkap dengan user stories & acceptance criteria |
| [02-architecture.md](02-architecture.md) | Arsitektur monorepo, tech stack, folder structure |
| [03-database-schema.md](03-database-schema.md) | ERD, semua tabel, relasi, indexing strategy |
| [04-design-system.md](04-design-system.md) | Design tokens, komponen, pola UI |
| [05-roadmap.md](05-roadmap.md) | Fase pengembangan, milestone, timeline |
| [06-deployment.md](06-deployment.md) | Panduan deploy ke cPanel VPS + dev env lokal |
| [07-features/](07-features/) | Spesifikasi detail per modul fitur |
| [08-implementation-plan.md](08-implementation-plan.md) | Task breakdown detail per fase |
| [design-system/preview.html](design-system/preview.html) | Interactive HTML preview design system |