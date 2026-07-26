# LeaderHub — Feature Expansion (Differentiators & Value-Add)

> **Version**: 1.0 | **Status**: Proposal | **Last Updated**: 2026-07-26
> **Tujuan**: Daftar fitur tambahan untuk membedakan LeaderHub dari kompetitor & meningkatkan value jual

## 1. Kategori Fitur Expansion

| Kategori | App | Status |
|----------|-----|--------|
| **SEO Boost + AI** | Website | Proposed |
| **Content Admin Power** | Website (Filament) | Proposed |
| **Job Portal** | Website | Proposed |
| **Gallery Modern** | Website | Proposed |
| **ERP Differentiators** | LeaderP | Proposed |
| **LeadGym Differentiators** | LeadGym | Proposed |
| **Cross-App Synergy** | All | Proposed |

---

## 2. SEO Boost + AI (Website)

### 2.1 AI-Powered SEO Automation
Fitur yang membuat LeaderHub unggal di Google tanpa admin harus paham SEO teknis.

#### F2.1.1 — AI Meta Generator
- **Apa**: Saat admin buat/edit produk/blog/page, AI auto-generate `seo_title`, `seo_description`, `og:image` alt, dan keyword suggestions.
- **Cara kerja**: Integrasi OpenAI/Anthropic API (atau lokal Ollama untuk hemat). Admin klik "Generate SEO" → AI analisis konten → suggest meta.
- **Value**: Setiap halaman auto-optimized, hemat waktu admin, ranking lebih baik.
- **Tech**: API call ke LLM, cache hasil, admin bisa edit sebelum save.

#### F2.1.2 — AI Content Suggestion (Blog)
- **Apa**: AI bantu drafting artikel — outline, intro, section expansion, conclusion.
- **Cara kerja**: Admin input topic + keyword target → AI generate draft → admin edit.
- **Value**: Output artikel lebih cepat, struktur SEO-friendly, konsisten.
- **Tech**: TipTap editor + AI plugin (stream response).

#### F2.1.3 — AI Image Alt Text
- **Apa**: Auto-generate alt text untuk semua gambar upload (product, blog, gallery).
- **Cara kerja**: Image upload → AI vision model (GPT-4o / Gemini Vision) → alt text.
- **Value**: Accessibility + SEO (Google baca alt text untuk image search).
- **Tech**: Vision API call saat upload, store di `alt` column.

#### F2.1.4 — AI Keyword Research
- **Apa**: AI suggest keyword target per halaman berdasarkan kompetitor & trend.
- **Cara kerja**: Admin input topic → AI scrape Google Trends + competitor → suggest keyword + difficulty score.
- **Value**: Konten targeting keyword yang benar-benar dicari.
- **Tech**: Google Trends API + LLM analysis.

### 2.2 Technical SEO (Built-in)
Bukan AI, tapi infrastruktur SEO yang matang.

#### F2.2.1 — Structured Data (JSON-LD) Otomatis
- Product schema (name, image, price, availability, brand, rating)
- Article schema (headline, author, datePublished, image)
- Organization schema (name, logo, contact, address)
- BreadcrumbList schema
- FAQPage schema (untuk blog FAQ section)
- JobPosting schema (untuk lowongan — lihat §4)
- **Value**: Rich snippet di Google, klik lebih tinggi.

#### F2.2.2 — Sitemap.xml Dynamic
- Auto-generate sitemap.xml untuk: products, blog, pages, gallery, jobs
- Terpisah per type (`sitemap-products.xml`, `sitemap-blog.xml`, dll)
- Auto-submit ke Google Search Console (API)
- Ping Google/Bing saat update
- **Value**: Indexing lebih cepat.

#### F2.2.3 — Robots.txt + Canonical
- Robots.txt configurable via admin
- Canonical URL per halaman (hindari duplicate content)
- hreflang untuk multi-bahasa (ID/EN)
- **Value**: Hindari penalty Google.

#### F2.2.4 — Open Graph + Twitter Card
- Auto-generate og:title, og:description, og:image per halaman
- Twitter Card (summary_large_image)
- Preview di FB/Twitter/LinkedIn/WhatsApp
- **Value**: Share di social media terlihat profesional.

