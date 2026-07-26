# Phase 1 — Website Foundation (Detail Task Step-by-Step)

> **Estimasi**: 2 minggu | **Tujuan**: Layout website, design system, settings, auth admin, static pages
> **Prasyarat**: Phase 0 selesai (monorepo + 3 app jalan, core accessible, design tokens built)

## Validasi Akhir Phase 1
1. Homepage load dengan layout Modern Premium (header, footer, dark/light toggle)
2. `/admin/login` berfungsi (Filament auth)
3. Settings CRUD berfungsi (admin bisa edit site name, contact, seo)
4. Static pages (About, Contact, Terms, Privacy) load dari DB
5. Dark mode toggle persist (localStorage)
6. Language toggle ID/EN berfungsi (session)
7. Migrations users, settings, pages, translations, categories, brands jalan tanpa error
8. Seeder admin user + default settings berhasil
9. Tidak ada error PHP, semua relasi FK rapi

---

## Task P1-T01: Setup Tailwind 4 + design tokens

### Tujuan
Tailwind 4 terkonfigurasi dengan design tokens LeaderHub.

### Step-by-Step

**Step 1**: Install Tailwind 4 + plugin Vite (jika belum di P0)
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
pnpm add -D tailwindcss @tailwindcss/vite
```

**Step 2**: Edit `vite.config.js`
```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@': '/resources/js',
    },
  },
});
```

**Step 3**: Edit `resources/css/app.css`
```css
@import "../../packages/design-tokens/dist/tokens.css";
@import "tailwindcss";

@theme {
  --font-sans: 'Inter', system-ui, sans-serif;
  --font-mono: ui-monospace, 'SF Mono', Menlo, monospace;
  --color-bg: var(--color-bg);
  --color-surface: var(--color-surface);
  --color-surface-2: var(--color-surface-2);
  --color-elevated: var(--color-elevated);
  --color-text: var(--color-text);
  --color-text-muted: var(--color-text-muted);
  --color-text-subtle: var(--color-text-subtle);
  --color-border: var(--color-border);
  --color-border-strong: var(--color-border-strong);
  --color-accent: var(--color-accent);
  --color-accent-hover: var(--color-accent-hover);
  --color-accent-soft: var(--color-accent-soft);
  --color-success: var(--color-success);
  --color-warning: var(--color-warning);
  --color-info: var(--color-info);
  --radius-sm: 6px;
  --radius-DEFAULT: 10px;
  --radius-lg: 14px;
  --radius-xl: 18px;
}

html { font-family: var(--font-sans); -webkit-font-smoothing: antialiased; }
body { background: var(--color-bg); color: var(--color-text); transition: background 200ms, color 200ms; }
```

**Step 4**: Tambahkan Google Fonts di layout (Step T03)
**Step 5**: Build & test
```bash
pnpm run build
# Pastikan public/build/ terisi, tidak error
```

### Validasi P1-T01
- [ ] `pnpm run build` berhasil tanpa error
- [ ] `public/build/` ada file CSS
- [ ] CSS variables `--color-bg`, `--color-accent` ter-load di browser (inspect)

---

## Task P1-T02: Migrations — users, settings, pages, translations, categories, brands

### Tujuan
Buat migrations di `core/database/migrations/` untuk tabel fondasi.

### Step-by-Step

**Step 1**: Migration users (extend default Laravel users)
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_01_000001_create_users_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('content_admin');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
PHP
```

**Step 2**: Migration settings
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_01_000002_create_settings_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, boolean, number, json, image
            $table->boolean('is_translatable')->default(false);
            $table->timestamps();
            $table->unique(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
PHP
```

**Step 3**: Migration pages
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_01_000003_create_pages_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('title_en')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_image')->nullable();
            $table->string('status')->default('published');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
PHP
```

**Step 4**: Migration translations
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_01_000004_create_translations_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 5);
            $table->string('namespace', 50)->default('website');
            $table->string('key');
            $table->text('value');
            $table->timestamps();
            $table->unique(['locale', 'namespace', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
PHP
```

**Step 5**: Migration categories
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_01_000005_create_categories_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->index('parent_id');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
PHP
```

**Step 6**: Migration brands
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/migrations/0002_01_01_000006_create_brands_table.php << 'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
PHP
```

**Step 7**: Buat Models di core
```bash
cd /run/media/salmon/data/Projects/leaderhub/core/src/Models

# User.php
cat > User.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'last_login_at' => 'datetime'];

    public function isActive(): bool
    {
        return $this->is_active;
    }
}
PHP

