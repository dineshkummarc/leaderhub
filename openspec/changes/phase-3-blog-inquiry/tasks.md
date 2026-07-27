# Tasks — Phase 3: Blog & Inquiry System

> **Detail step-by-step**: `docs/tasks/phase-3-blog-inquiry.md`
> **Estimasi**: 2 minggu | **Prasyarat**: Phase 2 selesai (homepage, catalog, gallery, products table exists)

## Prerequisites Verification

Sebelum mulai Phase 3, pastikan ini sudah ada dari Phase 2:
- [x] `products` table exists (inquiry_items FK ke products)
- [x] `users` table exists (blog_posts.author_id FK ke users)
- [x] `partials/product-card.blade.php` exists (perlu data-product-id attribute)
- [x] `products/show.blade.php` exists (perlu add to inquiry button)
- [x] Filament admin panel `/admin` accessible
- [x] Settings `whatsapp` dan `email` (group `contact`) sudah di-seed (Phase 1)

## Tasks

### T01: Migrations — blog & inquiry tables

**Goal**: Buat 6 migration files di `core/database/migrations/` untuk blog dan inquiry tables.

**Steps**:
1. Buat migration `0002_01_03_000001_create_blog_table.php`:
   - Create `blog_categories` (id, slug unique, name, description nullable, timestamps)
   - Create `blog_tags` (id, slug unique, name, timestamps)
   - Create `blog_posts` (id, blog_category_id FK cascade, author_id FK ke users cascade, slug unique, title, title_en nullable, excerpt nullable, content LONGTEXT, content_en nullable, featured_image nullable, status default 'draft', published_at nullable, reading_time nullable, seo_title nullable, seo_description nullable, timestamps)
   - Index: `INDEX(status, published_at)`, `INDEX(blog_category_id)`
   - Create `blog_post_tags` (blog_post_id FK cascade, blog_tag_id FK cascade, composite PK)
2. Buat migration `0002_01_03_000002_create_inquiries_table.php`:
   - Create `inquiries` (id, inquiry_number unique, uuid unique, customer_name, customer_phone, customer_email nullable, message nullable, status default 'new', admin_notes nullable, converted_sales_order_id nullable BIGINT — **NO FK**, ip_address nullable, user_agent nullable, timestamps)
   - Index: `INDEX(status, created_at)`, `INDEX(inquiry_number)`
   - Create `inquiry_items` (id, inquiry_id FK cascade, product_id FK cascade, quantity default 1, notes nullable, timestamps)
   - Index: `INDEX(inquiry_id)`
3. Jalankan: `cd apps/website && php artisan migrate --path=../../core/database/migrations --force`

**Validation**:
- [x] `php artisan migrate` berhasil tanpa error
- [x] 6 tabel ada di database
- [x] FK `blog_posts.blog_category_id` → `blog_categories.id` (cascade) ada
- [x] FK `blog_posts.author_id` → `users.id` (cascade) ada
- [x] FK `blog_post_tags.blog_post_id` → `blog_posts.id` (cascade) ada
- [x] FK `blog_post_tags.blog_tag_id` → `blog_tags.id` (cascade) ada
- [x] FK `inquiry_items.inquiry_id` → `inquiries.id` (cascade) ada
- [x] FK `inquiry_items.product_id` → `products.id` (cascade) ada
- [x] `inquiries.converted_sales_order_id` TIDAK punya FK constraint (belum ada sales_orders table)
- [x] Composite PK `blog_post_tags(blog_post_id, blog_tag_id)` ada

**Effort**: 3h

---

### T02: Models — BlogCategory, BlogTag, BlogPost, Inquiry, InquiryItem

**Goal**: Buat 5 model classes di `core/src/Models/`.

**Steps**:
1. Buat `BlogCategory.php`:
   - `protected $guarded = []`
   - `posts()` relation: `hasMany(BlogPost::class)`
2. Buat `BlogTag.php`:
   - `protected $guarded = []`
   - `posts()` relation: `belongsToMany(BlogPost::class, 'blog_post_tags')`
3. Buat `BlogPost.php`:
   - `protected $guarded = []`
   - `protected $casts = ['published_at' => 'datetime']`
   - Global scope `published`: `static::addGlobalScope('published', fn(Builder $q) => $q->where('status', 'published')->where('published_at', '<=', now()))`
   - `category()` relation: `belongsTo(BlogCategory::class, 'blog_category_id')`
   - `author()` relation: `belongsTo(User::class, 'author_id')`
   - `tags()` relation: `belongsToMany(BlogTag::class, 'blog_post_tags')`
