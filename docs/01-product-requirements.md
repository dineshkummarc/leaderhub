# LeaderHub — Product Requirements Document

> **Version**: 1.0 | **Status**: Draft | **Last Updated**: 2026-07-26

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen ini mendefinisikan kebutuhan produk untuk LeaderHub — platform
terintegrasi yang menyatukan website pemasaran, ERP distributor (LeaderP),
dan software gym multi-tenant (LeadGym). Mencakup user stories, acceptance
criteria, dan prioritas untuk setiap modul.

### 1.2 Referensi
- `00-overview.md` — visi, ruang lingkup, glossary
- `02-architecture.md` — arsitektur teknis
- `03-database-schema.md` — skema database
- `04-design-system.md` — design system

## 2. Personas

### P1 — Pengunjung Website (Guest)
Calon pembeli alat fitness yang browse katalog. Tidak login. Mengumpulkan
minat di inquiry cart, kirim via WA/email.

### P2 — Admin Konten (Website)
Mengelola konten publik: produk, gallery, blog, banner, pages, settings,
terjemahan.

### P3 — Admin Sales (LeaderP)
Menangani inquiry masuk dari website, konversi ke sales order, jadwalkan
instalasi, kelola warranty & after-sales.

### P4 — Admin Inventory (LeaderP)
Mengelola produk, gudang, stock, supplier, purchase order (USD).

### P5 — Admin Accounting (LeaderP)
Mengelola akuntansi, invoicing, laporan keuangan, multi-currency.

### P6 — Admin Lisensi (LeaderP)
Mengelola customer LeadGym, issue lisensi, suspend/expire, feature tier.

### P7 — Gym Owner (Tenant LeadGym)
Pelanggan yang beli LeadGym. Admin gym mereka sendiri: member, plan,
subscription, invoice, staff, settings.

### P8 — Gym Staff (Tenant LeadGym)
Staff gym: check-in member, view subscription, input invoice.

### P9 — Gym Trainer (Tenant LeadGym)
Trainer: lihat member yang assigned, kelola PT sessions.

## 3. Functional Requirements

### 3.1 Website Publik

#### FR-W01: Homepage
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai pengunjung, saya ingin melihat homepage yang menampilkan brand, produk unggulan, kategori, dan artikel terbaru agar saya memahami apa yang ditawarkan |
| **Acceptance Criteria** | 1. Hero banner full-viewport dengan Swiper, 3-5 slide 2. Section "Featured Products" (6-8 produk) 3. Section "Shop by Category" 4. Section "Latest Stories" (3 blog post) 5. Load < 3 detik 6. Responsive mobile/desktop 7. Dark/light toggle 8. Language toggle ID/EN |

#### FR-W02: Product Catalog
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai pengunjung, saya ingin browse katalog produk dengan filter kategori, search, dan sort agar menemukan produk yang relevan |
| **Acceptance Criteria** | 1. Grid produk 3:4, 12 per page 2. Filter: kategori, brand, price range 3. Search by name/SKU 4. Sort: newest, price asc/desc, name 5. Pagination 6. Grayscale→color on hover 7. SEO meta per page 8. Cache 60 menit |

#### FR-W03: Product Detail
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai pengunjung, saya ingin melihat detail produk lengkap dengan gallery, spec, dan tombol inquiry agar saya bisa tambah ke daftar minat |
| **Acceptance Criteria** | 1. Image gallery (multiple, zoom) 2. Name, SKU, brand, material, description 3. Price IDR 4. Spec table 5. Related products 6. "Tambah ke Daftar Minat" button 7. "Hubungi via WA" button 8. SEO structured data (JSON-LD Product) 9. Breadcrumb |

#### FR-W04: Gallery
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai pengunjung, saya ingin melihat gallery foto produk/installasi/event agar melihat bukti kualitas |
| **Acceptance Criteria** | 1. Album filter 2. Masonry grid 3. Lightbox (GLightbox) 4. Lazy load 5. WebP |

#### FR-W05: Blog
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai pengunjung, saya ingin membaca artikel fitness/tips/review agar mendapat insight |
| **Acceptance Criteria** | 1. Listing editorial grid 2. Filter kategori & tag 3. Detail: rich content, featured image, reading time, related 4. SEO meta 5. Author info |