#### F2.2.5 — Page Speed Optimization
- WebP conversion otomatis (Intervention Image)
- Lazy load (native `loading="lazy"` + IntersectionObserver)
- Responsive srcset (multiple sizes)
- Critical CSS inline (above-the-fold)
- Font display swap
- HTTP/2 push (jika Nginx/aaPanel)
- Brotli/Gzip compression
- **Value**: Core Web Vitals hijau, ranking boost.

#### F2.2.6 — Schema Markup Editor
- Admin bisa tambah custom JSON-LD per halaman (untuk skema niche)
- Preview di admin (Google Rich Results Test API)
- **Value**: Fleksibilitas untuk skema khusus industri fitness.

### 2.3 Geo SEO (Lokal)
Untuk bisnis distributor yang punya showroom/warehouse.

#### F2.3.1 — Google Business Profile Integration
- Sync data ke Google Business Profile (nama, alamat, jam buka, foto)
- Auto-update saat admin edit settings
- Review embed (tampilkan review Google di website)
- **Value**: Local SEO pack (map listing atas).

#### F2.3.2 — Location Pages
- Auto-generate halaman per kota: "Alat Fitness Jakarta", "Alat Fitness Surabaya"
- Template: hero + produk + testimonial + map + FAQ
- Dynamic content (produk terdekat, jam buka)
- **Value**: Tangkap long-tail keyword geo.

#### F2.3.3 — Geo Sitemap
- Sitemap khusus untuk location pages
- KML file untuk Google Earth/Maps
- **Value**: Local search visibility.

### 2.4 Content SEO Tools (untuk Admin)

#### F2.4.1 — SEO Score per Halaman
- Real-time SEO score di admin (seperti Yoast/RankMath)
- Check: keyword di title, meta length, heading structure, image alt, internal link, readability
- Suggestion perbaikan
- **Value**: Admin tahu apa yang kurang.

#### F2.4.2 — Internal Linking Suggestions
- AI suggest internal link antar artikel (berdasarkan topik relevan)
- Auto-insert link saat publish (opsional)
- **Value**: Page authority mengalir, struktur situs kuat.

#### F2.4.3 — Breadcrumb Dynamic
- Auto-generate breadcrumb per halaman (Home > Produk > Kategori > Produk)
- Tampil di UI + JSON-LD
- **Value**: UX + SEO.

#### F2.4.4 — 404 Monitor + Redirect Manager
- Log 404 errors (dari Google Search Console + internal)
- Admin bisa setup redirect 301/302
- Pattern redirect (regex)
- **Value**: Tidak kehilangan traffic dari broken link.

### 2.5 Analytics & Reporting

#### F2.5.1 — Google Search Console Integration
- Connect via API
- Tampilkan: impressions, clicks, CTR, average position per halaman
- Alert: drop ranking, new keyword opportunity
- **Value**: Data-driven SEO decisions.

#### F2.5.2 — Google Analytics 4 Integration
- Embed GA4 dashboard di admin (page views, top pages, traffic source)
- Event tracking (inquiry submit, product view, blog read)
- **Value**: Pahami user behavior.

#### F2.5.3 — SEO Dashboard
- Overview: ranking keywords, organic traffic, indexed pages, errors
- Trend chart (30/90/365 hari)
- Top performing pages
- Pages needing attention (drop ranking, missing meta)
- **Value**: Semua SEO metrics di satu tempat.

---

## 3. Content Admin Power (Website — Filament)

### 3.1 Advanced Product Management

#### F3.1.1 — Product Variants
- Varian per produk (warna, ukuran, spec berbeda)
- Harga per varian
- Stock per varian
- Image per varian
- **Value**: Produk kompleks (e.g. treadmill dengan beberapa model) tertangani.

#### F3.1.2 — Product Bundles
- Bundle beberapa produk jadi 1 paket (e.g. "Home Gym Setup" = treadmill + dumbbell + bench)
- Harga bundle (diskon vs beli satuan)
- Tampil di homepage & catalog
- **Value**: Upsell, AOV naik.

#### F3.1.3 — Product Comparison
- Pengunjung bisa compare 2-3 produk side-by-side
- Tabel perbandingan spec
- **Value**: Bantu keputusan pembelian.

#### F3.1.4 — Product Reviews & Ratings
- Admin input review (dari customer real, via follow-up)
- Rating 1-5 bintang
- Tampil di product detail (Rich Snippet)
- Filter: rating tertinggi, paling helpful
- **Value**: Social proof + SEO.

