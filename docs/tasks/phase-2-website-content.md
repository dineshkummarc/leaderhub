# Phase 2 — Website Content (Detail Task Step-by-Step)

> **Estimasi**: 2 minggu | **Tujuan**: Homepage, product catalog, gallery, image optimization, SEO
> **Prasyarat**: Phase 1 selesai (layout, design system, settings, auth, pages jalan)

## Validasi Akhir Phase 2
1. Homepage load dengan hero (Swiper), featured products, shop by category, latest stories, CTA
2. `/products` listing dengan filter, search, sort, pagination
3. `/products/{slug}` detail dengan gallery, specs, related, WA button, add to inquiry
4. `/gallery` masonry + lightbox
5. Image WebP + lazy load + srcset
6. SEO meta + JSON-LD + sitemap.xml + robots.txt
7. Admin CRUD: Product, Category, Brand, Gallery, Banner
8. Tidak ada error, semua FK rapi

---

## Task P2-T01: Migrations — products, images, specs, warehouses, stock, gallery, banners

### Step-by-Step

**Step 1**: Migration products
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_02_000001_create_products_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->string('material')->nullable();
            $table->longText('description')->nullable();
            $table->longText('description_en')->nullable();
            $table->decimal('purchase_price_usd', 12, 2)->default(0);
            $table->decimal('sale_price_idr', 15, 2)->default(0);
            $table->integer('warranty_period_months')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index(['category_id', 'status']);
            $table->index(['status', 'is_featured']);
            $table->fullText(['name', 'sku']);
        });
    }
    public function down(): void { Schema::dropIfExists('products'); }
};
PHP
```

**Step 2**: Migration product_images, product_specs
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_02_000002_create_product_images_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->index('product_id');
        });
        Schema::create('product_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('value');
            $table->integer('sort_order')->default(0);
            $table->index('product_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_specs');
        Schema::dropIfExists('product_images');
    }
};
PHP
```

**Step 3**: Migration warehouses, stock_levels, stock_movements
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_02_000003_create_warehouses_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('stock_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamps();
            $table->unique(['product_id', 'warehouse_id']);
        });
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // in, out, transfer
            $table->integer('quantity');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'warehouse_id']);
            $table->index('created_at');
        });
    }
    public function down(): void {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_levels');
        Schema::dropIfExists('warehouses');
    }
};
PHP
```

**Step 4**: Migration gallery_albums, gallery_items, banners
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_02_000004_create_gallery_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery_albums', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('gallery_albums')->cascadeOnDelete();
            $table->string('path');
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
            $table->index('album_id');
        });
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('cta_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('banners');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('gallery_albums');
    }
};
PHP
```

**Step 5**: Buat Models di core
```bash
cd /run/media/salmon/data/Projects/leaderhub/core/src/Models

cat > Product.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope('published', fn (Builder $q) => $q->where('status', 'published'));
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function images(): HasMany { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function specs(): HasMany { return $this->hasMany(ProductSpec::class)->orderBy('sort_order'); }
    public function stockLevels(): HasMany { return $this->hasMany(StockLevel::class); }

    public function scopeFeatured(Builder $q): Builder { return $q->where('is_featured', true); }

    public function getFeaturedImageAttribute(): ?string
    {
        return $this->images->first()?->path;
    }

    public function getTotalStockAttribute(): int
    {
        return $this->stockLevels->sum('quantity');
    }
}
PHP

cat > ProductImage.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $guarded = [];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
PHP

cat > ProductSpec.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpec extends Model
{
    protected $guarded = [];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
PHP

cat > Warehouse.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $guarded = [];
    public function stockLevels(): HasMany { return $this->hasMany(StockLevel::class); }
}
PHP

cat > StockLevel.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLevel extends Model
{
    protected $guarded = [];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
}
PHP

cat > StockMovement.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $guarded = [];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
}
PHP

cat > GalleryAlbum.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryAlbum extends Model
{
    protected $guarded = [];
    public function items(): HasMany { return $this->hasMany(GalleryItem::class)->orderBy('sort_order'); }
}
PHP

cat > GalleryItem.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    protected $guarded = [];
    public function album(): BelongsTo { return $this->belongsTo(GalleryAlbum::class); }
}
PHP

cat > Banner.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];
}
PHP
```

