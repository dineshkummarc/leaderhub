# Design — Phase 3: Blog & Inquiry System

## Context
- **Architecture**: `docs/02-architecture.md` — monorepo, core package, shared DB
- **Database schema**: `docs/03-database-schema.md` §3.28-3.31 (blog tables), §3.16-3.17 (inquiry tables)
- **Feature specs**: `docs/07-features/03-inquiry-system.md`, `docs/07-features/05-blog.md`
- **Step-by-step**: `docs/tasks/phase-3-blog-inquiry.md`
- **Design system**: `docs/04-design-system.md` — Modern Premium, editorial blog card §3.5, inquiry bar §3.3

## Approach

### 1. Blog System

#### 1.1 Database Schema

**Table `blog_categories`**:
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT PK | AUTO_INCREMENT | — |
| slug | VARCHAR(255) | UNIQUE | URL-friendly identifier |
| name | VARCHAR(255) | NOT NULL | Display name |
| description | TEXT | NULLABLE | — |
| created_at, updated_at | TIMESTAMP | — | — |

**Table `blog_tags`**:
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT PK | AUTO_INCREMENT | — |
| slug | VARCHAR(255) | UNIQUE | — |
| name | VARCHAR(255) | NOT NULL | — |
| created_at, updated_at | TIMESTAMP | — | — |

**Table `blog_posts`**:
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT PK | AUTO_INCREMENT | — |
| blog_category_id | BIGINT FK | → blog_categories.id, cascadeOnDelete | — |
| author_id | BIGINT FK | → users.id, cascadeOnDelete | User who wrote the post |
| slug | VARCHAR(255) | UNIQUE | — |
| title | VARCHAR(255) | NOT NULL | — |
| title_en | VARCHAR(255) | NULLABLE | English translation (for multi-bahasa, aktif Phase 5) |
| excerpt | TEXT | NULLABLE | Short summary for listing |
| content | LONGTEXT | NOT NULL | Rich text (HTML from RichEditor) |
| content_en | LONGTEXT | NULLABLE | English content (aktif Phase 5) |
| featured_image | VARCHAR(255) | NULLABLE | Path to storage |
| status | VARCHAR(20) | DEFAULT 'draft' | draft, published, archived |
| published_at | TIMESTAMP | NULLABLE | When to publish (future scheduling) |
| reading_time | INT | NULLABLE | Minutes (auto-calc or manual) |
| seo_title | VARCHAR(255) | NULLABLE | Override for SEO |
| seo_description | TEXT | NULLABLE | Override for SEO |
| created_at, updated_at | TIMESTAMP | — | — |

**Indexes**: `INDEX(status, published_at)`, `INDEX(blog_category_id)`

**Table `blog_post_tags`** (pivot):
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| blog_post_id | BIGINT FK | → blog_posts.id, cascadeOnDelete | — |
| blog_tag_id | BIGINT FK | → blog_tags.id, cascadeOnDelete | — |

**Primary key**: composite `(blog_post_id, blog_tag_id)` — no separate `id` column

#### 1.2 Models (di core/src/Models/)

**BlogCategory**:
```php
class BlogCategory extends Model {
    protected $guarded = [];
    public function posts(): HasMany { return $this->hasMany(BlogPost::class); }
}
```

**BlogTag**:
```php
class BlogTag extends Model {
    protected $guarded = [];
    public function posts(): BelongsToMany { return $this->belongsToMany(BlogPost::class, 'blog_post_tags'); }
}
```

**BlogPost** — dengan global scope `published`:
```php
class BlogPost extends Model {
    protected $guarded = [];
    protected $casts = ['published_at' => 'datetime'];

    protected static function booted(): void {
        // Global scope: hanya tampilkan post yang published DAN published_at <= now
        // Ini dipakai di frontend. Admin pakai withoutGlobalScope() untuk lihat draft.
        static::addGlobalScope('published', fn (Builder $q) =>
            $q->where('status', 'published')->where('published_at', '<=', now())
        );
    }

    public function category(): BelongsTo { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function tags(): BelongsToMany { return $this->belongsToMany(BlogTag::class, 'blog_post_tags'); }
}
```

