## Why

Fondasi website (Phase 1) hanya punya layout dan halaman statis. Pengunjung butuh melihat produk yang dijual, gallery bukti kualitas, dan homepage yang menarik. Phase 2 membangun konten inti: homepage dengan hero banner, katalog produk dengan filter/search, gallery foto, serta SEO dasar (sitemap, structured data) agar mulai terindex Google.

## What Changes

- Migrations di core: `products`, `product_images`, `product_specs`, `warehouses`, `stock_levels`, `stock_movements`, `gallery_albums`, `gallery_items`, `banners`
- Models di core: Product, ProductImage, ProductSpec, Warehouse, StockLevel, StockMovement, GalleryAlbum, GalleryItem, Banner
- Filament resources (website admin): Product, Category, Brand, GalleryAlbum, GalleryItem, Banner
- Homepage: hero Swiper, featured products, shop by category, CTA WhatsApp
- Product listing: filter (category, brand, price), search, sort, pagination 12/page
- Product detail: image gallery, specs table, related products, WA button, add to inquiry
- Gallery: masonry grid, GLightbox, album filter
- Image optimization: WebP conversion, lazy load, responsive srcset
- SEO: sitemap.xml dynamic, robots.txt, JSON-LD (Product, Organization, Breadcrumb)
- Demo content seeder: 5 produk, 3 kategori, 5 brand, 1 banner, 1 gallery album

## Capabilities

### New Capabilities
- `homepage`: Homepage dengan hero banner Swiper, featured products, shop by category, CTA
- `product-catalog`: Listing produk dengan filter/search/sort/pagination + detail produk dengan gallery/specs/related
- `gallery`: Gallery masonry dengan album filter dan lightbox
- `seo-basics`: Sitemap.xml dynamic, robots.txt, JSON-LD structured data
- `content-admin-products`: Filament CRUD untuk Product, Category, Brand, Gallery, Banner

### Modified Capabilities
<!-- None -->

## Impact

- **Code**: `apps/website/` (controllers, views, Filament resources), `core/` (migrations, models)
- **Database**: 9 tabel baru
- **Dependencies**: swiper, glightbox, intervention/image, spatie/laravel-sitemap

**Non-goals**: Blog & inquiry (Phase 3), ERP inventory management (Phase 4), AI SEO (Phase 5)

**Detail**: `docs/tasks/phase-2-website-content.md`