#### F3.1.5 — Product Video
- Upload video produk (demo, review, instalasi)
- Embed YouTube/Vimeo
- Tampil di gallery product detail
- **Value**: Engagement lebih tinggi.

#### F3.1.6 — Product Spec Sheet PDF
- Auto-generate spec sheet PDF per produk (dari data spec)
- Download button di product detail
- **Value**: Untuk customer yang butuh dokumen.

#### F3.1.7 — Product Availability Status
- Status: In Stock, Low Stock, Out of Stock, Pre-Order
- Auto-update dari stock level (sync dengan ERP)
- Badge di product card
- **Value**: Expectation management.

### 3.2 Advanced Blog/News/Events

#### F3.2.1 — Content Types
- **Article**: artikel biasa (tips, review, education)
- **News**: berita perusahaan, industri
- **Event**: event dengan tanggal, lokasi, registrasi
- **Press Release**: rilis resmi
- **Case Study**: studi kasus customer
- Tiap type punya template field berbeda
- **Value**: Konten terstruktur, bukan hanya "blog post".

#### F3.2.2 — Author System dengan SEO Boost
- Author profile (name, photo, bio, social media, expertise)
- Author page (`/author/{slug}`) dengan semua artikel
- JSON-LD Author (Person schema)
- Google E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness)
- Author reputation score (artikel count, views, engagement)
- **Value**: Google prioritaskan konten dengan author jelas → ranking boost.

#### F3.2.3 — Event Management
- Field: start_date, end_date, location, capacity, registration_link
- Countdown timer di frontend
- Auto-hide event yang sudah lewat
- Calendar view (month/week)
- Registrasi via form (simpan ke DB, notifikasi email)
- **Value**: Event marketing, lead generation.

#### F3.2.4 — Content Scheduling
- Schedule publish (datetime)
- Schedule unpublish (opsional)
- Draft → Review → Published workflow
- Multi-author dengan approval (editor approve sebelum publish)
- **Value**: Tim content management yang profesional.

#### F3.2.5 — Content Series
- Group artikel jadi series (e.g. "Panduan Lengkap Strength Training" — 5 artikel)
- Navigation antar artikel dalam series
- Series page (`/series/{slug}`)
- **Value**: Engagement, page views per session naik.

#### F3.2.6 — Featured Image Variants
- Auto-generate multiple size (thumbnail, medium, large, OG, Twitter)
- Admin pilih crop area
- WebP + fallback
- **Value**: Performance + visual konsisten.

#### F3.2.7 — Table of Contents (Auto)
- Auto-generate TOC dari heading (H2, H3)
- Sticky TOC di sidebar (desktop)
- Smooth scroll
- **Value**: UX + Google featured snippet.

#### F3.2.8 — Reading Progress + Estimated Time
- Progress bar di top (scroll)
- Estimated reading time (auto-calc)
- **Value**: Engagement signal.

#### F3.2.9 — Related Articles (AI)
- AI suggest related articles (berdasarkan semantic similarity)
- Bukan hanya same category
- **Value**: Page views per session naik.

#### F3.2.10 — Newsletter Signup
- Embed form newsletter di blog
- Integrasi Mailchimp / Brevo / self-hosted list
- Double opt-in
- **Value**: Email list building.

### 3.3 Media Library Modern

#### F3.3.1 — Centralized Media Library
- Semua upload (product, blog, gallery, banner) masuk media library pusat
- Folder/album organize
- Tag gambar
- Search by name, tag, type
- Bulk action (delete, move, tag)
- **Value**: Tidak duplikat upload, mudah cari.

#### F3.3.2 — Image Editor Built-in
- Crop, rotate, resize, filter
- Focal point selection (untuk responsive crop)
- **Value**: Tidak perlu Photoshop, langsung di admin.

#### F3.3.3 — Video Library
- Upload video (MP4) atau embed (YouTube, Vimeo)
- Thumbnail auto-generate
- Player dengan custom controls
- **Value**: Rich media content.

#### F3.3.4 — Document Library
- Upload PDF, DOCX, XLSX (spec sheet, brochure, manual)
- Versioning (v1, v2, v3)
- Download tracking
- **Value**: Sales material terorganisir.

### 3.4 Banner & Hero Manager

