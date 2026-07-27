# Tasks — Phase 2: Website Content

> **Detail**: `docs/tasks/phase-2-website-content.md` | **Estimasi**: 2 minggu | **Prasyarat**: Phase 1

## Tasks

### T01: Migrations — products, images, specs, warehouses, stock, gallery, banners
- [x] 4 migration files di core + 9 models (Product, ProductImage, ProductSpec, Warehouse, StockLevel, StockMovement, GalleryAlbum, GalleryItem, Banner)
- **Validation**: migrate berhasil, FK rapi, models accessible
- **Effort**: 4h

### T02: Filament resources — Product, Category, Brand, Gallery, Banner
- [x] 6 Filament resources dengan form + table + filters
- **Validation**: CRUD semua resource berfungsi, file upload tersimpan
- **Effort**: 6h

### T03: Homepage (hero, featured, category, CTA)
- [x] HomeController (cache 60min), home.blade.php (Swiper, featured, category, CTA), product-card partial, install swiper
- **Validation**: Homepage load, semua section tampil, Swiper autoplay
- **Effort**: 4h

### T04: Product listing page
- [x] ProductController index (filter, search, sort, paginate), products/index.blade.php
- **Validation**: `/products` load, filter/search/sort/pagination berfungsi
- **Effort**: 4h

### T05: Product detail page
- [x] ProductController show, products/show.blade.php (gallery, specs, related, WA, JSON-LD)
- **Validation**: `/products/{slug}` load, gallery swap, specs, related, JSON-LD ada
- **Effort**: 4h

### T06: Gallery page
- [x] GalleryController, gallery.blade.php (masonry, GLightbox, album filter)
- **Validation**: `/gallery` load, lightbox, filter berfungsi
- **Effort**: 3h

### T07: Image optimization (WebP, lazy load, srcset)
- [x] ImageService di core (WebP upload, thumbnail, srcset helper), lazy load attribute
- **Validation**: Upload → WebP, thumbnail, lazy load
- **Effort**: 3h

### T08: SEO meta + sitemap + robots
- [x] Sitemap route/controller, robots.txt, JSON-LD di layout dan shown di layout
- **Validation**: `/sitemap.xml` accessible, `/robots.txt` accessible, JSON-LD di view source
- **Effort**: 2h

### T09: Demo content seeder
- [x] Buat `DemoContentSeeder` untuk Phase 2 (Categories, Brands, Products, Gallery, Banners), attach image dummies.e, stock levels)
- **Validation**: Seeder berhasil, homepage menampilkan featured products
- **Effort**: 2h

## Final Validation
- [x] Homepage dengan hero Swiper
- [x] `/products` listing dengan filter/search/sort/pagination
- [x] `/products/{slug}` detail dengan gallery/specs/related/JSON-LD
- [x] `/gallery` masonry + lightbox
- [x] `/sitemap.xml` + `/robots.txt` accessible
- [x] Admin CRUD semua resource berfungsi
- [x] Tidak ada error, FK rapi