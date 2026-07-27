## Why

Website sudah punya homepage, katalog produk, dan gallery (Phase 2), tapi belum punya dua hal kritis: (1) blog untuk content marketing & SEO, dan (2) sistem inquiry untuk lead generation. Tanpa blog, website sulit menarik traffic organik dari Google karena tidak ada konten artikel yang bisa diindex. Tanpa inquiry system, pengunjung tidak cara mengumpulkan minat produk dan mengirim ke admin — yang adalah model bisnis utama LeaderHub (lead generation, bukan e-commerce). Phase 3 membangun keduanya secara lengkap dan terintegrasi.

## What Changes

### Blog System
- Migrations di core: `blog_categories`, `blog_tags`, `blog_posts`, `blog_post_tags` (composite PK)
- Models di core: BlogCategory, BlogTag, BlogPost (global scope `published` + `published_at <= now`), dengan relations: category, author (User), tags (belongsToMany)
- Filament resources: BlogPostResource (dengan RichEditor, tags multi-select, SEO fields, author select, status, published_at), BlogCategoryResource, BlogTagResource
- Blog frontend: listing page (`/blog`) dengan editorial grid 3 kolom, filter category & tag via query param, search, pagination 9/page
- Blog detail (`/blog/{slug}`): featured image, category badge, title, meta (author, date, reading time), rich content, tags, related posts (same category, exclude self, limit 3), JSON-LD Article schema

### Inquiry System
- Migrations di core: `inquiries` (dengan `inquiry_number` unique, `uuid` unique, `status`, `ip_address`, `user_agent`), `inquiry_items` (FK inquiry + product)
- Models di core: Inquiry (hasMany items), InquiryItem (belongsTo inquiry + product)
- InquiryService di core: `create(array $data, array $items)` dengan DB transaction, `generateNumber()` format `INQ-YYYY-XXXXX` (atomic increment), `buildWaMessage(Inquiry)` untuk pre-filled WhatsApp message
- Inquiry cart (frontend): `resources/js/inquiry.js` — localStorage key `leaderhub_inquiry`, functions: `getCart()`, `addItem(productId, name, slug, qty)`, `removeItem(productId)`, `updateQty(productId, qty)`, `clearCart()`, `getCount()`, `updateBar()` (show/hide floating bar)
- Floating inquiry bar: fixed bottom, hidden when cart empty, shows count + "Lihat Daftar" button
- "Daftar Minat Saya" page (`/inquiry`): list items dari localStorage, edit qty, hapus, form (nama, WA, email, pesan), submit via WA atau email
- Inquiry submit: `POST /inquiry` → validasi, honeypot anti-spam, rate limit 5/IP/jam, generate inquiry number, save ke DB + items, email ke admin, redirect ke wa.me (WA) atau success page (email)
- Filament InquiryResource (di website admin): list inquiries, view detail, update status, WA action button, admin notes

### Demo Content
- Seeder: 5 blog post (published, dengan tags), 1 inquiry (dengan items)

## Capabilities

### New Capabilities
- `blog-system`: Blog frontend (listing + detail) dengan filter, search, related posts, JSON-LD Article, dan admin CRUD via Filament
- `inquiry-cart`: Inquiry cart berbasis localStorage dengan floating bar, add/remove/edit qty, persist across page reloads
- `inquiry-submission`: Form submit inquiry dengan validasi, honeypot, rate limit, auto-numbering, notifikasi WA + email, save ke database
- `inquiry-admin`: Filament resource untuk admin melihat, merespons, dan mengubah status inquiry

### Modified Capabilities
<!-- None — semua baru, tidak ada spec existing yang berubah -->

## Impact

### Code Affected
- **core/database/migrations/**: 6 file migration baru (blog_categories, blog_tags, blog_posts, blog_post_tags, inquiries, inquiry_items)
- **core/src/Models/**: 4 model baru (BlogCategory, BlogTag, BlogPost, Inquiry, InquiryItem)
- **core/src/Services/**: 1 service baru (InquiryService)
- **apps/website/app/Http/Controllers/**: 2 controller baru (BlogController, InquiryController)
- **apps/website/app/Filament/Resources/**: 4 resource baru (BlogPostResource, BlogCategoryResource, BlogTagResource, InquiryResource)
- **apps/website/app/Mail/**: 1 mailable baru (InquiryReceived)
- **apps/website/resources/views/**: 4 view baru (blog/index, blog/show, inquiry/index, inquiry/success, emails/inquiry-received)
- **apps/website/resources/js/**: 1 file JS baru (inquiry.js)
- **apps/website/routes/web.php**: 4 route baru

### Database
- 6 tabel baru: `blog_categories`, `blog_tags`, `blog_posts`, `blog_post_tags`, `inquiries`, `inquiry_items`
- FK: `blog_posts.blog_category_id` → `blog_categories.id` (cascade), `blog_posts.author_id` → `users.id` (cascade), `blog_post_tags` composite PK, `inquiry_items.inquiry_id` → `inquiries.id` (cascade), `inquiry_items.product_id` → `products.id` (cascade)

### Dependencies
- Tidak ada package baru (RichEditor bawaan Filament, Mail bawaan Laravel)

### Breaking Changes
- Tidak ada — semua tambahan baru

## Non-goals

- **AI SEO meta generator** (Phase 5 — feature expansion Tier 1)
- **AI content suggestion** (Phase 5 — feature expansion)
- **Content types (News, Event, Press Release)** (Phase 5 — feature expansion)
- **Author system dengan E-E-A-T profile** (Phase 5 — feature expansion)
- **Newsletter signup** (Phase 5 — feature expansion)
- **Inquiry → Sales Order conversion** (Phase 4 — ERP Sales module)
- **Multi-bahasa untuk blog** (field `title_en`/`content_en` sudah ada di migration, tapi toggle EN untuk blog baru aktif di Phase 5)

## Reference

- **Step-by-step detail**: `docs/tasks/phase-3-blog-inquiry.md`
- **Feature spec inquiry**: `docs/07-features/03-inquiry-system.md`
- **Feature spec blog**: `docs/07-features/05-blog.md`
- **Database schema**: `docs/03-database-schema.md` §3.28-3.31 (blog), §3.16-3.17 (inquiry)
- **Design system**: `docs/04-design-system.md` §3.5 (editorial blog card), §3.3 (inquiry bar)