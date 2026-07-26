# Spec: product-catalog

## Requirements

### REQ-001: Listing Page
`GET /products` MUST display a grid of 12 products per page with: filter sidebar (category, brand, price min/max), search input, sort dropdown (newest, price asc, price desc, name A-Z), pagination.

### REQ-002: Product Card
Each product card MUST show: image (aspect 4:5), category label, name (weight 600), price (tabular-nums, IDR formatted). Hover: lift -2px + shadow-lg.

### REQ-003: Detail Page
`GET /products/{slug}` MUST display: image gallery (main + thumbnails swap), name (h1), SKU (mono), brand, price, description, specs table, related products (4, same category), "Tambah ke Daftar Minat" button, "Hubungi via WA" button.

### REQ-004: Filter & Search
Filter by category MUST filter via `category` query param (slug). Search MUST use fullText on `name` and `sku`. Sort MUST apply correct ORDER BY. Pagination MUST preserve query string via `withQueryString()`.

### REQ-005: Related Products
Detail page MUST show up to 4 related products from the same category, excluding the current product.

### REQ-006: JSON-LD Product
Detail page MUST include JSON-LD Product schema with: name, sku, brand, description, offers (priceCurrency IDR, price, availability InStock).

### REQ-007: Caching
Product listing MUST be cached 60 min per query string. Product detail MUST be cached 60 min per slug. Cache invalidated on product update.

### REQ-008: Empty State
When no products match filters, an empty state message MUST be displayed.

### REQ-009: 404 for Invalid Slug
`GET /products/{nonexistent-slug}` MUST return 404.