4. Buat `Inquiry.php`:
   - `protected $guarded = []`
   - `protected $casts = ['uuid' => 'string']`
   - `items()` relation: `hasMany(InquiryItem::class)`
5. Buat `InquiryItem.php`:
   - `protected $guarded = []`
   - `inquiry()` relation: `belongsTo(Inquiry::class)`
   - `product()` relation: `belongsTo(Product::class)`

**Validation**:
- [x] `php artisan tinker` → `Leaderhub\Core\Models\BlogPost::all()` berfungsi (return collection, mungkin kosong)
- [x] `Leaderhub\Core\Models\Inquiry::all()` berfungsi
- [x] Global scope `published` aktif: `BlogPost::all()` hanya return published posts
- [x] `BlogPost::withoutGlobalScope('published')->get()` return semua posts (termasuk draft)

**Effort**: 2h

---

### T03: Filament resources — BlogPost, BlogCategory, BlogTag, Inquiry

**Goal**: Buat 4 Filament resources di `apps/website/app/Filament/Resources/`.

**Steps**:
1. `php artisan make:filament-resource BlogPost`
2. `php artisan make:filament-resource BlogCategory`
3. `php artisan make:filament-resource BlogTag`
4. `php artisan make:filament-resource Inquiry --view` (dengan view page)

5. Edit `BlogPostResource.php`:
   - Navigation group: "Website"
   - Form schema: title (live, auto-slug), slug (unique), category select (relationship `category`, `createOptionForm` inline), author select (relationship `author`, default `auth()->id()`), tags multi-select (relationship `tags`, `createOptionForm` inline), excerpt textarea, content RichEditor (columnSpanFull), featured_image FileUpload (image, directory `blog`), status select (draft/published/archived), published_at DateTimePicker, SEO section (collapsible: seo_title, seo_description)
   - Table columns: title (searchable), category name, author name, status badge (colors: draft=gray, published=success, archived=warning), published_at (dateTime)
   - Filter: status select, category select

6. Edit `BlogCategoryResource.php`:
   - Form: name (live, auto-slug), slug (unique)
   - Table: name (searchable), slug

7. Edit `BlogTagResource.php`:
   - Form: name (live, auto-slug), slug (unique)
   - Table: name (searchable), slug

8. Edit `InquiryResource.php`:
   - Navigation group: "Website", icon: `heroicon-o-inbox`
   - Form: inquiry_number (disabled), customer_name (disabled), customer_phone (disabled), customer_email (disabled), message (disabled textarea), status (select: new/contacted/converted/closed/lost), admin_notes (textarea)
   - Table columns: inquiry_number (searchable), customer_name (searchable), customer_phone, status badge (colors: new=info, contacted=warning, converted=success, closed=gray, lost=danger), created_at (dateTime)
   - Filter: status select
   - Actions: ViewAction, EditAction, WA action (`Tables\Actions\Action::make('wa')->url(fn($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->customer_phone))->openUrlInNewTab()`)
   - `getPages()`: index (list), view, edit — **NO create page**

**Validation**:
- [x] `/admin/blog-posts` list tampil
- [x] Create blog post dengan RichEditor, tags, category berhasil
- [x] Published post tampil di frontend `/blog`
- [x] Draft post TIDAK tampil di frontend
- [x] `/admin/inquiries` list tampil (kosong dulu, akan ada data setelah T05)
- [x] Inquiry form: customer fields disabled, status & admin_notes editable
- [x] WA action button ada di table, link benar (wa.me/{phone digits})

**Effort**: 6h

---

### T04: Blog frontend — listing & detail

**Goal**: Buat BlogController, blog listing view, blog detail view, routes.

**Steps**:
1. `php artisan make:controller BlogController`
2. Edit `BlogController.php`:
   - `index(Request $request)`: load published posts with relations (category, author, tags), filter by category/tag/search, paginate 9 with `withQueryString()`
   - `show(string $slug)`: load post via `withoutGlobalScope('published')->where('slug', $slug)->firstOrFail()`, load related (same category, exclude self, limit 3)
3. Buat `resources/views/blog/index.blade.php`:
   - `@extends('layouts.app')`
   - `@section('title', 'Blog — LeaderHub')`
   - H1 "Blog"
   - Grid 3 kolom (md:grid-cols-3), 1 kolom mobile
   - Card: aspect 16:10 image, category label, title (font-semibold, line-clamp-2), meta (author · date · reading time)
   - Pagination: `{{ $posts->links() }}`
   - Empty state: "Tidak ada artikel yang cocok."