**PENTING untuk AI agent**: Global scope `published` otomatis filter di semua query. Saat admin perlu lihat draft, pakai `BlogPost::withoutGlobalScope('published')->...`. Saat frontend show detail by slug, juga pakai `withoutGlobalScope` lalu filter manual agar admin bisa preview draft via URL (opsional, atau pakai Filament view saja).

#### 1.3 Blog Frontend

**BlogController@index** — listing page:
```php
public function index(Request $request)
{
    $query = BlogPost::with(['category', 'author', 'tags']);

    // Filter by category slug
    if ($cat = $request->get('category')) {
        $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
    }
    // Filter by tag slug
    if ($tag = $request->get('tag')) {
        $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
    }
    // Search by title (LIKE, bukan fullText karena blog_posts belum punya fullText index)
    if ($search = $request->get('q')) {
        $query->where('title', 'like', "%{$search}%");
    }

    $posts = $query->latest('published_at')->paginate(9)->withQueryString();
    return view('blog.index', compact('posts'));
}
```

**PENTING**: `withQueryString()` wajib agar pagination link preserve filter/search query params.

**BlogController@show** — detail page:
```php
public function show(string $slug)
{
    // withoutGlobalScope agar admin bisa preview draft via URL jika perlu
    $post = BlogPost::withoutGlobalScope('published')
        ->where('slug', $slug)
        ->firstOrFail();

    // Related: same category, exclude self, limit 3
    $related = BlogPost::where('blog_category_id', $post->blog_category_id)
        ->where('id', '!=', $post->id)
        ->limit(3)
        ->get();

    return view('blog.show', compact('post', 'related'));
}
```

**Routes** (urutan PENTING — taruh sebelum route `/{slug}` dari Phase 1):
```php
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
// Route::get('/{slug}', [PageController::class, 'show'])->name('page.show'); // PALING BAWAH
```

**PENTING untuk AI agent**: Route `/blog/{slug}` HARUS didaftarkan SEBELUM route `/{slug}` (PageController). Jika tidak, `/blog/post-1` akan ditangkap oleh `/{slug}` dan mencari Page dengan slug `blog` → 404. Urutan route di Laravel: yang pertama match, yang dipakai.

#### 1.4 Blog Views

**`blog/index.blade.php`** — editorial grid:
- Grid 3 kolom (desktop), 1 kolom (mobile)
- Card: aspect 16:10 image, category label (text-xs muted), title (font-semibold text-lg, 2-line clamp), meta (author · date · reading time)
- Pagination di bawah
- Empty state jika tidak ada post

**`blog/show.blade.php`** — detail:
- Breadcrumb (Home / Blog / Category)
- Featured image (full-width, aspect 16:10, rounded-lg)
- Category badge, title (h1), meta (author · date · reading time)
- Content (prose, max-width 720px)
- Tags (badge neutral, clickable → filter)
- Related posts section (3 cards, same card style as listing)
- JSON-LD Article schema (headline, author Person, datePublished, image, publisher Organization)

#### 1.5 Blog Admin (Filament)

**BlogPostResource**:
- Form: title (live → auto slug), slug (unique), category select (createOptionForm inline), author select (default auth()->id()), tags multi-select (createOptionForm inline), excerpt textarea, content RichEditor, featured_image FileUpload, status select (draft/published/archived), published_at DateTimePicker, SEO section (collapsible: seo_title, seo_description)
- Table: title (searchable), category, author, status badge, published_at
- Filter: status, category

**BlogCategoryResource** & **BlogTagResource**: simple CRUD (name, slug).

---

### 2. Inquiry System

#### 2.1 Database Schema

