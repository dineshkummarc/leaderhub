## ADDED Requirements

### Requirement: Product Resource
A `ProductResource` SHALL exist in the admin panel with: form (name, slug auto, sku, barcode, category select, brand select, material, purchase_price_usd, sale_price_idr, warranty_period_months, description RichEditor, flags is_featured/is_new/is_best_seller, status, sort_order, images Repeater with FileUpload, specs Repeater), table (name, sku, category, price, is_featured, status badge), filters (category, status).

#### Scenario: Admin creates a product with images and specs
- **WHEN** admin fills the product create form with name, sku, price, 2 images, and 2 specs
- **THEN** the product MUST be created with all images and specs saved to the database

### Requirement: Category Resource
A `CategoryResource` SHALL exist with: form (parent_id select self, slug, name, name_en, description, image, is_featured, sort_order), table (name, slug, is_featured).

#### Scenario: Admin creates a category
- **WHEN** admin fills the category create form with name and slug
- **THEN** the category MUST be created in the database

### Requirement: Brand Resource
A `BrandResource` SHALL exist with: form (name, slug, logo, description), table (name, slug).

#### Scenario: Admin creates a brand
- **WHEN** admin fills the brand create form
- **THEN** the brand MUST be created in the database

### Requirement: Gallery Resources
`GalleryAlbumResource` (name, slug, description, cover_image, sort_order) and `GalleryItemResource` (album select, path FileUpload, alt, caption, sort_order) SHALL exist.

#### Scenario: Admin creates a gallery album with items
- **WHEN** admin creates an album and then adds items to it
- **THEN** the items MUST be associated with the album and appear on the gallery page

### Requirement: Banner Resource
A `BannerResource` SHALL exist with: form (title, subtitle, image FileUpload, link, cta_text, sort_order, is_active), table (title, is_active, sort_order).

#### Scenario: Admin creates an active banner
- **WHEN** admin creates a banner with is_active = true
- **THEN** the banner MUST appear in the homepage hero carousel

### Requirement: File Uploads
All file uploads SHALL store to `storage/app/public/{directory}/` and be accessible via `Storage::url()`. Images SHALL be converted to WebP via ImageService.

#### Scenario: Uploaded image is converted to WebP
- **WHEN** admin uploads a JPG image via any Filament FileUpload field
- **THEN** the stored file MUST be in WebP format

### Requirement: CRUD Works
Admin SHALL be able to create, read, update, and delete all resources without errors. Created products with `status = published` SHALL appear on the website.

#### Scenario: Published product appears on website
- **WHEN** admin creates a product with status `published`
- **THEN** the product MUST appear in the website product listing at `/products`