#### F3.4.1 — Dynamic Banner Builder
- Drag-and-drop banner builder (text, image, CTA, video bg)
- Multiple banner per page (homepage, category, blog)
- Schedule (start/end date)
- Targeting (by geo, device, language)
- A/B testing (2 variant, tampilkan random, track CTR)
- **Value**: Marketing experiment, conversion optimization.

#### F3.4.2 — Popup Manager
- Exit-intent popup
- Timer popup (after X seconds)
- Scroll popup (after X%)
- Content: newsletter, promo, inquiry nudge
- Frequency cap (per user, per session)
- **Value**: Lead capture.

### 3.5 Menu Builder

#### F3.5.1 — Dynamic Menu
- Admin atur menu (header, footer, mobile) tanpa coding
- Drag-and-drop reorder
- Multi-level (dropdown)
- Link ke: page, product, category, blog, custom URL
- **Value**: Fleksibilitas navigasi.

### 3.6 Form Builder

#### F3.6.1 — Custom Form Builder
- Admin buat form custom (contact, inquiry, registration, survey)
- Field: text, email, phone, select, radio, checkbox, file upload, date
- Submission → email + save DB
- Export CSV
- **Value**: Tidak perlu developer untuk form baru.

---

## 4. Job Portal (Website)

### 4.1 Job Vacancy Management

#### F4.1.1 — Job Posting
- Admin (HR) buat lowongan: title, department, location, type (full-time/part-time/contract), salary range, description, requirements, benefits
- Status: draft, published, closed, archived
- Publish/unpublish schedule
- Expiry date (auto-close)
- **Value**: Halaman karir profesional.

#### F4.1.2 — Job Categories & Tags
- Kategori: Sales, Technician, Admin, Trainer, Marketing
- Tags: senior, junior, remote, on-site
- Filter di frontend
- **Value**: Organisasi lowongan.

#### F4.1.3 — Job Detail Page
- Layout: title, department, location, type, salary, description, requirements, benefits, how-to-apply
- "Apply Now" button → form aplikasi
- Share button (LinkedIn, WhatsApp, email)
- Related jobs
- JSON-LD JobPosting schema (Google Jobs)
- **Value**: Muncul di Google Jobs search.

### 4.2 Job Application System

#### F4.2.1 — Online Application Form
- Field: nama, email, phone, posisi, cover letter, CV upload (PDF)
- Validasi: file size, format, required field
- Anti-spam (honeypot, rate limit)
- Submit → save ke `job_applications` table
- Email notifikasi ke HR
- **Value**: Lamaran langsung, tanpa email manual.

#### F4.2.2 — Application Tracking (Admin)
- List semua aplikasi di Filament
- Filter: posisi, status, date
- Status: new, reviewed, interview, rejected, hired
- View CV (preview PDF)
- Download CV bulk
- Add note per aplikasi
- Email template: interview invite, rejection, offer
- **Value**: ATS (Applicant Tracking System) sederhana.

#### F4.2.3 — Career Page
- `/careers` — list semua lowangan aktif
- Filter: department, location, type
- Search
- "No open positions" state dengan form "Submit CV for future"
- **Value**: Employer branding.

### 4.3 SEO for Jobs

#### F4.3.1 — Google Jobs Optimization
- JobPosting JSON-LD schema
- Auto-submit ke Google Jobs API (Indexing API)
- Sitemap-jobs.xml
- **Value**: Lowongan muncul di Google Jobs.

---

## 5. Gallery Modern (Website)

### 5.1 Advanced Gallery

#### F5.1.1 — Album Types
- **Product Gallery**: foto produk (auto-link ke product)
- **Installation Gallery**: foto instalasi (before/after)
- **Event Gallery**: foto event
- **Showroom Gallery**: foto showroom
- **Customer Gallery**: foto customer (UGC)
- **Video Gallery**: video demo, testimonial
- **Value**: Multi-purpose gallery.

#### F5.1.2 — Before/After Slider
- Interactive slider (drag) untuk before/after instalasi
- **Value**: Visual proof, engagement.

#### F5.1.3 — 360° Product View
- Upload 360° image sequence
- Viewer rotate (drag)
- **Value**: Pengunjung "pegang" produk secara digital.

#### F5.1.4 — Masonry + Grid Toggle
- User pilih view: masonry atau grid
- Preference persist (localStorage)
- **Value**: UX preference.

#### F5.1.5 — Lightbox Pro
- Zoom, pan, fullscreen
- Keyboard navigation
- Touch swipe
- Caption + EXIF info (camera, lens, date)
- Download button (opsional, watermark)
- Share button
- **Value**: Pengalaman gallery premium.