**Table `inquiries`**:
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT PK | AUTO_INCREMENT | — |
| inquiry_number | VARCHAR(50) | UNIQUE | Format: `INQ-YYYY-XXXXX` |
| uuid | CHAR(36) | UNIQUE | Public ID (for sharing URL, tidak expose auto-increment ID) |
| customer_name | VARCHAR(255) | NOT NULL | — |
| customer_phone | VARCHAR(50) | NOT NULL | WhatsApp number |
| customer_email | VARCHAR(255) | NULLABLE | — |
| message | TEXT | NULLABLE | Customer's message |
| status | VARCHAR(20) | DEFAULT 'new' | new, contacted, converted, closed, lost |
| admin_notes | TEXT | NULLABLE | Private notes from admin |
| converted_sales_order_id | BIGINT | NULLABLE | FK ke sales_orders (Phase 4, belum ada saat ini — pakai unsignedBigInteger tanpa FK constraint dulu, add FK di Phase 4) |
| ip_address | VARCHAR(45) | NULLABLE | Untuk rate limit & audit |
| user_agent | TEXT | NULLABLE | — |
| created_at, updated_at | TIMESTAMP | — | — |

**Indexes**: `INDEX(status, created_at)`, `INDEX(inquiry_number)`

**PENTING untuk AI agent**: Kolom `converted_sales_order_id` belum punya FK constraint karena tabel `sales_orders` belum ada (dibuat di Phase 4). Jangan tambah FK sekarang. Di Phase 4, migration sales_orders akan add FK: `$table->foreign('converted_sales_order_id')->references('id')->on('sales_orders')->nullOnDelete();`

**Table `inquiry_items`**:
| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | BIGINT PK | AUTO_INCREMENT | — |
| inquiry_id | BIGINT FK | → inquiries.id, cascadeOnDelete | — |
| product_id | BIGINT FK | → products.id, cascadeOnDelete | — |
| quantity | INT | DEFAULT 1 | — |
| notes | TEXT | NULLABLE | — |

**Index**: `INDEX(inquiry_id)`

#### 2.2 Models (di core/src/Models/)

**Inquiry**:
```php
class Inquiry extends Model {
    protected $guarded = [];
    protected $casts = ['uuid' => 'string']; // UUID disimpan sebagai string
    public function items(): HasMany { return $this->hasMany(InquiryItem::class); }
}
```

**InquiryItem**:
```php
class InquiryItem extends Model {
    protected $guarded = [];
    public function inquiry(): BelongsTo { return $this->belongsTo(Inquiry::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
```

#### 2.3 InquiryService (di core/src/Services/)

**PENTING untuk AI agent**: InquiryService adalah business logic terpusat. Jangan taruh logic ini di controller — pakai service agar bisa dipakai ulang dan ditest.

```php
class InquiryService
{
    /**
     * Create inquiry dengan items dalam DB transaction.
     * Atomic: jika gagal di tengah, rollback semua.
     */
    public static function create(array $data, array $items): Inquiry
    {
        return DB::transaction(function () use ($data, $items) {
            $inquiry = Inquiry::create([
                'inquiry_number' => static::generateNumber(),
                'uuid' => Str::uuid(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'message' => $data['message'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'new',
            ]);

            foreach ($items as $item) {
                InquiryItem::create([
                    'inquiry_id' => $inquiry->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                ]);
            }
            return $inquiry;
        });
    }

    /**
     * Generate inquiry number: INQ-YYYY-XXXXX
     * Sequential per year. Atomic: pakai max(id) + 1 dalam transaction.
     *
     * PENTING: Race condition bisa terjadi jika 2 inquiry submit bersamaan.
     * Untuk MVP, pakai max(id)+1. Untuk production high-traffic, pertimbangkan
     * lock table atau sequence table. Tapi untuk distributor fitness (low volume),
     * ini cukup aman.
     */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = Inquiry::whereYear('created_at', $year)->max('id') ?? 0;
        $next = $last + 1;
        return sprintf('INQ-%d-%05d', $year, $next);
    }

    /**
     * Build WhatsApp message dari inquiry.
     * Format: multi-line, mudah dibaca admin di WA.
     */
    public static function buildWaMessage(Inquiry $inquiry): string
    {
        $lines = ["Halo LeaderHub, saya tertarik dengan produk berikut:", "", "Inquiry: {$inquiry->inquiry_number}"];
        foreach ($inquiry->items as $i => $item) {
            $lines[] = ($i + 1) . ". {$item->product->name} ({$item->product->sku}) - {$item->quantity} unit";
        }
        $lines[] = "";
        $lines[] = "Nama: {$inquiry->customer_name}";
        if ($inquiry->message) $lines[] = "Pesan: {$inquiry->message}";
        return implode("\n", $lines);
    }
}
```

