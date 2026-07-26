# Phase 3 — Blog & Inquiry (Detail Task Step-by-Step)

> **Estimasi**: 2 minggu | **Tujuan**: Blog frontend, inquiry cart, form submit, admin view
> **Prasyarat**: Phase 2 selesai (homepage, catalog, gallery jalan)

## Validasi Akhir Phase 3
1. `/blog` listing dengan filter category, tag, search
2. `/blog/{slug}` detail dengan rich content, TOC, related
3. Inquiry cart (localStorage): add, remove, edit qty
4. Floating inquiry bar muncul saat ada item
5. `/inquiry` page: list, form, submit via WA/email
6. Inquiry tersimpan di DB dengan nomor INQ-YYYY-XXXXX
7. Admin (Filament) bisa view & respond inquiry
8. Rate limit 5/IP/jam berfungsi
9. Tidak ada error, FK rapi

---

## Task P3-T01: Migrations — blog & inquiry

### Step-by-Step

**Step 1**: Migration blog
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_03_000001_create_blog_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->longText('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft'); // draft, published, archived
            $table->timestamp('published_at')->nullable();
            $table->integer('reading_time')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->index(['status', 'published_at']);
            $table->index('blog_category_id');
        });
        Schema::create('blog_post_tags', function (Blueprint $table) {
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('blog_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['blog_post_id', 'blog_tag_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('blog_post_tags');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('blog_tags');
        Schema::dropIfExists('blog_categories');
    }
};
PHP
```

**Step 2**: Migration inquiry
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_03_000002_create_inquiries_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_number')->unique();
            $table->uuid('uuid')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new, contacted, converted, closed, lost
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('converted_sales_order_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
            $table->index('inquiry_number');
        });
        Schema::create('inquiry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->text('notes')->nullable();
            $table->index('inquiry_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('inquiry_items');
        Schema::dropIfExists('inquiries');
    }
};
PHP
```

**Step 3**: Models di core
```bash
cd /run/media/salmon/data/Projects/leaderhub/core/src/Models

cat > BlogCategory.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $guarded = [];
    public function posts(): HasMany { return $this->hasMany(BlogPost::class); }
}
PHP

cat > BlogTag.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    protected $guarded = [];
    public function posts(): BelongsToMany { return $this->belongsToMany(BlogPost::class); }
}
PHP

cat > BlogPost.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogPost extends Model
{
    protected $guarded = [];
    protected $casts = ['published_at' => 'datetime'];

    protected static function booted(): void
    {
        static::addGlobalScope('published', fn (Builder $q) => $q->where('status', 'published')->where('published_at', '<=', now()));
    }

    public function category(): BelongsTo { return $this->belongsTo(BlogCategory::class, 'blog_category_id'); }
    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function tags(): BelongsToMany { return $this->belongsToMany(BlogTag::class, 'blog_post_tags'); }
}
PHP

cat > Inquiry.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    protected $guarded = [];
    protected $casts = ['uuid' => 'string'];
    public function items(): HasMany { return $this->hasMany(InquiryItem::class); }
}
PHP

cat > InquiryItem.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryItem extends Model
{
    protected $guarded = [];
    public function inquiry(): BelongsTo { return $this->belongsTo(Inquiry::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
PHP
```

**Step 4**: Migration
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan migrate --path=../../core/database/migrations --force
```

### Validasi P3-T01
- [ ] Migration berhasil
- [ ] Tabel `blog_categories`, `blog_tags`, `blog_posts`, `blog_post_tags`, `inquiries`, `inquiry_items` ada
- [ ] FK `blog_posts.blog_category_id` → `blog_categories.id`
- [ ] FK `blog_posts.author_id` → `users.id`
- [ ] Composite PK `blog_post_tags(blog_post_id, blog_tag_id)`
- [ ] Models accessible via tinker

---

## Task P3-T02: Filament resources — Blog & Inquiry

### Step-by-Step

**Step 1**: Buat resources
```bash
php artisan make:filament-resource BlogPost
php artisan make:filament-resource BlogCategory
php artisan make:filament-resource BlogTag
php artisan make:filament-resource Inquiry --view
```

**Step 2**: Edit `BlogPostResource.php`
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('title')->required()->live()->afterStateUpdated(fn ($state, $set) => $set('slug', str($state)->slug())),
        Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
        Forms\Components\Select::make('blog_category_id')->relationship('category', 'name')->required()->createOptionForm([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required(),
        ]),
        Forms\Components\Select::make('author_id')->relationship('author', 'name')->required()->default(auth()->id()),
        Forms\Components\Select::make('tags')->relationship('tags', 'name')->multiple()->createOptionForm([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('slug')->required(),
        ]),
        Forms\Components\Textarea::make('excerpt'),
        Forms\Components\RichEditor::make('content')->columnSpanFull(),
        Forms\Components\FileUpload::make('featured_image')->image()->directory('blog'),
        Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required()->default('draft'),
        Forms\Components\DateTimePicker::make('published_at'),
        Forms\Components\Section::make('SEO')->schema([
            Forms\Components\TextInput::make('seo_title'),
            Forms\Components\Textarea::make('seo_description'),
        ])->collapsible(),
    ]);
}
```

**Step 3**: Edit `InquiryResource.php` (read-only + respond)
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('inquiry_number')->disabled(),
        Forms\Components\TextInput::make('customer_name')->disabled(),
        Forms\Components\TextInput::make('customer_phone')->disabled(),
        Forms\Components\TextInput::make('customer_email')->disabled(),
        Forms\Components\Textarea::make('message')->disabled()->columnSpanFull(),
        Forms\Components\Select::make('status')->options(['new' => 'New', 'contacted' => 'Contacted', 'converted' => 'Converted', 'closed' => 'Closed', 'lost' => 'Lost']),
        Forms\Components\Textarea::make('admin_notes')->columnSpanFull(),
    ]);
}