#### F5.1.6 — Gallery Filter Advanced
- Filter: album, tag, date range, type
- Sort: newest, oldest, popular (views)
- Search
- **Value**: Navigasi gallery besar.

#### F5.1.7 — Gallery Stats
- View count per image
- Popular images (most viewed)
- Download count
- **Value**: Insight konten visual.

### 5.2 Gallery Admin

#### F5.2.1 — Bulk Upload
- Drag-drop multiple image
- Auto-create album atau pilih existing
- Auto-generate alt (AI vision)
- Auto-resize + WebP
- Progress bar
- **Value**: Upload cepat.

#### F5.2.2 — Image Reorder (Drag-and-drop)
- Drag image untuk reorder dalam album
- **Value**: Kontrol urutan tampil.

#### F5.2.3 — Image Tagging
- Tag image (e.g. "treadmill", "instalasi", "jakarta")
- Multi-tag per image
- Search by tag
- **Value**: Organisasi.

---

## 6. ERP Differentiators (LeaderP)

### 6.1 Advanced Inventory

#### F6.1.1 — Barcode/QR Scanning
- Generate barcode/QR per produk
- Scan untuk: stock opname, receive goods, delivery
- Mobile-friendly (camera via browser)
- **Value**: Akurasi inventory, hemat waktu.

#### F6.1.2 — Stock Forecasting (AI)
- AI prediksi stock berdasarkan: historical sales, seasonality, lead time
- Alert: "Order lagi dalam 2 minggu"
- Suggest PO otomatis
- **Value**: Tidak overstock/understock.

#### F6.1.3 — Multi-Warehouse Transfer
- Transfer stock antar gudang
- Transfer order + approval
- In-transit tracking
- **Value**: Distribusi multi-lokasi.

#### F6.1.4 — Serial Number Tracking
- Track per unit (serial number)
- History: receive → stock → deliver → install → warranty → claim
- **Value**: Traceability untuk warranty.

#### F6.1.5 — Landed Cost Calculator
- Hitung landed cost: unit USD + shipping + duty + insurance + handling
- Allocasi ke per produk
- Update harga jual IDR otomatis (margin %)
- **Value**: Akurasi margin, tidak rugi.

### 6.2 Advanced Sales

#### F6.2.1 — CRM Lite
- Lead → Contact → Demo → Quote → Order → Close
- Activity timeline per customer
- Follow-up reminder (calendar)
- Pipeline view (kanban)
- **Value**: Sales team produktif.

#### F6.2.2 — Quotation Builder
- Buat quote PDF profesional (logo, items, terms, signature)
- Template customizable
- Track: sent, viewed, accepted, rejected
- Convert quote → SO
- **Value**: Sales process cepat.

#### F6.2.3 — Customer Portal (B2B)
- Customer login (gym owner yang sering beli)
- Lihat: history order, invoice, warranty, status delivery
- Submit repeat order
- **Value**: Retention, convenience.

### 6.3 Advanced Accounting

#### F6.3.1 — Multi-Currency Real-time
- Exchange rate auto-fetch (API Bank Indonesia / exchangerate.host)
- Historical rate (untuk transaksi lampau)
- Gain/loss currency report
- **Value**: Akurasi laporan multi-currency.

#### F6.3.2 — Tax Management (PPN)
- PPN in/out tracking
- Tax invoice generation
- SPT period summary
- **Value**: Compliance pajak Indonesia.

#### F6.3.3 — Financial Dashboard
- Cash flow chart
- Revenue vs expense
- AR aging (piutang)
- AP aging (utang)
- Profit margin per produk
- **Value**: Insight finansial real-time.

### 6.4 Advanced After-Sales

#### F6.4.1 — Maintenance Contract
- Kontrak maintenance tahunan (service rutin)
- Schedule otomatis (reminder)
- Track: visit, issue, resolution
- **Value**: Recurring revenue.

#### F6.4.2 — Customer Satisfaction Survey
- Kirim survey setelah instalasi/service
- Rating + feedback
- Dashboard: CSAT score, trend
- **Value**: Quality improvement.

### 6.5 Reporting & Analytics