4. Buat `resources/views/blog/show.blade.php`:
   - `@extends('layouts.app')`
   - `@section('title', $post->title . ' — LeaderHub Blog')`
   - Breadcrumb (Home / Blog / Category)
   - Featured image (full-width, rounded-lg)
   - Category badge, H1 title, meta (author · date · reading time)
   - Content: `{!! $post->content !!}` dalam `<div class="prose">`
   - Tags: badges linking to `/blog?tag={slug}`
   - Related posts section (3 cards)
   - JSON-LD Article: `@type: Article`, `headline`, `author` (Person), `datePublished` (ISO 8601), `image`, `publisher` (Organization)
5. Tambah routes di `routes/web.php` — **SEBELUM** route `/{slug}`:
   ```php
   Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
   Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
   // Route::get('/{slug}', [PageController::class, 'show'])->name('page.show'); // PALING BAWAH
   ```

**Validation**:
- [x] `/blog` load, 9 post per page
- [x] Filter `?category={slug}` berfungsi
- [x] Filter `?tag={slug}` berfungsi
- [x] Search `?q={query}` berfungsi
- [x] Pagination preserve query string
- [x] `/blog/{slug}` detail load dengan content, tags, related
- [x] `/blog/nonexistent` → 404
- [x] JSON-LD Article ada di view source
- [x] Route `/blog/post-1` TIDAK ditangkap oleh `/{slug}` (test: blog detail load, bukan page 404)

**Effort**: 4h

---

### T05: InquiryService (core)

**Goal**: Buat InquiryService di `core/src/Services/` dengan create, generateNumber, buildWaMessage.

**Steps**:
1. Buat `core/src/Services/InquiryService.php`
2. Implement `create(array $data, array $items): Inquiry`:
   - Wrap in `DB::transaction()`
   - Create Inquiry with `generateNumber()`, `Str::uuid()`, customer fields, ip, user_agent, status='new'
   - Loop items, create InquiryItem per item
   - Return Inquiry
3. Implement `generateNumber(): string`:
   - `$year = now()->year`
   - `$last = Inquiry::whereYear('created_at', $year)->max('id') ?? 0`
   - `return sprintf('INQ-%d-%05d', $year, $last + 1)`
4. Implement `buildWaMessage(Inquiry $inquiry): string`:
   - Build multi-line string: greeting, inquiry number, items list, customer name, message
   - Join with `\n`

**Validation**:
- [x] `php artisan tinker` → `Leaderhub\Core\Services\InquiryService::generateNumber()` return `INQ-2026-00001` (atau next sequential)
- [x] Test create: `InquiryService::create(['customer_name'=>'Test','customer_phone'=>'08123','customer_email'=>null,'message'=>'test'], [['product_id'=>1,'qty'=>1]])` → inquiry tersimpan dengan items
- [x] `buildWaMessage()` return string dengan format yang benar

**Effort**: 2h

---

### T06: Inquiry cart logic (localStorage JS)

**Goal**: Buat `resources/js/inquiry.js` dengan cart logic dan floating bar update.

**Steps**:
1. Buat `resources/js/inquiry.js` dengan functions:
   - `getCart()`, `saveCart(cart)`, `addItem(productId, name, slug, qty=1)`, `removeItem(productId)`, `updateQty(productId, qty)`, `clearCart()`, `getCount()`, `updateBar()`
   - `window.addToInquiry = function(productId) { ... }` — global function
   - `document.addEventListener('DOMContentLoaded', updateBar)` — update bar on load
   - Max 50 items limit in `addItem`
2. Import di `resources/js/app.js`: tambah `import './inquiry';`
3. Update `resources/views/partials/product-card.blade.php`:
   - Tambah `data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}"` di root `<a>` element
4. Update `resources/views/products/show.blade.php`:
   - Tambah hidden element: `<span data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" class="hidden"></span>`
   - Tambah button: `<button onclick="addToInquiry({{ $product->id }})" class="btn btn-primary">Tambah ke Daftar Minat</button>`
5. Update `resources/views/partials/inquiry-bar.blade.php` (dari Phase 1 placeholder):
   - Fixed bottom, z-300, bg-elevated, border-t, shadow-md, class `hidden`
   - Count: `<span id="inquiry-bar-count" class="font-bold text-accent">0</span>`
   - Button: link to `route('inquiry.index')`