public static function table(Table $table): Table
{
    return $table->columns([
        Tables\Columns\TextColumn::make('inquiry_number')->searchable(),
        Tables\Columns\TextColumn::make('customer_name')->searchable(),
        Tables\Columns\TextColumn::make('customer_phone'),
        Tables\Columns\TextColumn::make('status')->badge(),
        Tables\Columns\TextColumn::make('created_at')->dateTime(),
    ])->filters([
        Tables\Filters\SelectFilter::make('status'),
    ])->actions([
        Tables\Actions\ViewAction::make(),
        Tables\Actions\Action::make('wa')->url(fn ($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->customer_phone))->openUrlInNewTab(),
    ]);
}
```

### Validasi P3-T02
- [ ] CRUD blog post berfungsi (dengan tags, category, author)
- [ ] Inquiry list tampil, view detail, WA action button
- [ ] Status inquiry bisa diupdate

---

## Task P3-T03: Blog listing & detail

### Step-by-Step

**Step 1**: Buat BlogController
```bash
php artisan make:controller BlogController
```

Edit:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Leaderhub\Core\Models\BlogPost;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'author', 'tags']);
        if ($cat = $request->get('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
        }
        if ($tag = $request->get('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
        }
        if ($search = $request->get('q')) {
            $query->where('title', 'like', "%{$search}%");
        }
        $posts = $query->latest('published_at')->paginate(9)->withQueryString();
        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = BlogPost::withoutGlobalScope('published')->where('slug', $slug)->firstOrFail();
        $related = BlogPost::where('blog_category_id', $post->blog_category_id)->where('id', '!=', $post->id)->limit(3)->get();
        return view('blog.show', compact('post', 'related'));
    }
}
```

**Step 2**: Buat `resources/views/blog/index.blade.php`
```blade
@extends('layouts.app')
@section('title', 'Blog — LeaderHub')
@section('content')
<div class="max-w-[1120px] mx-auto px-8 py-12">
    <h1 class="text-h1 font-bold tracking-tight mb-8">Blog</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}" class="group block rounded-lg overflow-hidden border border-border bg-elevated hover:shadow-md transition">
            <div class="aspect-[16/10] bg-surface-2">
                @if($post->featured_image)<img src="{{ \Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">@endif
            </div>
            <div class="p-5">
                <div class="text-xs text-text-muted mb-2">{{ $post->category->name }}</div>
                <h2 class="font-semibold text-lg leading-snug mb-2 line-clamp-2">{{ $post->title }}</h2>
                <div class="text-xs text-text-muted">{{ $post->author->name }} · {{ $post->published_at?->format('d M Y') }}</div>
            </div>
        </a>
        @endforeach
    </div>
    {{ $posts->links() }}
</div>
@endsection
```