# Setting.php
cat > Setting.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];
    protected $casts = ['is_translatable' => 'boolean'];

    public static function get(string $key, ?string $group = null, mixed $default = null): mixed
    {
        $query = static::where('key', $key);
        if ($group) $query->where('group', $group);
        $setting = $query->first();
        return $setting?->value ?? $default;
    }

    public static function set(string $key, mixed $value, ?string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key, 'group' => $group],
            ['value' => $value]
        );
    }
}
PHP

# Page.php
cat > Page.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Page extends Model
{
    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope('published', fn (Builder $q) => $q->where('status', 'published'));
    }

    public function resolveRouteBinding($value, $field = null): \Illuminate\Database\Eloquent\Model
    {
        return static::withoutGlobalScope('published')->where('slug', $value)->firstOrFail();
    }
}
PHP

# Translation.php
cat > Translation.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $guarded = [];

    public static function t(string $locale, string $namespace, string $key, ?string $default = null): ?string
    {
        $t = static::where('locale', $locale)->where('namespace', $namespace)->where('key', $key)->first();
        return $t?->value ?? $default;
    }
}
PHP

# Category.php
cat > Category.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = [];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
PHP

# Brand.php
cat > Brand.php << 'PHP'
<?php

namespace Leaderhub\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $guarded = [];
}
PHP
```

**Step 8**: Jalankan migration dari website
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan migrate --path=../../core/database/migrations --force
```

### Validasi P1-T02
- [ ] `php artisan migrate` berhasil tanpa error
- [ ] Tabel `users`, `settings`, `pages`, `translations`, `categories`, `brands` ada di DB
- [ ] `php artisan tinker` → `Leaderhub\Core\Models\Setting::get('site_name')` berfungsi (return null dulu)
- [ ] FK `categories.parent_id` → `categories.id` ada (self-referencing)

---

## Task P1-T03: Base layout & partials

### Tujuan
Layout Modern Premium dengan header (nav + lang toggle + dark mode + inquiry count) & footer.

### Step-by-Step

**Step 1**: Buat layout utama `resources/views/layouts/app.blade.php`
```blade
<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}" class="{{ session('theme', 'light') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('meta_description', \Leaderhub\Core\Models\Setting::get('seo_description', 'seo'))">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="bg-bg text-text min-h-screen flex flex-col">
    @include('partials.header')
    <main class="flex-1">
        @yield('content')
    </main>
    @include('partials.footer')
    @include('partials.inquiry-bar')
</body>
</html>
```

**Step 2**: Buat `resources/views/partials/header.blade.php`
```blade
<header class="sticky top-0 z-200 h-16 border-b border-border bg-bg/85 backdrop-blur-xl">
    <div class="max-w-[1120px] mx-auto px-8 h-full flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-[1.0625rem] tracking-tight">
            <span class="w-[22px] h-[22px] rounded-[6px] bg-accent"></span>
            <span>LeaderHub</span>
        </a>
        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-text-muted">
            <a href="{{ route('products.index') }}" class="hover:text-text transition">Produk</a>
            <a href="{{ route('gallery.index') }}" class="hover:text-text transition">Gallery</a>
            <a href="{{ route('blog.index') }}" class="hover:text-text transition">Blog</a>
            <a href="{{ route('page.show', 'about') }}" class="hover:text-text transition">Tentang</a>
            <a href="{{ route('page.show', 'contact') }}" class="hover:text-text transition">Kontak</a>
        </nav>
        <div class="flex items-center gap-2">
            <button onclick="toggleTheme()" class="p-2 rounded-[10px] border border-border hover:bg-surface transition" aria-label="Toggle theme">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            <button onclick="toggleLang()" class="px-3 py-2 rounded-[10px] border border-border text-sm font-medium hover:bg-surface transition">
                {{ session('locale', 'id') === 'id' ? 'EN' : 'ID' }}
            </button>
            <a href="{{ route('inquiry.index') }}" class="relative p-2 rounded-[10px] border border-border hover:bg-surface transition" aria-label="Inquiry cart">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span id="inquiry-count" class="absolute -top-1 -right-1 bg-accent text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center hidden">0</span>
            </a>
        </div>
    </div>
</header>
```