**Validation**:
- [x] Buka product detail → klik "Tambah ke Daftar Minat" → alert "Ditambahkan"
- [x] Inquiry bar muncul di bottom dengan count
- [x] Refresh page → bar tetap muncul (localStorage persist)
- [x] Buka di tab baru → bar muncul (same localStorage)
- [x] Add 51st item → alert "Maksimal 50 produk per inquiry"
- [x] `localStorage.getItem('leaderhub_inquiry')` di console → JSON dengan items

**Effort**: 3h

---

### T07: Inquiry form & submit (controller, view, mailable, routes)

**Goal**: Buat InquiryController, inquiry page view, success view, mailable, routes.

**Steps**:
1. `php artisan make:controller InquiryController`
2. `php artisan make:mail InquiryReceived`
3. Edit `InquiryController.php`:
   - `index()`: return view `inquiry.index`
   - `store(Request $request)`:
     - Rate limit: `RateLimiter::tooManyAttempts('inquiry:' . $request->ip(), 5)` → reject
     - `RateLimiter::hit('inquiry:' . $request->ip(), 3600)`
     - Honeypot: `if ($request->filled('website')) abort(422)`
     - Validate: customer_name (required min 3), customer_phone (required min 8), customer_email (nullable email), message (nullable), items (required array min 1), items.*.product_id (required exists), items.*.qty (required integer min 1)
     - `InquiryService::create($validated, $validated['items'])`
     - `Mail::to(Setting::get('email', 'contact'))->send(new InquiryReceived($inquiry))`
     - If channel == 'wa': redirect to wa.me with buildWaMessage
     - If channel == 'email': return view `inquiry.success`
4. Edit `InquiryReceived.php` mailable:
   - Constructor: `public Inquiry $inquiry`
   - Envelope: subject "Inquiry Baru: {inquiry_number}"
   - Content: view `emails.inquiry-received`
5. Buat `resources/views/emails/inquiry-received.blade.php`:
   - Plain HTML email: inquiry number, customer info, items list
6. Buat `resources/views/inquiry/index.blade.php`:
   - `@extends('layouts.app')`
   - H1 "Daftar Minat Saya"
   - `<div id="inquiry-list">` — di-populate oleh JS
   - `<div id="inquiry-form" class="hidden">` — form dengan honeypot, CSRF, fields, hidden items inputs, submit buttons (WA + email)
   - `<div id="empty-state" class="hidden">` — "Belum ada produk di daftar minat" + link to products
   - JS module: import getCart, removeItem, updateQty from inquiry.js, populate list, generate hidden inputs, handle remove/qty change
7. Buat `resources/views/inquiry/success.blade.php`:
   - "Inquiry Anda telah terkirim"
   - Inquiry number (prominent)
   - Items summary
   - Link to homepage
8. Tambah routes di `routes/web.php`:
   ```php
   Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiry.index');
   Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
   ```

**Validation**:
- [x] `/inquiry` dengan cart kosong → empty state tampil, form hidden
- [x] `/inquiry` dengan cart ada → list items tampil, form tampil
- [x] Edit qty → hidden input update
- [x] Hapus item → item hilang dari list, bar count update
- [x] Submit via WA → redirect ke wa.me dengan message benar (cek URL)
- [x] Submit via email → success page tampil dengan inquiry number
- [x] Inquiry tersimpan di DB (cek via tinker: `Inquiry::latest()->first()`)
- [x] Email terkirim ke admin (cek `storage/logs/laravel.log` atau mailtrap)
- [x] Rate limit: submit 6x dalam 1 jam → error "Terlalu banyak inquiry"
- [x] Honeypot: isi field `website` → 422 abort
- [x] Validasi: nama < 3 char → validation error
- [x] Validasi: phone < 8 char → validation error
- [x] Validasi: email invalid → validation error
- [x] Validasi: items kosong → validation error
- [x] CSRF: POST tanpa token → 419

**Effort**: 6h

---

### T08: Demo content seeder (blog + inquiry)

**Goal**: Buat seeder untuk 5 blog post dan 1 inquiry dengan items.

**Steps**:
1. Buat `core/database/seeders/BlogInquirySeeder.php`:
   - Create blog category "Tips" (slug: tips)
   - Create blog tag "Strength" (slug: strength)
   - Get first user (author)
   - Loop 5x: create BlogPost (title "Tips Fitness #N", slug "post-N", content, status published, published_at now()-N days, reading_time 5) → sync tags
   - Get first product
   - Create Inquiry (inquiry_number INQ-2026-00001, customer_name "John Doe", phone "081234567890", email "john@example.com", message "Saya tertarik", status "new")
   - Create InquiryItem (inquiry_id, product_id, quantity 1)