**Step 3**: Buat `resources/views/blog/show.blade.php`
```blade
@extends('layouts.app')
@section('title', $post->title . ' — LeaderHub Blog')
@section('meta_description', $post->excerpt ?? str($post->content)->stripTags()->limit(160))

@section('content')
<article class="max-w-[720px] mx-auto px-8 py-12">
    <nav class="text-sm text-text-muted mb-6"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('blog.index') }}">Blog</a> / {{ $post->category->name }}</nav>
    @if($post->featured_image)
    <img src="{{ \Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full aspect-[16/10] object-cover rounded-lg mb-8">
    @endif
    <div class="text-xs text-text-muted mb-2">{{ $post->category->name }}</div>
    <h1 class="text-h1 font-bold tracking-tight mb-3">{{ $post->title }}</h1>
    <div class="text-sm text-text-muted mb-8">{{ $post->author->name }} · {{ $post->published_at?->format('d M Y') }} · {{ $post->reading_time ?? 5 }} min read</div>
    <div class="prose prose-slate max-w-none">{!! $post->content !!}</div>
    @if($post->tags->isNotEmpty())
    <div class="flex gap-2 mt-8">
        @foreach($post->tags as $tag)
        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" class="badge badge-neutral">{{ $tag->name }}</a>
        @endforeach
    </div>
    @endif
</article>
@if($related->isNotEmpty())
<section class="max-w-[1120px] mx-auto px-8 py-12 border-t border-border">
    <h2 class="text-h3 font-bold tracking-tight mb-6">Artikel Terkait</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($related as $p)
        <a href="{{ route('blog.show', $p->slug) }}" class="group block rounded-lg overflow-hidden border border-border bg-elevated hover:shadow-md transition">
            <div class="aspect-[16/10] bg-surface-2">
                @if($p->featured_image)<img src="{{ \Storage::url($p->featured_image) }}" alt="{{ $p->title }}" class="w-full h-full object-cover">@endif
            </div>
            <div class="p-5"><h3 class="font-semibold line-clamp-2">{{ $p->title }}</h3></div>
        </a>
        @endforeach
    </div>
</section>
@endif

@section('scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $post->title }}",
  "author": { "@type": "Person", "name": "{{ $post->author->name }}" },
  "datePublished": "{{ $post->published_at?->toISOString() }}",
  "image": "{{ $post->featured_image ? asset('storage/' . $post->featured_image) : '' }}",
  "publisher": { "@type": "Organization", "name": "LeaderHub" }
}
</script>
@endsection
@endsection
```

**Step 4**: Routes
```php
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
```

### Validasi P3-T03
- [ ] `/blog` load, 9 post/page
- [ ] Filter category & tag berfungsi
- [ ] Search berfungsi
- [ ] `/blog/{slug}` detail load
- [ ] Related posts tampil
- [ ] JSON-LD Article ada

---

## Task P3-T04: Inquiry cart logic (localStorage)

### Step-by-Step

**Step 1**: Buat `resources/js/inquiry.js`
```js
const STORAGE_KEY = 'leaderhub_inquiry';

export function getCart() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || { items: [] }; }
    catch { return { items: [] }; }
}

export function saveCart(cart) {
    cart.updated_at = new Date().toISOString();
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateBar();
}

export function addItem(productId, name, slug, qty = 1) {
    const cart = getCart();
    const existing = cart.items.find(i => i.product_id === productId);
    if (existing) existing.qty += qty;
    else cart.items.push({ product_id: productId, name, slug, qty, added_at: new Date().toISOString() });
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
    if (item) item.qty = Math.max(1, qty);
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

window.addToInquiry = function(productId) {
    const name = document.querySelector(`[data-product-id="${productId}"]`)?.dataset.name || 'Product';
    const slug = document.querySelector(`[data-product-id="${productId}"]`)?.dataset.slug || '';
    addItem(productId, name, slug);
    alert('Ditambahkan ke daftar minat');
};

document.addEventListener('DOMContentLoaded', updateBar);
```