#### F6.5.1 — Custom Report Builder
- Admin buat report custom (pilih table, field, filter, group, chart)
- Export: PDF, Excel, CSV
- Schedule (weekly/monthly email)
- **Value**: Reporting fleksibel.

#### F6.5.2 — Business Intelligence Dashboard
- KPI: revenue, profit, inventory turnover, customer acquisition, retention
- Trend (30/90/365 hari)
- Forecast (AI)
- **Value**: Data-driven decision.

---

## 7. LeadGym Differentiators (Gym Software)

### 7.1 Member Experience

#### F7.1.1 — Member Mobile Web App
- PWA (Progressive Web App) — install di HP tanpa app store
- Member login: lihat subscription, invoice, check-in history, class schedule
- Self-service: renew, freeze, update profile
- Push notification (class reminder, expiry)
- **Value**: Member engagement, retention.

#### F7.1.2 — Member Referral Program
- Member refer teman → dapat reward (free month, PT session, merchandise)
- Referral code per member
- Track: referral count, conversion
- **Value**: Organic growth.

#### F7.1.3 — Loyalty/Reward Points
- Point per check-in, per renewal, per referral
- Redeem: free month, PT session, merchandise
- **Value**: Gamification, retention.

### 7.2 Class & Booking

#### F7.2.1 — Class Schedule & Booking
- Class: yoga, zumba, spinning, HIIT
- Schedule (weekly recurring)
- Capacity limit
- Member book via web app
- Waitlist
- **Value**: Value-add service.

#### F7.2.2 — Personal Trainer Booking
- Member book PT session (pilih trainer, time)
- Trainer calendar
- Reminder (member + trainer)
- **Value**: PT revenue stream.

### 7.3 Advanced Analytics

#### F7.3.1 — Gym Analytics Dashboard
- Peak hours heatmap
- Member demographics (age, gender)
- Churn prediction (AI)
- Revenue per plan
- Capacity utilization
- **Value**: Optimasi operasional gym.

#### F7.3.2 — Member Journey Tracking
- From enquiry → trial → join → renew → churn
- Funnel analytics
- **Value**: Optimasi konversi.

### 7.4 Communication

#### F7.4.1 — WhatsApp Integration
- Auto-reminder via WA: expiry, class, payment
- Template message (approved WA Business API)
- **Value**: Engagement channel yang dipakai sehari-hari.

#### F7.4.2 — Email Campaign
- Newsletter, promo, event
- Template builder
- Segment (active, expired, prospect)
- **Value**: Re-engagement.

### 7.5 White-Label (Enterprise Tier)

#### F7.5.1 — Custom Branding
- Logo, color, font per tenant
- Custom domain
- Email dari domain tenant
- **Value**: Enterprise customer, premium pricing.

---

## 8. Cross-App Synergy

### 8.1 Unified Notification System
- Notifikasi cross-app: inquiry baru (website → LeaderP), license expiring (LeaderP → LeadGym), member churn alert (LeadGym → LeaderP)
- Channel: email, WA, dashboard
- **Value**: Tidak ada yang miss.

### 8.2 Customer 360
- Satu customer bisa: inquiry (website) → SO (LeaderP) → install → warranty → LeadGym license → gym member
- Timeline lengkap di LeaderP
- **Value**: Pahami customer end-to-end.

### 8.3 Marketing Automation
- Trigger: inquiry belum di-follow 3 hari → auto-reminder sales
- Trigger: customer beli treadmill → suggest LeadGym license
- Trigger: LeadGym member churn → email win-back
- **Value**: Automated revenue.

### 8.4 API Platform (Enterprise)
- Public API untuk integrasi pihak ketiga
- Documentation (Scramble)
- API key per customer
- **Value**: Ekosistem, lock-in customer.

---

## 9. Prioritas Implementasi Expansion

### Tier 1 — High Impact, Quick Win (Phase 5-6, masuk MVP+)
| Fitur | Effort | Impact |
|-------|--------|--------|
| AI Meta Generator | Medium | High (SEO) |
| Structured Data JSON-LD | Low | High (SEO) |
| Sitemap dynamic | Low | High (SEO) |
| Open Graph + Twitter Card | Low | Medium |
| Page Speed Optimization | Medium | High (Core Web Vitals) |
| Author System + E-E-A-T | Medium | High (SEO) |
| Content Types (Article/News/Event) | Medium | High |
| Job Portal (basic) | Medium | High (differentiator) |
| Gallery bulk upload + AI alt | Medium | Medium |
| SEO Score per halaman | Medium | High |
| Media Library centralized | Medium | High |
| Menu Builder | Low | Medium |
| 404 Monitor + Redirect | Low | Medium |