#### 2.4 Inquiry Cart (Frontend — localStorage)

**PENTING untuk AI agent**: Cart disimpan di localStorage browser, BUKAN di server. Ini karena pengunjung adalah guest (tidak login). Cart persist across page reloads dan tabs.

**File `resources/js/inquiry.js`**:
```js
const STORAGE_KEY = 'leaderhub_inquiry';

export function getCart() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY)) || { items: [] };
    } catch {
        return { items: [] };
    }
}

export function saveCart(cart) {
    cart.updated_at = new Date().toISOString();
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateBar(); // Update floating bar setiap save
}

export function addItem(productId, name, slug, qty = 1) {
    const cart = getCart();
    const existing = cart.items.find(i => i.product_id === productId);
    if (existing) {
        existing.qty += qty;
    } else {
        cart.items.push({
            product_id: productId,
            name,
            slug,
            qty,
            added_at: new Date().toISOString()
        });
    }
    saveCart(cart);
}

export function removeItem(productId) {
    const cart = getCart();
    cart.items = cart.items.filter(i => i.product_id !== productId);
    saveCart(cart);
}

export function updateQty(productId, qty) {
    const cart = getCart();
    const item = cart.items.find(i => i.product_id === productId);
    if (item) item.qty = Math.max(1, qty); // Min 1
    saveCart(cart);
}

export function clearCart() {
    localStorage.removeItem(STORAGE_KEY);
    updateBar();
}

export function getCount() {
    return getCart().items.reduce((sum, i) => sum + i.qty, 0);
}

export function updateBar() {
    const count = getCount();
    const bar = document.getElementById('inquiry-bar');
    const countEls = document.querySelectorAll('#inquiry-count, #inquiry-bar-count');
    if (count > 0) {
        bar?.classList.remove('hidden');
        countEls.forEach(el => el.textContent = count);
    } else {
        bar?.classList.add('hidden');
    }
}

// Global function untuk dipanggil dari onclick di HTML
window.addToInquiry = function(productId) {
    // Ambil name & slug dari data attribute di product card/detail
    const el = document.querySelector(`[data-product-id="${productId}"]`);
    const name = el?.dataset.name || 'Product';
    const slug = el?.dataset.slug || '';
    addItem(productId, name, slug);
    // Feedback ke user (bisa pakai toast/alert, sederhana dulu)
    alert('Ditambahkan ke daftar minat');
};

// Update bar saat page load
document.addEventListener('DOMContentLoaded', updateBar);
```

**Import di `resources/js/app.js`**:
```js
import './bootstrap';
import './inquiry'; // Pastikan ini ada
```

**PENTING**: `window.addToInquiry` harus global karena dipanggil dari `onclick` di Blade template. Jangan pakai module-only scope.

#### 2.5 Floating Inquiry Bar

**`partials/inquiry-bar.blade.php`** (update dari Phase 1 placeholder):
```blade
<div id="inquiry-bar" class="fixed bottom-0 left-0 right-0 z-300 bg-elevated border-t border-border shadow-md hidden">
    <div class="max-w-[1120px] mx-auto px-8 py-3 flex items-center justify-between">
        <span class="text-sm">
            <span id="inquiry-bar-count" class="font-bold text-accent">0</span> produk di daftar minat
        </span>
        <a href="{{ route('inquiry.index') }}" class="btn btn-primary text-sm">Lihat Daftar</a>
    </div>
</div>
```

