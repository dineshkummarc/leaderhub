# Phase 5 — Integration & Polish (Detail Task Step-by-Step)

> **Estimasi**: 1 minggu | **Tujuan**: Connect website ↔ hub ↔ gym, polish, optimize, SEO, docs
> **Prasyarat**: Phase 4 selesai (LeaderP + LeadGym MVP jalan)

## Validasi Akhir Phase 5
1. Product di LeaderP → tampil di website (shared DB)
2. Inquiry dari website → muncul di LeaderP → convert ke SO
3. License issue di LeaderP → LeadGym tenant bisa aktivasi
4. License suspend → LeadGym read-only
5. Google Translate widget + manual override berfungsi
6. SEO structured data (JSON-LD) lengkap
7. Performance: PageSpeed > 80
8. Accessibility: Lighthouse > 90
9. Error pages (404, 500, 503) tampil
10. Documentation lengkap

---

## Task P5-T01: Verify shared DB integration

### Step-by-Step

**Step 1**: Test product sync
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/hub
php artisan tinker
>>> use Leaderhub\Core\Models\Product;
>>> $p = Product::create(['name' => 'Test Sync Product', 'slug' => 'test-sync', 'sku' => 'SYNC-001', 'category_id' => 1, 'sale_price_idr' => 1000000, 'status' => 'published']);
>>> exit

cd ../website
php artisan tinker
>>> use Leaderhub\Core\Models\Product;
>>> Product::where('sku', 'SYNC-001')->first()->name;  // harus "Test Sync Product"
>>> exit
```

**Step 2**: Test inquiry → SO
```bash
# Submit inquiry via website (lihat Phase 3)
# Lihat di hub
cd ../hub
php artisan tinker
>>> use Leaderhub\Core\Models\Inquiry;
>>> Inquiry::latest()->first()->inquiry_number;  // harus INQ-YYYY-XXXXX
```

**Step 3**: Test license flow
```bash
# Issue license di hub
# Input license key di gym tenant settings
# Validate via API
# Suspend di hub → sync ke gym → read-only
```

### Validasi P5-T01
- [ ] Product sync: create di hub → tampil di website
- [   ] Inquiry sync: submit di website → muncul di hub
- [ ] License sync: issue di hub → aktivasi di gym → suspend di hub → read-only di gym

---

## Task P5-T02: Google Translate widget + manual translation override

### Step-by-Step

**Step 1**: Tambah Google Translate widget di `partials/header.blade.php`
```blade
<div id="google_translate_element" class="hidden"></div>
<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement({pageLanguage: 'id', includedLanguages: 'en'}, 'google_translate_element');
}
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
```

**Step 2**: Tambah field `_en` di Filament resources (Product, BlogPost, Page) — sudah ada di migration (name_en, description_en, dll)

**Step 3**: Update controllers untuk pakai field_en jika locale = en (sudah di Phase 2 & 3)

### Validasi P5-T02
- [ ] Toggle EN → konten EN tampil jika ada
- [   ] Jika belum diterjemahkan → Google Translate fallback
- [ ] Admin bisa input EN via Filament

---

## Task P5-T03: SEO structured data (JSON-LD)

### Step-by-Step

**Step 1**: Tambah BreadcrumbList JSON-LD di layout
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

**Step 2**: Product & Article JSON-LD sudah ada di Phase 2 & 3

**Step 3**: Tambah FAQPage JSON-LD di blog (opsional, jika ada FAQ section)

### Validasi P5-T03
- [ ] View source product detail → JSON-LD Product ada
- [   ] View source blog detail → JSON-LD Article ada
- [ ] View source homepage → JSON-LD Organization ada
- [ ] Valid di https://validator.schema.org

---

## Task P5-T04: Performance optimization

### Step-by-Step

**Step 1**: Cache config, route, view
```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

**Step 2**: Redis (jika ada)
```bash
# .env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

**Step 3**: Image optimization (sudah di Phase 2 T06)

**Step 4**: Critical CSS inline (opsional, pakai Vite plugin)

### Validasi P5-T04
- [ ] PageSpeed Insights > 80
- [   ] Core Web Vitals hijau

---

## Task P5-T05: Accessibility audit (WCAG 2.1 AA)

### Step-by-Step

**Step 1**: Cek kontras warna (sudah sesuai design system §9)
**Step 2**: Tambah `aria-label` di icon-only buttons
**Step 3**: Pastikan keyboard navigation (Tab order)
**Step 4**: Respect `prefers-reduced-motion`

### Validasi P5-T05
- [ ] Lighthouse accessibility > 90
- [   ] Tidak ada error di axe DevTools

---

## Task P5-T06: Cross-browser & responsive testing

### Step-by-Step

Test di: Chrome, Firefox, Safari, Edge (desktop & mobile)

### Validasi P5-T06
- [ ] Semua browser render konsisten
- [   ] Mobile responsive (320px, 768px, 1024px, 1280px)

---

## Task P5-T07: Error pages (404, 500, 503)

### Step-by-Step

**Step 1**: Buat `resources/views/errors/404.blade.php`
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

**Step 2**: Buat `500.blade.php` dan `503.blade.php` (pattern sama)

### Validasi P5-T07
- [ ] `/nonexistent` → 404 page tampil
- [   ] 500 & 503 page ada

---

## Task P5-T08: Documentation (admin guide, user guide, gym owner guide)

### Step-by-Step

Buat `docs/guides/`:
- `admin-leaderp.md` — panduan admin ERP
- `admin-website.md` — panduan admin konten website
- `gym-owner.md` — panduan gym owner LeadGym
- `superadmin.md` — panduan superadmin (DB management, license, users)

### Validasi P5-T08
- [ ] Dokumen lengkap & akurat

---

## Checklist Akhir Phase 5

```bash
cd /run/media/salmon/data/Projects/leaderhub/apps/website
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan serve --port=8000 &
sleep 2

# Integration test
curl -s http://localhost:8000/products | grep -q "produk ditemukan" && echo "✓ Website OK" || echo "✗ FAIL"
curl -s http://localhost:8000/nonexistent | grep -q "404" && echo "✓ 404 OK" || echo "✗ 404 FAIL"

# SEO
curl -s http://localhost:8000 | grep -q "Organization" && echo "✓ JSON-LD OK" || echo "✗ FAIL"
curl -s http://localhost:8000/sitemap.xml | grep -q "<urlset" && echo "✓ Sitemap OK" || echo "✗ FAIL"

kill %1
cd /run/media/salmon/data/Projects/leaderhub
git add -A && git commit -m "Phase 5: Integration & polish — SEO, performance, accessibility, docs"
```

**Jika semua ✓ → lanjut Phase 6 (Deployment).**