**Step 3**: Buat `resources/views/partials/footer.blade.php`
```blade
<footer class="border-t border-border bg-surface mt-20">
    <div class="max-w-[1120px] mx-auto px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <div class="flex items-center gap-2 font-bold mb-3">
                <span class="w-[22px] h-[22px] rounded-[6px] bg-accent"></span>
                <span>LeaderHub</span>
            </div>
            <p class="text-sm text-text-muted">{{ \Leaderhub\Core\Models\Setting::get('tagline', 'general') ?? 'Distributor alat fitness premium' }}</p>
        </div>
        <div>
            <h4 class="font-semibold text-sm mb-3">Navigasi</h4>
            <ul class="space-y-2 text-sm text-text-muted">
                <li><a href="{{ route('products.index') }}" class="hover:text-text">Produk</a></li>
                <li><a href="{{ route('gallery.index') }}" class="hover:text-text">Gallery</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-text">Blog</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold text-sm mb-3">Perusahaan</h4>
            <ul class="space-y-2 text-sm text-text-muted">
                <li><a href="{{ route('page.show', 'about') }}" class="hover:text-text">Tentang</a></li>
                <li><a href="{{ route('page.show', 'contact') }}" class="hover:text-text">Kontak</a></li>
            </ul>
        </div>
        <div>
            <h4 class="font-semibold text-sm mb-3">Kontak</h4>
            <ul class="space-y-2 text-sm text-text-muted">
                <li>{{ \Leaderhub\Core\Models\Setting::get('email', 'contact') ?? 'info@leaderhub.id' }}</li>
                <li>{{ \Leaderhub\Core\Models\Setting::get('phone', 'contact') ?? '+62 21 1234 5678' }}</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-border py-4 text-center text-xs text-text-subtle">
        © {{ date('Y') }} LeaderHub. All rights reserved.
    </div>
</footer>
```

**Step 4**: Buat `resources/views/partials/inquiry-bar.blade.php` (placeholder, detail di Phase 3)
```blade
<div id="inquiry-bar" class="fixed bottom-0 left-0 right-0 z-300 bg-elevated border-t border-border shadow-md hidden">
    <div class="max-w-[1120px] mx-auto px-8 py-3 flex items-center justify-between">
        <span class="text-sm"><span id="inquiry-bar-count" class="font-bold text-accent">0</span> produk di daftar minat</span>
        <a href="{{ route('inquiry.index') }}" class="btn btn-primary text-sm">Lihat Daftar</a>
    </div>
</div>
```

**Step 5**: Buat `resources/js/app.js` dengan toggle logic
```js
import './bootstrap';

window.toggleTheme = function() {
    const html = document.documentElement;
    const isDark = html.classList.toggle('dark');
    fetch('/api/theme', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
};

window.toggleLang = function() {
    fetch('/api/lang', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
        .then(() => location.reload());
};
```

### Validasi P1-T03
- [ ] Homepage (dummy) load dengan header & footer
- [ ] Logo, nav, toggle button tampil
- [ ] Tidak ada error Blade/PHP

---

## Task P1-T04: Dark mode & language toggle

### Tujuan
Toggle dark/light persist via session, toggle ID/EN via session.

### Step-by-Step

**Step 1**: Buat middleware `SetLocale`
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan make:middleware SetLocale
```

Edit `app/Http/Middleware/SetLocale.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        app()->setLocale(session('locale', 'id'));
        return $next($request);
    }
}
```

**Step 2**: Register middleware di `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
    ]);
})
```

**Step 3**: Buat controller untuk toggle
```bash
php artisan make:controller ThemeController
```

Edit `app/Http/Controllers/ThemeController.php`:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function toggleTheme(Request $request)
    {
        session(['theme' => session('theme', 'light') === 'dark' ? 'light' : 'dark']);
        return response()->json(['theme' => session('theme')]);
    }

    public function toggleLang(Request $request)
    {
        session(['locale' => session('locale', 'id') === 'id' ? 'en' : 'id']);
        return response()->json(['locale' => session('locale')]);
    }
}
```