### Tier 2 — Medium Impact (Post-MVP, Phase 7+)
| Fitur | Effort | Impact |
|-------|--------|--------|
| AI Content Suggestion | Medium | Medium |
| AI Image Alt Text | Low | Medium |
| AI Keyword Research | Medium | Medium |
| Google Business Profile Integration | Medium | High (Local SEO) |
| Location Pages | Medium | High (Local SEO) |
| Product Variants & Bundles | Medium | Medium |
| Product Reviews & Ratings | Medium | High |
| Event Management | Medium | Medium |
| Content Series | Low | Medium |
| Newsletter Signup | Low | Medium |
| Banner A/B Testing | Medium | Medium |
| Popup Manager | Low | Medium |
| Form Builder | Medium | High |
| Job Application Tracking | Medium | High |
| Before/After Slider | Low | Medium |
| Barcode Scanning (ERP) | Medium | High |
| CRM Lite (ERP) | High | High |
| Quotation Builder (ERP) | Medium | High |
| WhatsApp Integration (Gym) | Medium | High |
| Member Web App PWA (Gym) | High | High |

### Tier 3 — Advanced (Future, Phase 8+)
| Fitur | Effort | Impact |
|-------|--------|--------|
| AI Stock Forecasting | High | High |
| Customer Portal B2B | High | Medium |
| Multi-Currency Real-time | Medium | Medium |
| Tax Management PPN | High | Medium |
| Custom Report Builder | High | High |
| Business Intelligence Dashboard | High | High |
| Class & Booking (Gym) | High | High |
| Member Referral + Loyalty | Medium | Medium |
| Gym Analytics Advanced | High | High |
| White-Label (Gym) | High | High |
| API Platform | High | High |
| Marketing Automation | High | High |
| 360° Product View | Medium | Low |
| Custom JSON-LD Editor | Low | Low |

---

## 10. Dampak ke Dokumen Lain

### 10.1 Database Schema Tambahan (Tier 1)
Tabel baru yang perlu ditambahkan ke `03-database-schema.md`:

```sql
-- Job Portal
CREATE TABLE job_vacancies (
  id, slug, title, department, location, type (full-time/part-time/contract),
  salary_min, salary_max, description, requirements, benefits,
  status (draft/published/closed), published_at, expiry_date,
  seo_title, seo_description, created_by, timestamps
);

CREATE TABLE job_applications (
  id, job_vacancy_id (FK), name, email, phone, cover_letter,
  cv_path, status (new/reviewed/interview/rejected/hired),
  admin_notes, timestamps
);

-- Authors (extend users)
ALTER TABLE users ADD COLUMN: slug, bio, photo, social_links (JSON), expertise;

-- Events (extend blog_posts atau tabel terpisah)
CREATE TABLE events (
  id, title, slug, description, start_date, end_date, location,
  capacity, registration_link, image, status, timestamps
);

-- Event registrations
CREATE TABLE event_registrations (
  id, event_id (FK), name, email, phone, notes, timestamps
);

-- Media Library
CREATE TABLE media (
  id, disk, path, name, alt, type (image/video/document),
  size, mime, width, height, tags (JSON), folder_id (FK self),
  uploaded_by (FK users), timestamps
);

CREATE TABLE media_folders (
  id, parent_id (FK self), name, timestamps
);

-- SEO redirects
CREATE TABLE redirects (
  id, from_url, to_url, type (301/302), is_active, timestamps
);

-- SEO 404 logs
CREATE TABLE seo_errors (
  id, url, referer, status (404), count, last_seen_at, timestamps
);

-- Menus
CREATE TABLE menus (
  id, location (header/footer/mobile), name, timestamps
);

CREATE TABLE menu_items (
  id, menu_id (FK), parent_id (FK self), label, url,
  type (page/product/category/blog/custom), target_id,
  sort_order, is_active, timestamps
);

-- Custom forms
CREATE TABLE forms (
  id, name, slug, description, fields (JSON), success_message,
  email_to, is_active, timestamps
);

CREATE TABLE form_submissions (
  id, form_id (FK), data (JSON), ip_address, timestamps
);

-- Newsletter
CREATE TABLE newsletter_subscribers (
  id, email, name, status (pending/subscribed/unsubscribed),
  subscribed_at, timestamps
);
```

