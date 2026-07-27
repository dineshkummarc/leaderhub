# Design — Phase 5: Integration & Polish

## Context
- **Architecture**: `docs/02-architecture.md` §6 (data flow), §7 (deployment)
- **Design system**: `docs/04-design-system.md` §9 (accessibility)
- **Step-by-step**: `docs/tasks/phase-5-integration-polish.md`
- **Feature expansion**: `docs/09-feature-expansion.md` §2 (SEO boost)

## Approach

### 1. Integration Verification

Test end-to-end flow:

**Product sync**:
```bash
# Di LeaderP, create product
cd apps/hub && php artisan tinker
>>> use Leaderhub\Core\Models\Product;
>>> Product::create(['name'=>'Sync Test','slug'=>'sync-test','sku'=>'SYNC-001','category_id'=>1,'sale_price_idr'=>1000000,'status'=>'published']);
# Di website, verify
cd ../website && php artisan tinker
>>> Leaderhub\Core\Models\Product::where('sku','SYNC-001')->first()->name;
# Harus: "Sync Test"
```

**Inquiry → SO**:
```bash
# Submit inquiry via website (Phase 3)
# Di LeaderP, verify inquiry muncul
# Convert ke SO via Filament action
# Verify: inquiry.status = 'converted', SO created
```

**License flow**:
```bash
# Di LeaderP, issue license
# Di LeadGym, input license key di tenant settings
# Verify: license_status = 'active'
# Di LeaderP, suspend license
# Run gym:sync-licenses
# Verify: LeadGym tenant license_status = 'suspended', read-only mode
```

**Multi-tenant isolation**:
```bash
# Login sebagai Tenant A, create member
# Login sebagai Tenant B, verify member dari Tenant A TIDAK tampil
```

### 2. Multi-Bahasa Enhancement

**Google Translate widget** di `partials/header.blade.php`:
```blade
<div id="google_translate_element" class="hidden"></div>
<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'id',
        includedLanguages: 'en',
        autoDisplay: false
    }, 'google_translate_element');
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
```

**Manual override di controllers** (sudah ada di Phase 2 & 3, verify):
```php
// ProductController@show
$locale = session('locale', 'id');
$title = $locale === 'en' && $product->name_en ? $product->name_en : $product->name;
```

### 3. SEO Advanced

**BreadcrumbList JSON-LD** di layout:
```blade
@section('scripts')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ config('app.url') }}" },
    @yield('breadcrumb_items')
  ]
}
</script>
@endsection
```

**Canonical** di layout:
```blade
<link rel="canonical" href="{{ url()->current() }}">
```

**hreflang** di layout:
```blade
<link rel="alternate" hreflang="id" href="{{ url()->current() }}">
<link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
```

**Open Graph + Twitter Card** di layout:
```blade
<meta property="og:title" content="@yield('og_title', config('app.name'))">
<meta property="og:description" content="@yield('og_description', Setting::get('seo_description', 'seo'))">
<meta property="og:image" content="@yield('og_image', asset('storage/' . Setting::get('logo', 'general')))">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('og_title', config('app.name'))">
<meta name="twitter:description" content="@yield('og_description', Setting::get('seo_description', 'seo'))">
<meta name="twitter:image" content="@yield('og_image', asset('storage/' . Setting::get('logo', 'general')))">
```

### 4. Performance

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Redis (opsional):
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 5. Accessibility

- `aria-label` di icon-only buttons:
```blade
<button aria-label="Toggle dark mode" onclick="toggleTheme()">
```

- `prefers-reduced-motion`:
```css
@media (prefers-reduced-motion: reduce) {
    * { transition: none !important; animation: none !important; }
}
```

### 6. Error Pages

`resources/views/errors/404.blade.php`:
```blade
@extends('layouts.app')
@section('title', '404 — Halaman Tidak Ditemukan')
@section('content')
<div class="max-w-[720px] mx-auto px-8 py-20 text-center">
    <div class="text-6xl font-bold text-accent mb-4">404</div>
    <h1 class="text-h2 font-bold tracking-tight mb-3">Halaman tidak ditemukan</h1>
    <p class="text-text-muted mb-6">Maaf, halaman yang Anda cari tidak ada atau sudah dipindahkan.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
</div>
@endsection
```

Pattern sama untuk 500, 503.

## Models / Tables Affected
- Tidak ada tabel baru

## Dependencies
- Phase 0-4 selesai