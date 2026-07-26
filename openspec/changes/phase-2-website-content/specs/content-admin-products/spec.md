# Spec: content-admin-products

## Requirements

### REQ-001: Product Resource
A `ProductResource` MUST exist in the admin panel with: form (name, slug auto, sku, barcode, category select, brand select, material, purchase_price_usd, sale_price_idr, warranty_period_months, description RichEditor, flags is_featured/is_new/is_best_seller, status, sort_order, images Repeater with FileUpload, specs Repeater), table (name, sku, category, price, is_featured, status badge), filters (category, status).

### REQ-002: Category Resource
A `CategoryResource` MUST exist with: form (parent_id select self, slug, name, name_en, description, image, is_featured, sort_order), table (name, slug, is_featured).

### REQ-003: Brand Resource
A `BrandResource` MUST exist with: form (name, slug, logo, description), table (name, slug).

### REQ-004: Gallery Resources
`GalleryAlbumResource` (name, slug, description, cover_image, sort_order) and `GalleryItemResource` (album select, path FileUpload, alt, caption, sort_order) MUST exist.

### REQ-005: Banner Resource
A `BannerResource` MUST exist with: form (title, subtitle, image FileUpload, link, cta_text, sort_order, is_active), table (title, is_active, sort_order).

### REQ-006: File Uploads
All file uploads MUST store to `storage/app/public/{directory}/` and be accessible via `Storage::url()`. Images MUST be converted to WebP via ImageService.

### REQ-007: CRUD Works
Admin MUST be able to create, read, update, and delete all resources without errors. Created products with `status = published` MUST appear on the website.