**PENTING**: Bar punya class `hidden` by default. JS `updateBar()` akan remove `hidden` saat ada item. Pastikan `partials/inquiry-bar.blade.php` di-include di `layouts/app.blade.php` (sudah dari Phase 1).

#### 2.6 "Daftar Minat Saya" Page

**Route**: `Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiry.index');`

**View `inquiry/index.blade.php`**:
- Container max-width 720px
- H1 "Daftar Minat Saya"
- Item list: di-render oleh JS dari localStorage (bukan dari server — karena cart di localStorage)
- Setiap item: product name, qty input (editable), hapus button
- Form (nama, WA, email, pesan) — tampil hanya jika cart tidak kosong
- Hidden inputs untuk items (dibuat oleh JS, dikirim saat submit)
- Empty state: "Belum ada produk di daftar minat" + link ke `/products`
- Honeypot field: `<input name="website" class="hidden" tabindex="-1">` — bot akan isi, human tidak

**PENTING untuk AI agent**: Page ini render item cart via JavaScript (bukan Blade) karena data ada di localStorage. Blade hanya sediakan container `<div id="inquiry-list">` dan form. JS populate items dan hidden inputs.

#### 2.7 Inquiry Submit

**Route**: `Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');`

**InquiryController@store**:
```php
public function store(Request $request)
{
    // 1. Rate limit: 5 inquiry per IP per jam
    $key = 'inquiry:' . $request->ip();
    if (RateLimiter::tooManyAttempts($key, 5)) {
        return back()->with('error', 'Terlalu banyak inquiry. Coba lagi nanti.');
    }
    RateLimiter::hit($key, 3600); // 3600 detik = 1 jam

    // 2. Honeypot: jika field "website" terisi → bot → abort
    if ($request->filled('website')) {
        abort(422);
    }

    // 3. Validasi
    $validated = $request->validate([
        'customer_name' => 'required|min:3',
        'customer_phone' => 'required|min:8',
        'customer_email' => 'nullable|email',
        'message' => 'nullable',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.qty' => 'required|integer|min:1',
    ]);

    // 4. Create inquiry via service
    $inquiry = InquiryService::create($validated, $validated['items']);

    // 5. Email ke admin
    Mail::to(Setting::get('email', 'contact'))->send(new InquiryReceived($inquiry));

    // 6. Channel: WA atau email
    if ($request->input('channel') === 'wa') {
        $wa = Setting::get('whatsapp', 'contact');
        $msg = urlencode(InquiryService::buildWaMessage($inquiry));
        return redirect("https://wa.me/{$wa}?text={$msg}");
    }

    // 7. Email channel → success page
    return view('inquiry.success', ['inquiry' => $inquiry]);
}
```

**PENTING untuk AI agent**:
- Rate limit pakai `Illuminate\Support\Facades\RateLimiter` (Laravel 10+)
- Honeypot field name `website` — HARUS hidden di form, jangan kasih label visible
- `items` array dikirim dari hidden inputs yang dibuat JS
- Channel `wa` → redirect ke wa.me (external), channel `email` → return success view
- Email dikirim ke admin (settings `email` group `contact`) — bukan ke customer

#### 2.8 Mailable

**`app/Mail/InquiryReceived.php`**:
```php
class InquiryReceived extends Mailable
{
    use Queueable;
    public function __construct(public Inquiry $inquiry) {}
    public function envelope(): Envelope { return new Envelope(subject: "Inquiry Baru: {$this->inquiry->inquiry_number}"); }
    public function content(): Content { return new Content(view: 'emails.inquiry-received'); }
}
```

**View `emails/inquiry-received.blade.php`**: plain HTML email dengan inquiry number, customer info, items list.

#### 2.9 Inquiry Admin (Filament)