#### FR-W06: Inquiry System
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai pengunjung, saya ingin mengumpulkan produk yang menarik minat dan kirim daftar ke admin via WA/email agar di-follow-up |
| **Acceptance Criteria** | 1. "Tambah ke Daftar Minat" di product card & detail 2. Inquiry cart di localStorage 3. Floating inquiry bar (count + "Lihat Daftar") 4. "Daftar Minat Saya" page: list, hapus, edit qty 5. Inquiry form: nama, WA, email, pesan 6. Submit → generate INQ-YYYY-XXXXX 7. Kirim via wa.me link + email ke admin 8. Rate limit 5/IP/jam 9. Inquiry tersimpan di DB |

#### FR-W07: Multi-Bahasa
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai pengunjung, saya ingin toggle bahasa ID/EN agar nyaman membaca |
| **Acceptance Criteria** | 1. Toggle ID/EN di header 2. Default ID 3. Manual translation override (admin input EN) 4. Fallback Google Translate untuk yang belum diterjemahkan 5. Session-based preference |

#### FR-W08: Static Pages
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai pengunjung, saya ingin melihat halaman About, Contact, Terms, Privacy |
| **Acceptance Criteria** | 1. About (company profile) 2. Contact (form + map + address) 3. Terms & Privacy 4. CMS-managed (admin edit konten) 5. SEO meta |

### 3.2 LeaderP — ERP

#### FR-E01: Inventory Management
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai admin inventory, saya ingin mengelola produk, gudang, stock, supplier agar tahu ketersediaan |
| **Acceptance Criteria** | 1. CRUD produk (name, SKU, barcode, brand, material, desc, purchase price USD, sale price IDR, serial, warranty period) 2. Multi-warehouse 3. Stock level per warehouse 4. Stock movement log (in/out/transfer) 5. Low stock alert 6. Supplier CRUD (currency USD) 7. Barcode generate |

#### FR-E02: Purchase Order
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai admin inventory, saya ingin membuat PO dalam USD, receive goods, dan hitung landed cost agar tahu harga aktual |
| **Acceptance Criteria** | 1. Create PO (supplier, items USD, qty) 2. Receive goods → stock in 3. Landed cost calc (USD price + shipping + bea + konversi IDR) 4. PO status: draft/sent/received/partial/cancelled 5. PDF export 6. Auto journal entry (accounting) |

#### FR-E03: Sales Order
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai admin sales, saya ingin konversi inquiry ke SO, jadwalkan instalasi, dan generate invoice agar proses penjualan tertutup |
| **Acceptance Criteria** | 1. Create SO IDR (from inquiry or direct) 2. Delivery → stock out 3. Invoice generation 4. SO status: draft/confirmed/delivered/invoiced/closed 5. Activity log per SO 6. Follow-up scheduling |

#### FR-E04: Installation & Warranty
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai admin sales, saya ingin jadwalkan instalasi, assign teknisi, dan auto-create warranty agar after-sales terpantau |
| **Acceptance Criteria** | 1. Schedule installation (date, technician, checklist) 2. Completion sign-off 3. Auto-create warranty on complete 4. Warranty period tracking 5. Warranty claim management 6. Repair/replacement tracking |

#### FR-E05: After-Sales Support
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai admin sales, saya ingin terima support ticket, jadwalkan maintenance, dan track resolution agar customer puas |
| **Acceptance Criteria** | 1. Support ticket CRUD 2. Priority (low/medium/high/urgent) 3. Maintenance schedule 4. Status tracking (open/in-progress/resolved/closed) 5. Customer follow-up log |

#### FR-E06: Documents
| Aspek | Detail |
|------|--------|
| **Priority** | C |
| **User Story** | Sebagai admin, saya ingin generate invoice, surat, kontrak, sertifikat dari template agar dokumen resmi terstandar |
| **Acceptance Criteria** | 1. Sales invoice (from SO) 2. Purchase invoice (from PO) 3. Surat/letter templates 4. Kontrak/contract templates 5. Sertifikat/warranty card 6. PDF generation 7. Document numbering 8. Digital archive |