**Step 4**: Tambah routes di `routes/web.php`
```php
use App\Http\Controllers\ThemeController;

Route::post('/api/theme', [ThemeController::class, 'toggleTheme']);
Route::post('/api/lang', [ThemeController::class, 'toggleLang']);
```

**Step 5**: Test
```bash
# Toggle dark
curl -X POST http://localhost:8000/api/theme -H "X-CSRF-TOKEN: ..." -b cookie.txt
# Response: {"theme":"dark"}
```

### Validasi P1-T04
- [ ] Klik toggle theme → html dapat class `dark`, persist setelah refresh
- [ ] Klik toggle lang → session `locale` berubah, reload page
- [ ] Tidak ada error 419 (CSRF)

---

## Task P1-T05: Admin auth (Filament panel website)

### Tujuan
Filament panel untuk content_admin, install Shield, setup roles.

### Step-by-Step

**Step 1**: Install Shield (jika belum)
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
composer require bezhansalleh/filament-shield:"^5.0"
php artisan shield:install
```

**Step 2**: Edit `app/Providers/Filament/AdminPanelProvider.php`
```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        ->brandName('LeaderHub Admin')
        ->font('Inter')
        ->colors([
            'primary' => '#DC2626',
            'danger'  => '#DC2626',
            'success' => '#16A34A',
            'warning' => '#D97706',
            'info'    => '#2563EB',
            'gray'    => '#64748B',
        ])
        ->renderHook('panels.head', fn () => view('filament.theme-styles'))
        ->login()
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
        ->middleware([
            \Filament\Http\Middleware\DisableBladeIconComponents::class,
            \Filament\Http\Middleware\DispatchServingFilamentEvents::class,
        ])
        ->authMiddleware([
            \Filament\Http\Middleware\Authenticate::class,
        ]);
}
```

**Step 3**: Buat `resources/views/filament/theme-styles.blade.php`
```blade
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root { --color-accent: #DC2626; --font-sans: 'Inter', system-ui, sans-serif; }
  html.dark { --color-accent: #EF4444; }
  body { font-family: var(--font-sans); -webkit-font-smoothing: antialiased; }
  .fi-sidebar-brand h1 { font-weight: 700; letter-spacing: -0.02em; }
  .fi-btn-primary { border-radius: 10px; font-weight: 600; }
  .fi-card { border-radius: 14px; }
  .fi-table th { font-weight: 600; font-size: 12px; color: #64748B; text-transform: none; }
  .fi-input, .fi-select { border-radius: 10px; }
  .fi-input:focus, .fi-select:focus { box-shadow: 0 0 0 3px rgba(220,38,38,0.12); }
</style>
```

**Step 4**: Generate Shield roles & policies
```bash
php artisan shield:generate --all
```

### Validasi P1-T05
- [ ] `/admin/login` accessible
- [ ] Login dengan user (dari seeder T07) berhasil
- [ ] Dashboard Filament tampil dengan theme Modern Premium
- [ ] Tidak ada error

---

## Task P1-T06: Settings module (Filament resource)

### Tujuan
CRUD settings (key-value, grouped) via Filament.

### Step-by-Step

**Step 1**: Buat Filament resource
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan make:filament-resource Setting --view
```

**Step 2**: Edit `app/Filament/Resources/SettingResource.php`
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Leaderhub\Core\Models\Setting;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('group')
                ->options([
                    'general' => 'General',
                    'contact' => 'Contact',
                    'seo' => 'SEO',
                    'social' => 'Social',
                    'currency' => 'Currency',
                ])->required(),
            Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('type')
                ->options(['text' => 'Text', 'textarea' => 'Textarea', 'boolean' => 'Boolean', 'number' => 'Number', 'json' => 'JSON', 'image' => 'Image'])
                ->required()->default('text'),
            Forms\Components\Textarea::make('value')->columnSpanFull(),
            Forms\Components\Toggle::make('is_translatable'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')->badge(),
                Tables\Columns\TextColumn::make('key')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('value')->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')->options([
                    'general' => 'General', 'contact' => 'Contact', 'seo' => 'SEO', 'social' => 'Social', 'currency' => 'Currency',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkDeleteGroup::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
            'view' => Pages\ViewSetting::route('/{record}'),
        ];
    }
}
```

### Validasi P1-T06
- [ ] `/admin/settings` list tampil
- [ ] Create setting baru berhasil
- [ ] Edit setting berhasil
- [ ] Filter by group berfungsi

---

## Task P1-T07: Seeder (admin user, default settings)

### Tujuan
Seed super_admin, content_admin, default settings.

### Step-by-Step

**Step 1**: Buat UserSeeder di core
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/seeders/UserSeeder.php << 'PHP'
<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Leaderhub\Core\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@leaderhub.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'content@leaderhub.id'],
            [
                'name' => 'Content Admin',
                'password' => Hash::make('password'),
                'role' => 'content_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
PHP
```

**Step 2**: Buat SettingSeeder
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/seeders/SettingSeeder.php << 'PHP'
<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'LeaderHub', 'type' => 'text'],
            ['group' => 'general', 'key' => 'tagline', 'value' => 'Distributor alat fitness premium', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'email', 'value' => 'info@leaderhub.id', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'phone', 'value' => '+62 21 1234 5678', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'whatsapp', 'value' => '6282112345678', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'address', 'value' => 'Jl. Fitness No. 1, Jakarta', 'type' => 'textarea'],
            ['group' => 'seo', 'key' => 'seo_title', 'value' => 'LeaderHub — Alat Fitness Premium', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'seo_description', 'value' => 'Distributor alat fitness premium di Indonesia', 'type' => 'textarea'],
            ['group' => 'social', 'key' => 'instagram', 'value' => 'https://instagram.com/leaderhub', 'type' => 'text'],
            ['group' => 'currency', 'key' => 'default', 'value' => 'IDR', 'type' => 'text'],
            ['group' => 'currency', 'key' => 'exchange_rate_usd_idr', 'value' => '15800', 'type' => 'number'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['group' => $s['group'], 'key' => $s['key']], $s);
        }
    }
}
PHP
```

**Step 3**: Register seeders di website `database/seeders/DatabaseSeeder.php`
```php
public function run(): void
{
    $this->call([
        \Leaderhub\Core\Database\Seeders\UserSeeder::class,
        \Leaderhub\Core\Database\Seeders\SettingSeeder::class,
    ]);
}
```

**Step 4**: Jalankan seeder
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan db:seed
```