**InquiryResource** (di website admin panel):
- Form: inquiry_number (disabled), customer_name (disabled), customer_phone (disabled), customer_email (disabled), message (disabled), status (select: new/contacted/converted/closed/lost), admin_notes (textarea)
- Table: inquiry_number (searchable), customer_name (searchable), customer_phone, status (badge), created_at (dateTime)
- Filter: status
- Actions: View, Edit (update status + notes), WA action (open wa.me dengan customer_phone), Delete

**PENTING**: InquiryResource adalah untuk admin merespons inquiry, BUKAN untuk create inquiry dari admin. Field customer_* di-disable (read-only). Admin hanya bisa ubah status dan tambah notes.

---

## Models / Tables Affected

| Table | Action | Phase |
|-------|--------|-------|
| `blog_categories` | CREATE | Phase 3 |
| `blog_tags` | CREATE | Phase 3 |
| `blog_posts` | CREATE | Phase 3 |
| `blog_post_tags` | CREATE | Phase 3 |
| `inquiries` | CREATE | Phase 3 |
| `inquiry_items` | CREATE | Phase 3 |
| `inquiries.converted_sales_order_id` | ADD FK constraint | Phase 4 (saat sales_orders dibuat) |

## Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Route `/blog/{slug}` ditangkap `/{slug}` | Blog detail 404 | Daftarkan route `/blog/*` SEBELUM `/{slug}` di routes/web.php |
| Race condition inquiry number | Duplicate INQ number | Pakai `max(id)+1` dalam DB transaction. Untuk high-traffic, add lock table. |
| localStorage cart hilang | User kehilangan daftar minat | Expected behavior (guest cart). Tidak ada mitigasi — ini trade-off model guest. |
| Honeypot field terisi oleh autofill browser | False positive bot | Field name `website` jarang di-autofill. Jika masalah, ganti ke `company_url`. |
| Rate limit terlalu ketat | User legitimate ditolak | 5/IP/jam cukup untuk distributor. Bisa dinaikkan di .env jika perlu. |
| RichEditor XSS | Script injection di blog content | Filament RichEditor sanitize HTML by default. Jangan disable sanitization. |

## Dependencies

- **Phase 0**: Monorepo, core package, shared DB
- **Phase 1**: Layout, Filament admin, settings (untuk WhatsApp number, email admin)
- **Phase 2**: Products table (inquiry items FK ke products), product card partial (data-product-id attribute)

## Testing Strategy

### Manual Testing Checklist
1. **Blog listing**: Buka `/blog` → 9 post per page, filter category & tag berfungsi, search berfungsi
2. **Blog detail**: Buka `/blog/{slug}` → content, featured image, tags, related posts, JSON-LD di view source
3. **Blog admin**: Login `/admin` → create post dengan RichEditor, set published → tampil di frontend
4. **Inquiry cart**: Buka product detail → klik "Tambah ke Daftar Minat" → inquiry bar muncul dengan count
5. **Inquiry cart persist**: Refresh page → bar tetap muncul (localStorage)
6. **Inquiry page**: Buka `/inquiry` → list items, edit qty, hapus item
7. **Inquiry submit WA**: Isi form → klik "Kirim via WhatsApp" → redirect ke wa.me dengan message benar
8. **Inquiry submit email**: Isi form → klik "Kirim via Email" → success page tampil, email terkirim ke admin
9. **Inquiry admin**: Login admin → `/admin/inquiries` → inquiry baru tampil, ubah status, WA action
10. **Rate limit**: Submit 6 inquiry dalam 1 jam → error "Terlalu banyak inquiry"
11. **Honeypot**: Isi field `website` → submit → 422 abort
12. **Empty state**: Hapus semua item dari cart → `/inquiry` tampil empty state

### Automated Testing (future)
- Feature test: blog listing, blog detail, inquiry submit (valid, invalid, rate limit, honeypot)
- Unit test: InquiryService::generateNumber(), InquiryService::buildWaMessage()