### 10.2 Update Implementation Plan
Fitur Tier 1 ditambahkan ke Phase 5 (Integration & Polish) sebagai task tambahan:
- P5-T09: AI Meta Generator + SEO Score
- P5-T10: Structured Data + Sitemap dynamic
- P5-T11: Author System + Content Types
- P5-T12: Job Portal (vacancy + application)
- P5-T13: Media Library centralized
- P5-T14: Menu Builder + Form Builder
- P5-T15: Gallery bulk upload + AI alt
- P5-T16: 404 Monitor + Redirect Manager

### 10.3 Update PRD
Tambahkan user stories untuk fitur Tier 1 di `01-product-requirements.md`.

---

## 11. Tech Stack Tambahan untuk Expansion

| Fitur | Library/Service |
|-------|----------------|
| AI (meta, content, alt) | OpenAI API / Anthropic API / Ollama (lokal) |
| AI Vision (alt text) | GPT-4o / Gemini Vision |
| Google Search Console | Google Search Console API |
| Google Analytics 4 | GA4 Measurement Protocol / embed |
| Google Business Profile | Google Business Profile API |
| Sitemap | spatie/laravel-sitemap |
| SEO redirect | spatie/laravel-redirect-trailing-slash + custom |
| Newsletter | Mailchimp / Brevo / listmonk (self-hosted) |
| PWA | laravel/vite-plugin-pwa |
| Barcode | milon/barcode + html5-qrcode (scanner) |
| QR | simplesoftwareio/simple-qrcode |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| Image | intervention/image |
| Form builder | Filament custom + spatie/laravel-form-builder |
| WhatsApp | WhatsApp Business API / fonnte.com (Indonesia) |
| Calendar | fullcalendar (Filament widget) |
| Chart | flowframe/laravel-trend + Filament chart widget |

---

## 12. Estimasi Effort Tambahan (Tier 1)

| Fitur | Estimasi |
|-------|----------|
| AI Meta Generator | 3 hari |
| Structured Data JSON-LD | 1 hari |
| Sitemap dynamic | 1 hari |
| Open Graph + Twitter Card | 0.5 hari |
| Page Speed Optimization | 2 hari |
| Author System + E-E-A-T | 2 hari |
| Content Types (Article/News/Event) | 3 hari |
| Job Portal (basic) | 4 hari |
| Gallery bulk upload + AI alt | 2 hari |
| SEO Score per halaman | 3 hari |
| Media Library centralized | 3 hari |
| Menu Builder | 1 hari |
| 404 Monitor + Redirect | 2 hari |
| Form Builder | 3 hari |
| Newsletter Signup | 1 hari |
| **Total Tier 1** | **~31 hari (6 minggu)** |

**Catatan**: Tier 1 bisa dikerjakan paralel dengan Phase 5 (Integration & Polish) atau sebagai Phase 7 setelah MVP live. Rekomendasi: sebagian masuk Phase 5 (SEO + content), sebagian Phase 7 (job portal, form builder, media library advanced).

---

## 13. Kesimpulan

**Semua fitur yang Anda sebut POSSIBLE** dan akan membuat LeaderHub jauh berbeda dari kompetitor:

1. **SEO Boost + AI** → website muncul di Google tanpa admin paham SEO teknis
2. **Content Admin Power** → admin kelola produk, artikel, event, banner, menu tanpa coding
3. **Job Portal** → halaman karir + lamaran online (differentiator untuk distributor)
4. **Gallery Modern** → before/after, 360°, bulk upload, AI alt
5. **ERP Differentiators** → barcode, AI forecasting, CRM, quotation builder
6. **LeadGym Differentiators** → PWA, class booking, loyalty, white-label
7. **Cross-App Synergy** → customer 360, marketing automation

**Saran implementasi**:
- **MVP (Phase 1-6)**: fokus core fitur (website + ERP + gym basic)
- **MVP+ (Phase 5-7)**: masukkan Tier 1 SEO + content + job portal
- **V2 (Phase 8+)**: Tier 2 & 3 (advanced AI, CRM, PWA, white-label)

Dengan expansion ini, LeaderHub bukan hanya "website + ERP + gym software" tapi **platform bisnis lengkap** yang bisa dijual dengan premium pricing.