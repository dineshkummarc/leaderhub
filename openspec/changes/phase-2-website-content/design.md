# Design — Phase 2: Website Content

## Context
Lihat `docs/03-database-schema.md` (schema), `docs/07-features/01-homepage.md`, `docs/07-features/02-product-catalog.md`, `docs/07-features/04-gallery.md`, `docs/tasks/phase-2-website-content.md`.

## Approach

### Migrations (di core)
- `products` — FK category (cascade), FK brand (nullOnDelete), slug unique, sku unique, fullText(name, sku), index(category_id, status), index(status, is_featured)
- `product_images` — FK product (cascade), sort_order
- `product_specs` — FK product (cascade), label, value, sort_order
- `warehouses` — code unique, is_active
- `stock_levels` — FK product + warehouse (cascade), unique(product_id, warehouse_id), low_stock_threshold
- `stock_movements` — FK product + warehouse (cascade), type (in/out/transfer), reference_type/id (polymorphic)
- `gallery_albums` — slug unique, cover_image, sort_order
- `gallery_items` — FK album (cascade), path, alt, caption, views, sort_order
- `banners` — title, subtitle, image, link, cta_text, is_active, sort_order

### Models (di core)
- `Product` — global scope `published`, scope `featured`, relations: category, brand, images, specs, stockLevels, accessor `featured_image`, `total_stock`
- `ProductImage`, `ProductSpec` — belongsTo Product
- `Warehouse` — hasMany StockLevels
- `StockLevel` — belongsTo Product + Warehouse
- `StockMovement` — belongsTo Product + Warehouse
- `GalleryAlbum` — hasMany Items
- `GalleryItem` — belongsTo Album
- `Banner` — cast is_active boolean

### Homepage
- `HomeController` — cache 60min, load banners (active), featured products (8), featured categories
- View: Swiper hero, shop by category grid, featured products grid, CTA WhatsApp

### Product Listing
- `ProductController@index` — filter (category, brand, price min/max), search (fullText), sort (newest/price_asc/price_desc/name), paginate 12
- `ProductController@show` — load product (without global scope for admin preview), related (same category, exclude self, limit 4)

### Gallery
- `GalleryController@index` — load albums + items, masonry (CSS columns), GLightbox, album filter (client-side)

### Image Optimization
- `ImageService` di core — upload WebP (quality 80), generate thumbnail (width 400), `srcset()` helper

### SEO
- `SitemapController` — spatie/sitemap, include products + pages, route `/sitemap.xml`
- `public/robots.txt` — Allow + Sitemap URL
- JSON-LD: Product (detail page), Organization (layout), Breadcrumb (future)

## Models / Tables Affected
- `products`, `product_images`, `product_specs`, `warehouses`, `stock_levels`, `stock_movements`, `gallery_albums`, `gallery_items`, `banners`

## Risks
- **FullText index**: MySQL 8.0 support fullText, pastikan query pakai `whereFullText`
- **Swiper import**: pakai ESM import di Vite, bukan CDN
- **Image upload**: pastikan storage symlink berfungsi (Phase 0)

## Dependencies
- Phase 0 (monorepo, core, storage)
- Phase 1 (layout, Tailwind, Filament admin)