### Validasi P1-T07
- [ ] `php artisan db:seed` berhasil
- [ ] User `admin@leaderhub.id` ada di DB, role `super_admin`
- [ ] Settings ada 11 record
- [ ] Login ke `/admin` dengan `admin@leaderhub.id` / `password` berhasil

---

## Task P1-T08: Static pages (About, Contact, Terms, Privacy)

### Tujuan
PageController render pages dari DB, PageResource untuk CRUD.

### Step-by-Step

**Step 1**: Buat PageController
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan make:controller PageController
```

Edit `app/Http/Controllers/PageController.php`:
```php
<?php

namespace App\Http\Controllers;

use Leaderhub\Core\Models\Page;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::withoutGlobalScope('published')->where('slug', $slug)->firstOrFail();
        $locale = session('locale', 'id');
        $title = $locale === 'en' && $page->title_en ? $page->title_en : $page->title;
        $content = $locale === 'en' && $page->content_en ? $page->content_en : $page->content;

        return view('page.show', compact('page', 'title', 'content'));
    }
}
```

**Step 2**: Buat view `resources/views/page/show.blade.php`
```blade
@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-[720px] mx-auto px-8 py-16">
    <h1 class="text-h1 font-bold tracking-tight mb-8">{{ $title }}</h1>
    <div class="prose prose-slate max-w-none text-body">
        {!! $content !!}
    </div>
