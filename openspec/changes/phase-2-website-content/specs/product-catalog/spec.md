## ADDED Requirements

### Requirement: Listing Page
`GET /products` SHALL display a grid of 12 products per page with: filter sidebar (category, brand, price min/max), search input, sort dropdown (newest, price asc, price desc, name A-Z), and pagination.

#### Scenario: Products are paginated at 12 per page
- **WHEN** user navigates to `/products` and there are 30 published products
- **THEN** the page MUST display 12 products and pagination links

### Requirement: Product Card
Each product card SHALL show: image (aspect 4:5), category label, name (weight 600), price (tabular-nums, IDR formatted). Hover SHALL lift the card -2px with shadow-lg.

#### Scenario: Product card displays price in IDR format
- **WHEN** a product card with sale_price_idr = 45000000 is rendered
- **THEN** the price MUST display as "Rp 45.000.000" with tabular-nums

### Requirement: Detail Page
`GET /products/{slug}` SHALL display: image gallery (main + thumbnails swap), name as H1, SKU (mono), brand, price, description, specs table, related products (4, same category), "Tambah ke Daftar Minat" button, "Hubungi via WA" button.

#### Scenario: Thumbnail click swaps main image
- **WHEN** user clicks a thumbnail image on the product detail page
- **THEN** the main image MUST update to show the clicked thumbnail's image

### Requirement: Filter and Search
Filter by category SHALL filter via `category` query param (slug). Search SHALL use fullText on `name` and `sku`. Sort SHALL apply correct ORDER BY. Pagination SHALL preserve query string via `withQueryString()`.

#### Scenario: Filter preserves search query in pagination
- **WHEN** user searches for "treadmill" and navigates to page 2
- **THEN** the URL MUST include `?q=treadmill&page=2`

### Requirement: Related Products
Detail page SHALL show up to 4 related products from the same category, excluding the current product.

#### Scenario: Related products exclude current product
- **WHEN** user views product detail for "Treadmill Pro X1"
- **THEN** the related products section MUST NOT include "Treadmill Pro X1"

### Requirement: JSON-LD Product
Detail page SHALL include JSON-LD Product schema with: name, sku, brand, description, offers (priceCurrency IDR, price, availability InStock).

#### Scenario: Product schema is valid
- **WHEN** developer validates the product detail page JSON-LD at validator.schema.org
- **THEN** the Product schema MUST pass validation

### Requirement: 404 for Invalid Slug
`GET /products/{nonexistent-slug}` SHALL return a 404 response.

#### Scenario: Invalid slug returns 404
- **WHEN** user navigates to `/products/nonexistent-product`
- **THEN** the server MUST return HTTP 404

### Requirement: Empty State
When no products match filters, an empty state message SHALL be displayed.

#### Scenario: No products match filter
- **WHEN** user filters by a category with no products
- **THEN** the page MUST display an empty state message instead of an empty grid