2. Register di `apps/website/database/seeders/DatabaseSeeder.php`
3. `php artisan db:seed --class=Leaderhub\\Core\\Database\\Seeders\\BlogInquirySeeder`

**Validation**:
- [x] Seeder berhasil tanpa error
- [x] 5 blog post tampil di `/blog`
- [x] 1 inquiry tampil di `/admin/inquiries`
- [x] Inquiry punya 1 item (product)

**Effort**: 1h

---

## Final Validation

Jalankan semua test ini sebelum lanjut ke Phase 4:

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website

# 1. Migrations clean
php artisan migrate:status | grep Pending && echo "✗ Pending migration" || echo "✓ All migrations ran"

# 2. Seeder clean
php artisan db:seed --class=Leaderhub\\Core\\Database\\Seeders\\BlogInquirySeeder --force
echo "✓ Seeder OK"

# 3. Serve & test
php artisan serve --port=8000 &
sleep 2

# Blog listing
curl -s http://localhost:8000/blog | grep -q "Tips Fitness" && echo "✓ Blog listing OK" || echo "✗ Blog listing FAIL"

# Blog detail
curl -s http://localhost:8000/blog/post-1 | grep -q "Article\|Tips Fitness" && echo "✓ Blog detail OK" || echo "✗ Blog detail FAIL"

# Blog 404
curl -s -o /dev/null -w "%{http_code}" http://localhost:8000/blog/nonexistent | grep -q "404" && echo "✓ Blog 404 OK" || echo "✗ Blog 404 FAIL"

# Inquiry page (empty cart)
curl -s http://localhost:8000/inquiry | grep -q "Daftar Minat" && echo "✓ Inquiry page OK" || echo "✗ Inquiry page FAIL"

# Inquiry submit (email channel)
CSRF=$(curl -s http://localhost:8000/inquiry -c /tmp/cookie.txt | grep csrf-token | sed 's/.*content="\([^"]*\)".*/\1/')
curl -s -X POST http://localhost:8000/inquiry \
  -H "X-CSRF-TOKEN: $CSRF" \
  -b /tmp/cookie.txt \
  -d "customer_name=Test User&customer_phone=081234567890&customer_email=test@example.com&message=Test&items[1][product_id]=1&items[1][qty]=1&channel=email" \
  | grep -q "Inquiry Anda telah terkirim\|INQ-" && echo "✓ Inquiry submit OK" || echo "✗ Inquiry submit FAIL"

# Inquiry in DB
php artisan tinker --execute="
use Leaderhub\Core\Models\Inquiry;
\$i = Inquiry::latest()->first();
echo 'Inquiry: ' . \$i->inquiry_number . ' | Items: ' . \$i->items->count() . PHP_EOL;
"

# Rate limit (submit 5 more, should be blocked on 6th)
for i in 1 2 3 4 5; do
  curl -s -X POST http://localhost:8000/inquiry -H "X-CSRF-TOKEN: $CSRF" -b /tmp/cookie.txt -d "customer_name=Rate Test&customer_phone=081234567890&items[1][product_id]=1&items[1][qty]=1&channel=email" > /dev/null
done
curl -s -X POST http://localhost:8000/inquiry -H "X-CSRF-TOKEN: $CSRF" -b /tmp/cookie.txt -d "customer_name=Rate Test&customer_phone=081234567890&items[1][product_id]=1&items[1][qty]=1&channel=email" | grep -q "Terlalu banyak" && echo "✓ Rate limit OK" || echo "✗ Rate limit FAIL"

kill %1

# 4. FK integrity check
php artisan tinker --execute="
use Leaderhub\Core\Models\BlogPost;
use Leaderhub\Core\Models\Inquiry;
echo 'Blog posts: ' . BlogPost::withoutGlobalScope('published')->count() . PHP_EOL;
echo 'Published: ' . BlogPost::all()->count() . PHP_EOL;
echo 'Inquiries: ' . Inquiry::count() . PHP_EOL;
"

# 5. Git commit
cd /run/media/salmon/data/Projects/leaderhub
git add -A
git commit -m "Phase 3: Blog & inquiry system — blog frontend, inquiry cart, form submit, admin"
```

**Jika semua ✓ → lanjut Phase 4. Jika ada ✗ → fix dulu, jangan lanjut.**