</div>
@endsection
```

**Step 3**: Tambah route
```php
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show')->where('slug', '[a-z0-9-]+');
```
**Catatan**: Taruh route ini PALING BAWAH di `routes/web.php` agar tidak override route lain.

**Step 4**: Buat PageResource (Filament)
```bash
php artisan make:filament-resource Page
```

Edit `app/Filament/Resources/PageResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Leaderhub\Core\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Website';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('title')->required(),
            Forms\Components\TextInput::make('title_en')->label('Title (EN)'),
            Forms\Components\RichEditor::make('content')->columnSpanFull(),
            Forms\Components\RichEditor::make('content_en')->label('Content (EN)')->columnSpanFull(),
            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('seo_title'),
                Forms\Components\Textarea::make('seo_description'),
                Forms\Components\TextInput::make('seo_image'),
            ])->collapsible(),
            Forms\Components\Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published'])->required()->default('published'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('slug')->searchable(),
            Tables\Columns\TextColumn::make('title')->searchable(),
            Tables\Columns\TextColumn::make('status')->badge(),
        ])->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
```

**Step 5**: Seeder pages
```bash
cat > /run/media/salmon/data/Projects/leaderhub/core/database/seeders/PageSeeder.php << 'PHP'
<?php

namespace Leaderhub\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Leaderhub\Core\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'about', 'title' => 'Tentang Kami', 'content' => '<p>LeaderHub adalah distributor alat fitness premium...</p>'],
            ['slug' => 'contact', 'title' => 'Kontak', 'content' => '<p>Hubungi kami di info@leaderhub.id</p>'],
            ['slug' => 'terms', 'title' => 'Syarat & Ketentuan', 'content' => '<p>Syarat dan ketentuan penggunaan...</p>'],
            ['slug' => 'privacy', 'title' => 'Kebijakan Privasi', 'content' => '<p>Kebijakan privasi...</p>'],
        ];
        foreach ($pages as $p) {
            Page::updateOrCreate(['slug' => $p['slug']], array_merge($p, ['status' => 'published']));
        }
    }
}
PHP
```

Register di DatabaseSeeder & seed:
```bash
php artisan db:seed --class=Leaderhub\\Core\\Database\\Seeders\\PageSeeder
```

### Validasi P1-T08
- [ ] `/about` load dari DB
- [ ] `/contact` load
- [ ] `/terms` load
- [ ] `/privacy` load
- [ ] Admin bisa edit page via `/admin/pages`
- [ ] Route `{slug}` tidak override route lain (test `/products` tetap jalan)

---

## Checklist Akhir Phase 1

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website

# 1. Migrations clean
php artisan migrate:status | grep -v "Ran" | grep "Pending" && echo "✗ Ada migration pending" || echo "✓ Semua migration ran"

# 2. Seeder clean
php artisan db:seed --force
echo "✓ Seeder OK"

# 3. Serve & test
php artisan serve --port=8000 &
sleep 2

# Homepage
curl -s http://localhost:8000 | grep -q "LeaderHub" && echo "✓ Homepage OK" || echo "✗ Homepage FAIL"

# Static pages
for slug in about contact terms privacy; do
  curl -s http://localhost:8000/$slug | grep -q "max-w-\[720px\]" && echo "✓ /$slug OK" || echo "✗ /$slug FAIL"
done

# Admin login
curl -s http://localhost:8000/admin/login | grep -q "Filament" && echo "✓ Admin login OK" || echo "✗ Admin FAIL"

# Theme toggle
curl -s -X POST http://localhost:8000/api/theme -H "X-CSRF-TOKEN: $(curl -s http://localhost:8000 | grep csrf-token | sed 's/.*content="\([^"]*\)".*/\1/')" -c /tmp/cookie.txt -b /tmp/cookie.txt | grep -q "dark" && echo "✓ Theme toggle OK" || echo "✗ Theme FAIL"

kill %1

# 4. FK integrity check
php artisan tinker --execute="
use Leaderhub\Core\Models\User;
use Leaderhub\Core\Models\Setting;
use Leaderhub\Core\Models\Page;
echo 'Users: ' . User::count() . PHP_EOL;
echo 'Settings: ' . Setting::count() . PHP_EOL;
echo 'Pages: ' . Page::count() . PHP_EOL;
"

# 5. Git commit
cd /run/media/salmon/data/Projects/leaderhub
git add -A
git commit -m "Phase 1: Website foundation — layout, design system, settings, auth, pages"
```

**Jika semua ✓ → lanjut Phase 2. Jika ada ✗ → fix dulu.**