**Step 2**: Import di `resources/js/app.js`
```js
import './bootstrap';
import './inquiry';
```

**Step 3**: Update product card & detail untuk include data attribute
```blade
<!-- product-card.blade.php -->
<a href="..." data-product-id="{{ $product->id }}" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" ...>
```

### Validasi P3-T04
- [ ] Klik "Tambah ke Daftar Minat" → item masuk localStorage
- [ ] Inquiry bar muncul dengan count
- [ ] Refresh page → bar tetap muncul (persist)

---

## Task P3-T05: Inquiry form & submit (WA + email)

### Step-by-Step

**Step 1**: Buat InquiryService di core
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/src/Services/InquiryService.php << 'PHP'
<?php

namespace Leaderhub\Core\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Leaderhub\Core\Models\Inquiry;
use Leaderhub\Core\Models\InquiryItem;
use Leaderhub\Core\Models\Product;

class InquiryService
{
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

    public static function generateNumber(): string
    {
        $year = now()->year;
        $last = Inquiry::whereYear('created_at', $year)->max('id') ?? 0;
        $next = $last + 1;
        return sprintf('INQ-%d-%05d', $year, $next);
    }

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
PHP
```

**Step 2**: Buat Mailable
```bash
php artisan make:mail InquiryReceived
```

Edit `app/Mail/InquiryReceived.php`:
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Leaderhub\Core\Models\Inquiry;

class InquiryReceived extends Mailable
{
    use Queueable;

    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): \Illuminate\Mail\Envelope
    {
        return new \Illuminate\Mail\Envelope(subject: "Inquiry Baru: {$this->inquiry->inquiry_number}");
    }

    public function content(): \Illuminate\Mail\Content
    {
        return new \Illuminate\Mail\Content(view: 'emails.inquiry-received');
    }
}
```

Buat `resources/views/emails/inquiry-received.blade.php`:
```blade
<h2>Inquiry Baru</h2>
<p><strong>Nomor:</strong> {{ $inquiry->inquiry_number }}</p>
<p><strong>Nama:</strong> {{ $inquiry->customer_name }}</p>
<p><strong>WA:</strong> {{ $inquiry->customer_phone }}</p>
<p><strong>Email:</strong> {{ $inquiry->customer_email }}</p>
<p><strong>Pesan:</strong> {{ $inquiry->message }}</p>
<h3>Items:</h3>
<ul>
@foreach($inquiry->items as $item)
<li>{{ $item->product->name }} ({{ $item->product->sku }}) - {{ $item->quantity }} unit</li>
@endforeach
</ul>
```

**Step 3**: Buat InquiryController
```bash
php artisan make:controller InquiryController
```

Edit:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Leaderhub\Core\Models\Inquiry;
use Leaderhub\Core\Services\InquiryService;
use App\Mail\InquiryReceived;

class InquiryController extends Controller
{
    public function index()
    {
        return view('inquiry.index');
    }

    public function store(Request $request)
    {
        // Rate limit: 5/IP/jam
        $key = 'inquiry:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', 'Terlalu banyak inquiry. Coba lagi nanti.');
        }
        RateLimiter::hit($key, 3600);

        // Honeypot
        if ($request->filled('website')) abort(422);