#### FR-E07: Accounting
| Aspek | Detail |
|------|--------|
| **Priority** | D |
| **User Story** | Sebagai admin accounting, saya ingin chart of accounts, journal double-entry, multi-currency, dan laporan keuangan |
| **Acceptance Criteria** | 1. Chart of accounts CRUD 2. Journal entries (double-entry, debit/credit) 3. Multi-currency USD/IDR 4. Reports: Laba Rugi, Neraca, Arus Kas 5. Bank reconciliation 6. Auto-journal from PO/SO |

#### FR-E08: HRM
| Aspek | Detail |
|------|--------|
| **Priority** | E |
| **User Story** | Sebagai admin HR, saya ingin kelola employee, department agar data SDM terorganisir |
| **Acceptance Criteria** | 1. Employee CRUD (profile, department, position, salary) 2. Department CRUD 3. Attendance (future) 4. Leave management (future) |

#### FR-E09: Company Admin
| Aspek | Detail |
|------|--------|
| **Priority** | E |
| **User Story** | Sebagai admin company, saya ingin kelola kontak pihak ketiga, agreement, dan profil perusahaan |
| **Acceptance Criteria** | 1. Contacts (supplier, vendor, service, government) 2. Agreements (contract, MOU, renewal reminder) 3. Company profile (info, legal, compliance) |

#### FR-E10: License Manager (LeadGym)
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai admin lisensi, saya ingin issue license key untuk customer gym, track status, dan suspend/expire agar monetisasi LeadGym terkontrol |
| **Acceptance Criteria** | 1. Customer CRUD (gym owner, company info) 2. License issue (key, expiry, feature tier: basic/pro/enterprise) 3. Activation log 4. Suspend/expire/renew 5. Subscription plan (monthly/yearly) 6. API endpoint untuk LeadGym validate 7. Dashboard: active licenses, revenue, expiring soon |

#### FR-E11: DB Management
| Aspek | Detail |
|------|--------|
| **Priority** | C |
| **User Story** | Sebagai superadmin, saya ingin backup/restore database dan manage tabel dari panel agar tidak perlu SSH |
| **Acceptance Criteria** | 1. Manual backup (download .sql.gz) 2. Scheduled backup (cron) 3. Restore (upload .sql.gz, confirm) 4. Table browser 5. Optimize tables 6. Migration status 7. super_admin only, confirmation for destructive ops |

### 3.3 LeadGym — Software Gym (Multi-tenant)

#### FR-G01: Member Management
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai gym owner, saya ingin kelola profil member lengkap agar data member terorganisir |
| **Acceptance Criteria** | 1. Member CRUD (name, photo, contact, DOB, emergency contact, gender, address) 2. Membership card (QR/barcode) 3. Search & filter 4. Member history (subscriptions, check-in, invoices) 5. Import/export CSV 6. Tenant-scoped (hanya data gym sendiri) |

#### FR-G02: Plans
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai gym owner, saya ingin define tipe membership (monthly, quarterly, annual, PT) agar member bisa pilih paket |
| **Acceptance Criteria** | 1. Plan CRUD (name, duration, price, features, status) 2. Plan categories: membership, PT sessions, add-on 3. Pricing per plan 4. Active/archived status |

#### FR-G03: Subscriptions
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai gym owner, saya ingin kelola subscription member (active, renewal, freeze, transfer) agar tahu siapa yang aktif |
| **Acceptance Criteria** | 1. Create subscription (member + plan) 2. Status: active/expired/frozen/transferred 3. Auto-calculate end date 4. Renewal (extend) 5. Freeze (pause, extend end date) 6. Transfer (move to another member) 7. Expiry notification (7 days before) 8. History log |

#### FR-G04: Invoices
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai gym owner, saya ingin generate invoice untuk pembayaran membership/transaksi agar pencatatan keuangan jelas |
| **Acceptance Criteria** | 1. Invoice from subscription 2. Manual invoice (services, add-on) 3. Invoice numbering (INV-YYYY-XXXXX per tenant) 4. Status: unpaid/paid/partial/cancelled 5. PDF generation 6. Payment record 7. Invoice transaction log |

#### FR-G05: Services
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai gym owner, saya ingin define services (PT, kelas, locker) agar bisa ditagih ke member |
| **Acceptance Criteria** | 1. Service CRUD (name, type, price, duration) 2. Assign to member 3. Track usage (PT sessions remaining) |