**Step 6**: Jalankan migration
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan migrate --path=../../core/database/migrations --force
```

### Validasi P2-T01
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Tabel `products`, `product_images`, `product_specs`, `warehouses`, `stock_levels`, `stock_movements`, `gallery_albums`, `gallery_items`, `banners` ada
- [ ] FK `products.category_id` → `categories.id` (cascade)
- [ ] FK `products.brand_id` → `brands.id` (null on delete)
- [ ] FK `product_images.product_id` → `products.id` (cascade)
- [ ] Unique `stock_levels(product_id, warehouse_id)`
- [ ] `php artisan tinker` → `Leaderhub\Core\Models\Product::all()` berfungsi

---

## Task P2-T02: Filament resources — Product, Category, Brand, Gallery, Banner

### Step-by-Step

**Step 1**: Buat resources
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan make:filament-resource Product
php artisan make:filament-resource Category
php artisan make:filament-resource Brand
php artisan make:filament-resource GalleryAlbum
php artisan make:filament-resource GalleryItem
php artisan make:filament-resource Banner
```

**Step 2**: Edit `app/Filament/Resources/ProductResource.php`
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Leaderhub\Core\Models\Product;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Website';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic')->schema([
                Forms\Components\TextInput::make('name')->required()->live()->afterStateUpdated(fn ($state, $set) => $set('slug', str($state)->slug())),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('sku')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('barcode'),
                Forms\Components\Select::make('category_id')->relationship('category', 'name')->required()->createOptionForm([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('slug')->required(),
                ]),
                Forms\Components\Select::make('brand_id')->relationship('brand', 'name')->createOptionForm([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('slug')->required(),
                ]),
                Forms\Components\TextInput::make('material'),
            ])->columns(2),
            Forms\Components\Section::make('Pricing')->schema([
                Forms\Components\TextInput::make('purchase_price_usd')->numeric()->prefix('USD'),
                Forms\Components\TextInput::make('sale_price_idr')->numeric()->prefix('IDR'),
                Forms\Components\TextInput::make('warranty_period_months')->numeric()->suffix('months'),
            ])->columns(3),
            Forms\Components\Section::make('Description')->schema([
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Flags')->schema([
                Forms\Components\Toggle::make('is_featured')->label('Featured'),
                Forms\Components\Toggle::make('is_new')->label('New'),
                Forms\Components\Toggle::make('is_best_seller')->label('Best Seller'),
                Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])->required()->default('draft'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(3),
            Forms\Components\Section::make('Images')->schema([
                Forms\Components\Repeater::make('images')->relationship()->schema([
                    Forms\Components\FileUpload::make('path')->image()->directory('products')->required(),
                    Forms\Components\TextInput::make('alt'),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])->orderable('sort_order')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Specs')->schema([
                Forms\Components\Repeater::make('specs')->relationship()->schema([
                    Forms\Components\TextInput::make('label')->required(),
                    Forms\Components\TextInput::make('value')->required(),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])->orderable('sort_order')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('sku')->searchable(),
            Tables\Columns\TextColumn::make('category.name')->sortable(),
            Tables\Columns\TextColumn::make('sale_price_idr')->money('IDR')->sortable(),
            Tables\Columns\IconColumn::make('is_featured')->boolean(),
            Tables\Columns\TextColumn::make('status')->badge()->colors(['draft' => 'gray', 'published' => 'success', 'archived' => 'warning']),
        ])->filters([
            Tables\Filters\SelectFilter::make('category_id')->relationship('category', 'name'),
            Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
        ])->actions([Tables\Actions\EditAction::make()])->bulkActions([Tables\Actions\BulkDeleteGroup::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

**Step 3**: Edit `CategoryResource.php`, `BrandResource.php`, `GalleryAlbumResource.php`, `GalleryItemResource.php`, `BannerResource.php` — pattern sama, schema sesuai field tabel. (Lihat dokumen schema untuk field lengkap.)

Contoh `BannerResource.php`:
```php
public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\TextInput::make('title')->required(),
        Forms\Components\Textarea::make('subtitle'),
        Forms\Components\FileUpload::make('image')->image()->directory('banners')->required(),
        Forms\Components\TextInput::make('link'),
        Forms\Components\TextInput::make('cta_text'),
        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        Forms\Components\Toggle::make('is_active')->default(true),
    ]);
}
```

### Validasi P2-T02
- [ ] `/admin/products` list tampil
- [ ] Create product dengan images + specs berhasil
- [ ] Category, Brand, Gallery, Banner CRUD berfungsi
- [ ] File upload tersimpan di `storage/app/public/products/`

---

## Task P2-T03: Homepage (hero, featured, category, blog, CTA)

### Step-by-Step

**Step 1**: Buat HomeController
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan make:controller HomeController
```

Edit `app/Http/Controllers/HomeController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Leaderhub\Core\Models\Banner;
use Leaderhub\Core\Models\Category;
use Leaderhub\Core\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember('homepage:data', 3600, function () {
            return [
                'banners' => Banner::where('is_active', true)->orderBy('sort_order')->get(),
                'featuredProducts' => Product::featured()->limit(8)->get(),
                'featuredCategories' => Category::where('is_featured', true)->orderBy('sort_order')->get(),
            ];
        });
        return view('home', $data);
    }
}
```

**Step 2**: Buat view `resources/views/home.blade.php`
```blade
@extends('layouts.app')

@section('content')
<!-- Hero -->
<section class="relative h-[80vh] min-h-[480px] overflow-hidden">
    <div class="swiper hero-swiper absolute inset-0">
        <div class="swiper-wrapper">
            @foreach($banners as $banner)
            <div class="swiper-slide relative">
                <img src="{{ \Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/40 to-transparent"></div>
                <div class="relative max-w-[1120px] mx-auto px-8 h-full flex flex-col justify-end pb-20">
                    <h1 class="text-display font-bold text-white tracking-tight max-w-2xl">{{ $banner->title }}</h1>
                    @if($banner->subtitle)<p class="text-lg text-slate-300 mt-3 max-w-lg">{{ $banner->subtitle }}</p>@endif
                    @if($banner->cta_text)<a href="{{ $banner->link ?? '#' }}" class="btn btn-primary btn-lg mt-6 w-fit">{{ $banner->cta_text }}</a>@endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- Shop by Category -->
<section class="max-w-[1120px] mx-auto px-8 py-20">
    <div class="eyebrow text-accent text-xs font-semibold mb-2">Kategori</div>
    <h2 class="text-h2 font-bold tracking-tight mb-8">Shop by Category</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($featuredCategories as $category)
        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group block rounded-lg overflow-hidden border border-border hover:shadow-md transition">
            <div class="aspect-square bg-surface-2">
                @if($category->image)<img src="{{ \Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover group-hover:scale-105 transition">@endif
            </div>
            <div class="p-4"><h3 class="font-semibold">{{ $category->name }}</h3></div>
        </a>
        @endforeach
    </div>
</section>

<!-- Featured Products -->
<section class="max-w-[1120px] mx-auto px-8 py-12">
    <div class="flex items-end justify-between mb-8">
        <div>
            <div class="eyebrow text-accent text-xs font-semibold mb-2">Produk</div>
            <h2 class="text-h2 font-bold tracking-tight">Featured Products</h2>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Lihat semua</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach($featuredProducts as $product)
        @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>

<!-- CTA -->
<section class="max-w-[1120px] mx-auto px-8 py-20">
    <div class="rounded-lg bg-accent-soft border border-accent/20 p-10 text-center">
        <h2 class="text-h2 font-bold tracking-tight mb-3">Butuh konsultasi alat fitness?</h2>
        <p class="text-text-muted mb-6">Tim kami siap membantu menemukan solusi terbaik untuk kebutuhan Anda.</p>
        <a href="https://wa.me/{{ \Leaderhub\Core\Models\Setting::get('whatsapp', 'contact') }}" class="btn btn-primary btn-lg">Hubungi via WhatsApp</a>
    </div>
</section>
@endsection

@push('scripts')
<script>
import Swiper from 'swiper';
import { Pagination, Autoplay } from 'swiper/modules';
document.addEventListener('DOMContentLoaded', () => {
    new Swiper('.hero-swiper', {
        modules: [Pagination, Autoplay],
        loop: true,
        autoplay: { delay: 6000 },
        pagination: { el: '.swiper-pagination' },
    });
});
</script>
@endpush
```

**Step 3**: Buat `resources/views/partials/product-card.blade.php`
```blade
@php $locale = session('locale', 'id'); @endphp
<a href="{{ route('products.show', $product->slug) }}" class="group block rounded-lg overflow-hidden border border-border bg-elevated hover:shadow-lg hover:-translate-y-0.5 transition">
    <div class="aspect-[4/5] bg-surface-2 relative">
        @if($product->featured_image)<img src="{{ \Storage::url($product->featured_image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">@endif
        @if($product->is_featured)<span class="absolute top-3 left-3 bg-accent text-white text-[11px] font-semibold px-2.5 py-1 rounded-full">Featured</span>@endif
        @elseif($product->is_new)<span class="absolute top-3 left-3 bg-slate-900 text-white text-[11px] font-semibold px-2.5 py-1 rounded-full">New</span>@endif
    </div>
    <div class="p-4">
        <div class="text-xs text-text-muted mb-1">{{ $product->category->name }}</div>
        <h3 class="font-semibold text-base mb-2 leading-snug">{{ $locale === 'en' && $product->name_en ? $product->name_en : $product->name }}</h3>
        <div class="font-bold tabular-nums"><span class="text-text-muted text-sm font-medium">Rp</span>{{ number_format($product->sale_price_idr, 0, ',', '.') }}</div>
    </div>
</a>
```

**Step 4**: Install Swiper
```bash
pnpm add swiper
```

**Step 5**: Route
```php
Route::get('/', [HomeController::class, 'index'])->name('home');
```

### Validasi P2-T03
- [ ] Homepage load dengan hero Swiper (autoplay)
- [ ] Featured products tampil (jika ada data)
- [   ] Shop by category tampil
- [ ] CTA WhatsApp link benar
- [ ] Tidak ada error

---

## Task P2-T04: Product listing page

### Step-by-Step

**Step 1**: Buat ProductController
```bash
php artisan make:controller ProductController
```

Edit `app/Http/Controllers/ProductController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Leaderhub\Core\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($cat = $request->get('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
        }
        if ($brand = $request->get('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $brand));
        }
        if ($search = $request->get('q')) {
            $query->whereFullText(['name', 'sku'], $search);
        }
        if ($min = $request->get('min_price')) {
            $query->where('sale_price_idr', '>=', $min);
        }
        if ($max = $request->get('max_price')) {
            $query->where('sale_price_idr', '<=', $max);
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('sale_price_idr', 'asc'),
            'price_desc' => $query->orderBy('sale_price_idr', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        return view('products.index', compact('products'));
    }

    public function show(string $slug)
    {
        $product = Product::withoutGlobalScope('published')->where('slug', $slug)->firstOrFail();
        $related = Product::where('category_id', $product->category_id)->where('id', '!=', $product->id)->limit(4)->get();
        return view('products.show', compact('product', 'related'));
    }
}
```

**Step 2**: Buat `resources/views/products/index.blade.php`
```blade
@extends('layouts.app')
@section('title', 'Produk — LeaderHub')
@section('content')
<div class="max-w-[1120px] mx-auto px-8 py-12">
    <nav class="text-sm text-text-muted mb-4"><a href="{{ route('home') }}" class="hover:text-text">Home</a> / <span>Produk</span></nav>
    <h1 class="text-h1 font-bold tracking-tight mb-8">Produk</h1>
    <div class="grid grid-cols-1 md:grid-cols-[240px_1fr] gap-8">
        <aside class="space-y-6">
            <form method="GET" id="filter-form">
                <div class="mb-4">
                    <label class="text-sm font-medium block mb-2">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="form-input w-full">
                </div>
                <div class="mb-4">
                    <label class="text-sm font-medium block mb-2">Harga</label>
                    <div class="flex gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="form-input w-full">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="form-input w-full">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-sm font-medium block mb-2">Sort</label>
                    <select name="sort" class="form-input w-full">
                        <option value="newest" @selected(request('sort','newest')==='newest')>Terbaru</option>
                        <option value="price_asc" @selected(request('sort')==='price_asc')>Harga Terendah</option>
                        <option value="price_desc" @selected(request('sort')==='price_desc')>Harga Tertinggi</option>
                        <option value="name" @selected(request('sort')==='name')>Nama A-Z</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-full">Terapkan</button>
            </form>
        </aside>
        <div>
            <div class="text-sm text-text-muted mb-4">{{ $products->total() }} produk ditemukan</div>
            @if($products->isEmpty())
                <div class="text-center py-20 text-text-muted">Tidak ada produk yang cocok.</div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
                @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
            {{ $products->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
```

**Step 3**: Buat `resources/views/products/show.blade.php`
```blade
@extends('layouts.app')
@section('title', $product->name . ' — LeaderHub')
@section('meta_description', str($product->description)->limit(160))

@section('content')
<div class="max-w-[1120px] mx-auto px-8 py-12">
    <nav class="text-sm text-text-muted mb-4">
        <a href="{{ route('home') }}" class="hover:text-text">Home</a> /
        <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-text">{{ $product->category->name }}</a> /
        <span>{{ $product->name }}</span>
    </nav>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
            @if($product->images->isNotEmpty())
            <div class="rounded-lg overflow-hidden border border-border mb-3">
                <img id="main-image" src="{{ \Storage::url($product->images->first()->path) }}" alt="{{ $product->name }}" class="w-full aspect-[4/5] object-cover">
            </div>
            @if($product->images->count() > 1)
            <div class="flex gap-2">
                @foreach($product->images as $img)
                <button onclick="document.getElementById('main-image').src='{{ \Storage::url($img->path) }}'" class="w-16 h-16 rounded border border-border overflow-hidden hover:border-accent transition">
                    <img src="{{ \Storage::url($img->path) }}" alt="{{ $img->alt }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
            @endif
        </div>
        <div>
            <div class="text-sm text-text-muted mb-1">{{ $product->category->name }}@if($product->brand) · {{ $product->brand->name }}@endif</div>
            <h1 class="text-h1 font-bold tracking-tight mb-2">{{ $product->name }}</h1>
            <div class="text-sm text-text-muted font-mono mb-4">SKU: {{ $product->sku }}</div>
            <div class="text-2xl font-bold tabular-nums mb-6"><span class="text-text-muted text-base font-medium">Rp</span>{{ number_format($product->sale_price_idr, 0, ',', '.') }}</div>
            @if($product->description)<div class="prose max-w-none text-text-muted mb-6">{!! $product->description !!}</div>@endif
            @if($product->specs->isNotEmpty())
            <div class="border border-border rounded-lg overflow-hidden mb-6">
                <table class="w-full text-sm">
                    @foreach($product->specs as $spec)
                    <tr class="border-b border-border last:border-0">
                        <td class="px-4 py-3 font-medium bg-surface w-1/3">{{ $spec->label }}</td>
                        <td class="px-4 py-3">{{ $spec->value }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
            <div class="flex gap-3">
                <button onclick="addToInquiry({{ $product->id }})" class="btn btn-primary">Tambah ke Daftar Minat</button>
                <a href="https://wa.me/{{ \Leaderhub\Core\Models\Setting::get('whatsapp', 'contact') }}?text={{ urlencode('Halo, saya tertarik dengan ' . $product->name . ' (SKU: ' . $product->sku . ')') }}" class="btn btn-outline">Hubungi via WA</a>
            </div>
        </div>
    </div>
    @if($related->isNotEmpty())
    <section class="mt-20">
        <h2 class="text-h3 font-bold tracking-tight mb-6">Produk Terkait</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach($related as $p)
            @include('partials.product-card', ['product' => $p])
            @endforeach
        </div>
    </section>
    @endif
</div>

@section('scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "sku": "{{ $product->sku }}",
  "brand": { "@type": "Brand", "name": "{{ $product->brand?->name }}" },
  "description": "{{ str($product->description)->stripTags()->limit(160) }}",
  "offers": { "@type": "Offer", "priceCurrency": "IDR", "price": "{{ $product->sale_price_idr }}", "availability": "https://schema.org/InStock" }
}
</script>
@endsection
@endsection
```

**Step 4**: Routes
```php
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
```

### Validasi P2-T04
- [ ] `/products` load, pagination 12/page
- [ ] Filter category, brand, price, search berfungsi
- [ ] Sort berfungsi
- [ ] `/products/{slug}` detail load
- [   ] Image gallery swap berfungsi
- [ ] Specs table tampil
- [ ] Related products tampil
- [ ] WA link benar
- [ ] JSON-LD Product ada di view source

---

## Task P2-T05: Gallery page

### Step-by-Step

**Step 1**: Buat GalleryController
```bash
php artisan make:controller GalleryController
```

Edit:
```php
<?php

namespace App\Http\Controllers;

use Leaderhub\Core\Models\GalleryAlbum;
use Leaderhub\Core\Models\GalleryItem;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::orderBy('sort_order')->get();
        $items = GalleryItem::with('album')->orderBy('sort_order')->get();
        return view('gallery', compact('albums', 'items'));
    }
}
```

**Step 2**: Buat `resources/views/gallery.blade.php`
```blade
@extends('layouts.app')
@section('title', 'Gallery — LeaderHub')
@section('content')
<div class="max-w-[1120px] mx-auto px-8 py-12">
    <h1 class="text-h1 font-bold tracking-tight mb-8">Gallery</h1>
    <div class="flex gap-2 mb-8 flex-wrap" id="album-filter">
        <button data-album="all" class="badge badge-neutral active px-3 py-1.5 rounded-full text-sm cursor-pointer">All</button>
        @foreach($albums as $album)
        <button data-album="{{ $album->id }}" class="badge badge-neutral px-3 py-1.5 rounded-full text-sm cursor-pointer">{{ $album->name }}</button>
        @endforeach
    </div>
    <div class="columns-1 md:columns-3 gap-5 [column-fill:_balance]">
        @foreach($items as $item)
        <a href="{{ \Storage::url($item->path) }}" class="glightbox block mb-5 break-inside-avoid" data-gallery="gallery" data-glightbox="title: {{ $item->caption }}; description: {{ $item->alt }}" data-album="{{ $item->album_id }}">
            <img src="{{ \Storage::url($item->path) }}" alt="{{ $item->alt }}" loading="lazy" class="w-full rounded-lg">
        </a>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lightbox = GLightbox({ selector: '.glightbox' });
    document.querySelectorAll('#album-filter button').forEach(btn => {
        btn.addEventListener('click', () => {
            const album = btn.dataset.album;
            document.querySelectorAll('#album-filter button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('[data-album]').forEach(el => {
                if (el.tagName === 'A') {
                    el.style.display = (album === 'all' || el.dataset.album === album) ? '' : 'none';
                }
            });
        });
    });
});
</script>
@endpush
@endsection
```

**Step 3**: Route
```php
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
```

### Validasi P2-T05
- [ ] `/gallery` load, masonry tampil
- [ ] Lightbox berfungsi (zoom, prev/next)
- [ ] Album filter berfungsi (client-side)
- [ ] Lazy load berfungsi

---

## Task P2-T06: Image optimization (WebP, lazy load, srcset)

### Step-by-Step

**Step 1**: Install Intervention Image
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
composer require intervention/image
```

**Step 2**: Buat ImageService di core
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/src/Services/ImageService.php << 'PHP'
<?php

namespace Leaderhub\Core\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageService
{
    public static function upload(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $filename = uniqid() . '.webp';
        $path = "{$directory}/{$filename}";

        $image = ImageManager::gd()->read($file);
        $image->encode(new \Intervention\Image\Encoders\WebpEncoder(quality: 80));
        Storage::disk($disk)->put($path, $image->toString());

        // Generate thumbnail
        $thumb = clone $image;
        $thumb->scale(width: 400);
        Storage::disk($disk)->put("{$directory}/thumb_{$filename}", $thumb->toString());

        return $path;
    }

    public static function srcset(string $path, array $widths = [400, 800, 1200]): string
    {
        $urls = [];
        foreach ($widths as $w) {
            $urls[] = asset("storage/{$path}") . " {$w}w";
        }
        return implode(', ', $urls);
    }
}
PHP
```

**Step 3**: Update Filament FileUpload untuk pakai ImageService (opsional, atau biarkan default + convert saat render)

**Step 4**: Tambah `loading="lazy"` di semua `<img>` (sudah di product card & gallery)

### Validasi P2-T06
- [ ] Upload image → file `.webp` tersimpan
- [ ] Thumbnail tersimpan
- [ ] Lazy load attribute ada di HTML

---

## Task P2-T07: SEO meta + sitemap + robots

### Step-by-Step

**Step 1**: Install spatie/sitemap
```bash
composer require spatie/laravel-sitemap
```

**Step 2**: Buat SitemapController
```bash
php artisan make:controller SitemapController
```

Edit:
```php
<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Leaderhub\Core\Models\Product;
use Leaderhub\Core\Models\Page;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home'))->setPriority(1.0))
            ->add(Url::create(route('products.index'))->setPriority(0.9))
            ->add(Url::create(route('gallery.index'))->setPriority(0.7));

        Product::all()->each(fn ($p) => $sitemap->add(Url::create(route('products.show', $p->slug))->setPriority(0.8)));
        Page::all()->each(fn ($pg) => $sitemap->add(Url::create(route('page.show', $pg->slug))->setPriority(0.6)));

        return $sitemap->render();
    }
}
```

**Step 3**: Route
```php
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
```

**Step 4**: Buat `public/robots.txt`
```
User-agent: *
Allow: /
Sitemap: https://leaderhub.id/sitemap.xml
```

**Step 5**: Tambah Organization JSON-LD di layout
```blade
@section('scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "LeaderHub",
  "url": "{{ config('app.url') }}",
  "logo": "{{ asset('storage/' . \Leaderhub\Core\Models\Setting::get('logo', 'general')) }}",
  "contactPoint": { "@type": "ContactPoint", "telephone": "{{ \Leaderhub\Core\Models\Setting::get('phone', 'contact') }}", "contactType": "sales" }
}
</script>
@endsection
```

### Validasi P2-T07
- [ ] `/sitemap.xml` accessible, berisi URL products & pages
- [ ] `/robots.txt` accessible
- [ ] JSON-LD Organization ada di view source
- [ ] JSON-LD Product ada di product detail

---

## Task P2-T08: Demo content seeder

### Step-by-Step

```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/seeders/DemoContentSeeder.php << 'PHP'
<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\Banner;
use Leaderhub\Core\Models\Brand;
use Leaderhub\Core\Models\Category;
use Leaderhub\Core\Models\Product;
use Leaderhub\Core\Models\ProductImage;
use Leaderhub\Core\Models\ProductSpec;
use Leaderhub\Core\Models\GalleryAlbum;
use Leaderhub\Core\Models\GalleryItem;
use Leaderhub\Core\Models\Warehouse;
use Leaderhub\Core\Models\StockLevel;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $cats = ['Cardio' => 'cardio', 'Strength' => 'strength', 'Machine' => 'machine'];
        $catIds = [];
        foreach ($cats as $name => $slug) {
            $c = Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'is_featured' => true, 'sort_order' => count($catIds)]);
            $catIds[$slug] = $c->id;
        }

        // Brands
        $brands = ['Technogym' => 'technogym', 'Life Fitness' => 'life-fitness', 'Hammer Strength' => 'hammer-strength'];
        $brandIds = [];
        foreach ($brands as $name => $slug) {
            $b = Brand::updateOrCreate(['slug' => $slug], ['name' => $name]);
            $brandIds[$slug] = $b->id;
        }

        // Warehouse
        $wh = Warehouse::updateOrCreate(['code' => 'WH01'], ['name' => 'Main Warehouse', 'is_active' => true]);

        // Products
        $products = [
            ['name' => 'Treadmill Pro X1', 'sku' => 'LDR-TREAD-X1', 'cat' => 'cardio', 'brand' => 'technogym', 'price' => 45000000, 'featured' => true],
            ['name' => 'Dumbbell Set 20kg', 'sku' => 'LDR-DUMB-20', 'cat' => 'strength', 'brand' => 'hammer-strength', 'price' => 2500000, 'new' => true],
            ['name' => 'Smith Machine', 'sku' => 'LDR-SMITH-01', 'cat' => 'machine', 'brand' => 'life-fitness', 'price' => 28000000],
            ['name' => 'Exercise Bike B2', 'sku' => 'LDR-BIKE-B2', 'cat' => 'cardio', 'brand' => 'technogym', 'price' => 15000000, 'featured' => true],
            ['name' => 'Power Rack R1', 'sku' => 'LDR-RACK-R1', 'cat' => 'strength', 'brand' => 'hammer-strength', 'price' => 18000000, 'best' => true],
        ];
        foreach ($products as $p) {
            $product = Product::updateOrCreate(
                ['sku' => $p['sku']],
                [
                    'name' => $p['name'],
                    'slug' => str($p['name'])->slug(),
                    'category_id' => $catIds[$p['cat']],
                    'brand_id' => $brandIds[$p['brand']] ?? null,
                    'sale_price_idr' => $p['price'],
                    'purchase_price_usd' => $p['price'] / 15800,
                    'is_featured' => $p['featured'] ?? false,
                    'is_new' => $p['new'] ?? false,
                    'is_best_seller' => $p['best'] ?? false,
                    'status' => 'published',
                    'description' => '<p>' . $p['name'] . ' adalah alat fitness premium untuk kebutuhan gym profesional.</p>',
                ]
            );
            StockLevel::updateOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $wh->id],
                ['quantity' => rand(0, 20)]
            );
            ProductSpec::updateOrCreate(['product_id' => $product->id, 'label' => 'Material'], ['value' => 'Steel']);
            ProductSpec::updateOrCreate(['product_id' => $product->id, 'label' => 'Warranty'], ['value' => '12 months']);
        }

        // Banners
        Banner::updateOrCreate(['title' => 'Built for Champions'], ['subtitle' => 'Distributor alat fitness premium', 'image' => 'banners/hero1.jpg', 'cta_text' => 'Jelajahi Katalog', 'link' => '/products', 'is_active' => true, 'sort_order' => 0]);

        // Gallery
        $album = GalleryAlbum::updateOrCreate(['slug' => 'showroom'], ['name' => 'Showroom']);
        // Items dummy (path placeholder)
    }
}
PHP
```

Register & seed:
```bash
php artisan db:seed --class=Leaderhub\\Core\\Database\\Seeders\\DemoContentSeeder
```

### Validasi P2-T08
- [ ] Seeder berhasil
- [ ] Homepage menampilkan featured products
- [ ] `/products` menampilkan 5 produk
- [ ] Product detail berfungsi

---

## Checklist Akhir Phase 2

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan migrate:status | grep Pending && echo "✗ Migration pending" || echo "✓ Migrations OK"
php artisan db:seed --force
php artisan optimize:clear
php artisan serve --port=8000 &
sleep 2

curl -s http://localhost:8000 | grep -q "hero-swiper" && echo "✓ Homepage OK" || echo "✗ Homepage FAIL"
curl -s http://localhost:8000/products | grep -q "produk ditemukan" && echo "✓ Products OK" || echo "✗ Products FAIL"
curl -s http://localhost:8000/products/treadmill-pro-x1 | grep -q "SKU" && echo "✓ Product detail OK" || echo "✗ FAIL"
curl -s http://localhost:8000/gallery | grep -q "glightbox" && echo "✓ Gallery OK" || echo "✗ Gallery FAIL"
curl -s http://localhost:8000/sitemap.xml | grep -q "<urlset" && echo "✓ Sitemap OK" || echo "✗ Sitemap FAIL"
curl -s http://localhost:8000/robots.txt | grep -q "Sitemap" && echo "✓ Robots OK" || echo "✗ Robots FAIL"

# FK check
php artisan tinker --execute="
use Leaderhub\Core\Models\Product;
echo 'Products: ' . Product::count() . PHP_EOL;
echo 'Featured: ' . Product::featured()->count() . PHP_EOL;
"

kill %1
cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 2: Website content — homepage, catalog, gallery, SEO"
```

**Jika semua ✓ → lanjut Phase 3.**