        $validated = $request->validate([
            'customer_name' => 'required|min:3',
            'customer_phone' => 'required|min:8',
            'customer_email' => 'nullable|email',
            'message' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $inquiry = InquiryService::create($validated, $validated['items']);
        Mail::to(\Leaderhub\Core\Models\Setting::get('email', 'contact'))->send(new InquiryReceived($inquiry));

        if ($request->input('channel') === 'wa') {
            $wa = \Leaderhub\Core\Models\Setting::get('whatsapp', 'contact');
            $msg = urlencode(InquiryService::buildWaMessage($inquiry));
            return redirect("https://wa.me/{$wa}?text={$msg}");
        }
        return view('inquiry.success', ['inquiry' => $inquiry]);
    }
}
```

**Step 4**: Buat `resources/views/inquiry/index.blade.php`
```blade
@extends('layouts.app')
@section('title', 'Daftar Minat Saya — LeaderHub')
@section('content')
<div class="max-w-[720px] mx-auto px-8 py-12">
    <h1 class="text-h1 font-bold tracking-tight mb-8">Daftar Minat Saya</h1>
    <div id="inquiry-list" class="space-y-3 mb-8"></div>
    <div id="inquiry-form" class="hidden">
        <form method="POST" action="{{ route('inquiry.store') }}" id="inquiry-submit-form">
            @csrf
            <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">
            <div class="space-y-4">
                <div><label class="form-label">Nama Lengkap *</label><input name="customer_name" class="form-input w-full" required></div>
                <div><label class="form-label">No. WhatsApp *</label><input name="customer_phone" class="form-input w-full" required></div>
                <div><label class="form-label">Email</label><input name="customer_email" type="email" class="form-input w-full"></div>
                <div><label class="form-label">Pesan</label><textarea name="message" class="form-input w-full" rows="3"></textarea></div>
            </div>
            <div id="items-input"></div>
            <div class="flex gap-3 mt-6">
                <button type="submit" name="channel" value="wa" class="btn btn-primary">Kirim via WhatsApp</button>
                <button type="submit" name="channel" value="email" class="btn btn-secondary">Kirim via Email</button>
            </div>
        </form>
    </div>
    <div id="empty-state" class="text-center py-20 text-text-muted hidden">Belum ada produk di daftar minat. <a href="{{ route('products.index') }}" class="text-accent">Browse katalog</a></div>
</div>

@push('scripts')
<script type="module">
import { getCart, removeItem, updateQty, clearCart } from './inquiry.js';

const cart = getCart();
const listEl = document.getElementById('inquiry-list');
const formEl = document.getElementById('inquiry-form');
const emptyEl = document.getElementById('empty-state');
const itemsInput = document.getElementById('items-input');

if (cart.items.length === 0) {
    emptyEl.classList.remove('hidden');
} else {
    formEl.classList.remove('hidden');
    cart.items.forEach(item => {
        listEl.insertAdjacentHTML('beforeend', `
            <div class="flex items-center justify-between border border-border rounded-lg p-4">
                <div><div class="font-medium">${item.name}</div><div class="text-sm text-text-muted">Qty: <input type="number" min="1" value="${item.qty}" data-id="${item.product_id}" class="w-16 form-input qty-input"></div></div>
                <button data-id="${item.product_id}" class="text-accent text-sm remove-btn">Hapus</button>
            </div>
        `);
        itemsInput.insertAdjacentHTML('beforeend', `<input type="hidden" name="items[${item.product_id}][product_id]" value="${item.product_id}"><input type="hidden" name="items[${item.product_id}][qty]" value="${item.qty}" data-qty-id="${item.product_id}">`);
    });
}

document.querySelectorAll('.remove-btn').forEach(btn => btn.addEventListener('click', () => { removeItem(parseInt(btn.dataset.id)); location.reload(); }));
document.querySelectorAll('.qty-input').forEach(input => input.addEventListener('change', () => {
    updateQty(parseInt(input.dataset.id), parseInt(input.value));
    document.querySelector(`[data-qty-id="${input.dataset.id}"]`).value = input.value;
}));
</script>
@endpush
@endsection
```

**Step 5**: Routes
```php
Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiry.index');
Route::post('/inquiry', [InquiryController::class, 'store'])->name('inquiry.store');
```

### Validasi P3-T05
- [ ] Form tampil saat ada item di cart
- [ ] Submit via WA → redirect ke wa.me dengan message benar
- [ ] Submit via Email → success page tampil
- [ ] Inquiry tersimpan di DB dengan nomor INQ-YYYY-XXXXX
- [ ] Email terkirim ke admin
- [ ] Rate limit 5/jam berfungsi (test submit 6x → error)
- [ ] Honeypot berfungsi (isi field `website` → reject)

---

## Task P3-T06: Demo content (blog + inquiry)

### Step-by-Step

```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/seeders/BlogInquirySeeder.php << 'PHP'
<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\BlogCategory;
use Leaderhub\Core\Models\BlogPost;
use Leaderhub\Core\Models\BlogTag;
use Leaderhub\Core\Models\Inquiry;
use Leaderhub\Core\Models\InquiryItem;
use Leaderhub\Core\Models\Product;
use Leaderhub\Core\Models\User;

class BlogInquirySeeder extends Seeder
{
    public function run(): void
    {
        $cat = BlogCategory::updateOrCreate(['slug' => 'tips'], ['name' => 'Tips']);
        $tag = BlogTag::updateOrCreate(['slug' => 'strength'], ['name' => 'Strength']);
        $author = User::first();

        for ($i = 1; $i <= 5; $i++) {
            BlogPost::updateOrCreate(
                ['slug' => "post-{$i}"],
                [
                    'title' => "Tips Fitness #{$i}",
                    'blog_category_id' => $cat->id,
                    'author_id' => $author->id,
                    'content' => '<p>Konten artikel tips fitness nomor ' . $i . '...</p>',
                    'status' => 'published',
                    'published_at' => now()->subDays($i),
                    'reading_time' => 5,
                ]
            )->tags()->sync([$tag->id]);
        }

        $product = Product::first();
        if ($product) {
            $inquiry = Inquiry::updateOrCreate(
                ['inquiry_number' => 'INQ-' . now()->year . '-00001'],
                [
                    'uuid' => \Str::uuid(),
                    'customer_name' => 'John Doe',
                    'customer_phone' => '081234567890',
                    'customer_email' => 'john@example.com',
                    'message' => 'Saya tertarik dengan produk ini',
                    'status' => 'new',
                ]
            );
            InquiryItem::updateOrCreate(
                ['inquiry_id' => $inquiry->id, 'product_id' => $product->id],
                ['quantity' => 1]
            );
        }
    }
}
PHP
```

```bash
php artisan db:seed --class=Leaderhub\\Core\\Database\\Seeders\\BlogInquirySeeder
```

### Validasi P3-T06
- [ ] 5 blog post tampil di `/blog`
- [ ] 1 inquiry tampil di `/admin/inquiries`

---

## Checklist Akhir Phase 3

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan migrate:status | grep Pending && echo "✗ Pending" || echo "✓ Migrations OK"
php artisan db:seed --class=Leaderhub\\Core\\Database\\Seeders\\BlogInquirySeeder --force
php artisan serve --port=8000 &
sleep 2

curl -s http://localhost:8000/blog | grep -q "Tips Fitness" && echo "✓ Blog OK" || echo "✗ Blog FAIL"
curl -s http://localhost:8000/blog/post-1 | grep -q "Article" && echo "✓ Blog detail OK" || echo "✗ FAIL"
curl -s http://localhost:8000/inquiry | grep -q "Daftar Minat" && echo "✓ Inquiry page OK" || echo "✗ FAIL"

# Test inquiry submit
curl -s -X POST http://localhost:8000/inquiry -H "X-CSRF-TOKEN: $(curl -s http://localhost:8000/inquiry -c /tmp/c.txt | grep csrf | sed 's/.*content="\([^"]*\)".*/\1/')" -b /tmp/c.txt -d "customer_name=Test&customer_phone=08123456&items[1][product_id]=1&items[1][qty]=1&channel=email" | grep -q "success\|INQ-" && echo "✓ Inquiry submit OK" || echo "✗ FAIL"

php artisan tinker --execute="
use Leaderhub\Core\Models\Inquiry;
echo 'Inquiries: ' . Inquiry::count() . PHP_EOL;
echo 'Latest: ' . Inquiry::latest()->first()->inquiry_number . PHP_EOL;
"

kill %1
cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 3: Blog & inquiry system"
```

**Jika semua ✓ → lanjut Phase 4.**