#### FR-G06: Enquiries & Follow-ups
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai gym owner, saya ingin track enquiry walk-in/lead dan follow-up agar konversi tinggi |
| **Acceptance Criteria** | 1. Enquiry CRUD (name, contact, source, interest) 2. Status: new/contacted/converted/lost 3. Follow-up log (date, note, next action) 4. Convert to member |

#### FR-G07: Expenses
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai gym owner, saya ingin catat pengeluaran gym agar tahu profit |
| **Acceptance Criteria** | 1. Expense CRUD (date, category, amount, description) 2. Category management 3. Monthly summary 4. Filter by date |

#### FR-G08: Check-in
| Aspek | Detail |
|------|--------|
| **Priority** | B |
| **User Story** | Sebagai gym staff, saya ingin check-in member via barcode/QR agar tahu siapa yang hadir dan kapasitas gym |
| **Acceptance Criteria** | 1. Scan barcode/QR (camera or manual input) 2. Validate active subscription 3. Log check-in time 4. Capacity counter (current vs max) 5. Block if subscription expired 6. Check-out (optional) |

#### FR-G09: Dashboard
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai gym owner, saya ingin dashboard dengan stats (active members, revenue, retention, capacity) agar tahu performa gym |
| **Acceptance Criteria** | 1. Active members count 2. Revenue this month 3. New members this month 4. Expiring soon (7 days) 5. Check-in today 6. Capacity gauge 7. Retention rate 8. Charts (trend) |

#### FR-G10: Tenant Settings
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai gym owner, saya ingin setting branding gym (name, logo, color override) agar terasa milik saya |
| **Acceptance Criteria** | 1. Gym name, logo, address, contact 2. Color override (within design system range) 3. Operating hours 4. Capacity setting 5. License key input & status |

#### FR-G11: License Validation
| Aspek | Detail |
|------|--------|
| **Priority** | A (MVP) |
| **User Story** | Sebagai sistem, saya ingin validate license key ke LeaderP agar hanya customer berlisensi yang bisa pakai |
| **Acceptance Criteria** | 1. Input license key di settings 2. Validate via LeaderP API 3. Store validation result (valid/expired/suspended) 4. Daily sync (cron) 5. Read-only mode if expired 6. Banner "Lisensi berakhir" if expired |

## 4. Non-Functional Requirements

### 4.1 Performance
| Metric | Target |
|--------|--------|
| Homepage load | < 3 detik (3G) |
| Product listing | < 2 detik |
| Admin page load | < 2 detik |
| API response | < 500ms |
| Database query | < 100ms (indexed) |

### 4.2 Scalability
- Website: 1000 concurrent visitors
- LeaderP: 50 concurrent admin
- LeadGym: 100 tenants, 10.000 members total

### 4.3 Availability
- Uptime target: 99.5%
- Maintenance window: Sunday 02:00-04:00 WIB

### 4.4 Security
- HTTPS everywhere
- Rate limiting (inquiry, login, API)
- File upload validation
- Destructive ops confirmation
- Multi-tenant isolation (no cross-tenant data leak)

### 4.5 Compatibility
- Browser: Chrome 100+, Firefox 100+, Safari 15+, Edge 100+
- Mobile: iOS 15+, Android 10+
- PHP: 8.2+
- MySQL: 8.0+ / MariaDB 10.6+

## 5. Prioritas & MVP

| Priority | Modul | Status |
|----------|-------|--------|
| **A (MVP)** | Website: Homepage, Catalog, Detail, Inquiry | Phase 1-3 |
| **A (MVP)** | LeaderP: Inventory, PO, SO, License Manager | Phase 4 |
| **A (MVP)** | LeadGym: Members, Plans, Subscriptions, Invoices, Dashboard, License | Phase 4 |
| **B** | Website: Gallery, Blog, Multi-bahasa, Static pages | Phase 2-3 |
| **B** | LeaderP: Installation/Warranty, After-sales, License Manager | Phase 4-5 |
| **B** | LeadGym: Services, Enquiries, Expenses, Check-in | Phase 5 |
| **C** | LeaderP: Documents, DB Management | Phase 5 |
| **D** | LeaderP: Accounting | Phase 6 (future) |
| **E** | LeaderP: HRM, Company Admin | Phase 6 (future) |