# LeaderHub — Feature Spec: Product Catalog

> **Priority**: A (MVP) | **App**: Website | **Status**: Draft

## 1. Tujuan
Menampilkan katalog produk dengan filter, search, sort, pagination agar
pengunjung menemukan produk yang relevan.

## 2. Halaman

### 2.1 Product Listing (`/products`)
```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
├─────────────────────────────────────────────┤
│ PAGE TITLE: "Produk" (h1, weight 700)       │
│ Breadcrumb: Home / Produk                    │
├──────────────┬──────────────────────────────┤
│ SIDEBAR      │ PRODUCT GRID (3:4 cards)      │
│ FILTER       │ ┌──┐ ┌──┐ ┌──┐ ┌──┐          │
│              │ │  │ │  │ │  │ │  │          │
│ Kategori     │ └──┘ └──┘ └──┘ └──┘          │
│ □ Treadmill  │ ┌──┐ ┌──┐ ┌──┐ ┌──┐          │
│ □ Dumbbell    │ │  │ │  │ │  │ │  │          │
│ □ Machine    │ └──┘ └──┘ └──┘ └──┘          │
│              │                              │
│ Brand        │ SORT: Newest ▼  | 12 of 30  │
│ □ Technogym  │                              │
│ □ Life       │ PAGINATION: < 1 2 3 >        │
│              │                              │
│ Price Range  │                              │
│ [min] - [max]│                              │
│              │                              │
│ [APPLY]      │                              │
├──────────────┴──────────────────────────────┤
│ FOOTER                                       │
└─────────────────────────────────────────────┘
```

### 2.2 Product Detail (`/products/{slug}`)
```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
├─────────────────────────────────────────────┤
│ Breadcrumb: Home / Produk / Kategori / Name │
├──────────────────┬──────────────────────────┤
│ IMAGE GALLERY    │ PRODUCT INFO              │
│ (vertical thumb) │ - Name (h1, weight 700)  │
│                  │ - SKU (mono)             │
│ [Main image]     │ - Brand                   │
│                  │ - Price (red, large)     │
│ [thumb][thumb]   │ - Short description       │
│                  │ - Specs table             │
│                  │ - Qty selector            │
│                  │ [Tambah ke Daftar Minat] │
│                  │ [Hubungi via WA]         │
├──────────────────┴──────────────────────────┤
│ RELATED PRODUCTS (grid 4)                    │
├─────────────────────────────────────────────┤
│ FOOTER                                       │
└─────────────────────────────────────────────┘
```

## 3. Filter & Search

### 3.1 Filter
| Filter | Source | Type |
|--------|--------|------|
| Kategori | `categories` (hierarchical) | checkbox tree |
| Brand | `brands` | checkbox |
| Price range | `sale_price_idr` | min-max input |
| Status | (hidden) `status = published` | — |

### 3.2 Search
- Search by name OR SKU
- Query: `WHERE name LIKE %q% OR sku LIKE %q%`
- Input di header (global) + di listing

### 3.3 Sort
| Option | Query |
|--------|-------|
| Terbaru | `ORDER BY created_at DESC` |
| Harga Terendah | `ORDER BY sale_price_idr ASC` |
| Harga Tertinggi | `ORDER BY sale_price_idr DESC` |
| Nama A-Z | `ORDER BY name ASC` |

### 3.4 Pagination
- 12 per page
- URL: `?page=N`
- SEO: canonical, prev/next meta

## 4. Product Card (listing)
- Lihat `04-design-system.md` §3.2
- Aspect 4:5, border halus, radius-lg, hover lift -2px + shadow-lg
- Tag pill accent, category label muted, name weight 600, price tabular-nums
- "Tambah ke Daftar Minat" icon button (overlay on hover)

## 5. Product Detail

### 5.1 Image Gallery
- Main image + thumbnail vertical strip
- Click thumbnail → swap main
- Zoom on hover (or click → lightbox)
- WebP, responsive srcset

### 5.2 Info
- Name: h1, weight 700, letter-spacing -0.03em
- SKU: mono, muted
- Brand: link to `/products?brand={slug}`
- Price: weight 700, tabular-nums, size 1.5rem
- Description: rich text
- Specs table: dari `product_specs` (label-value pairs)

### 5.3 Actions
- Qty selector (1-99)
- "Tambah ke Daftar Minat" → add to inquiry cart (localStorage)
- "Hubungi via WA" → wa.me link dengan pre-filled message (produk + SKU)

### 5.4 Related Products
- Same category, exclude current, limit 4
- Grid 4 kolom

## 6. Data Source
- Listing: `products` WHERE `status = published` + filter + sort + paginate
- Detail: `products` WHERE `slug = ?` AND `status = published` + images + specs
- Related: `products` WHERE `category_id = ?` AND `id != ?` AND `status = published` LIMIT 4

## 7. Caching
- Listing: cache 60 menit per query string, invalidate saat product update
- Detail: cache 60 menit per slug, invalidate saat product update
- Keys: `product:list:{hash}`, `product:detail:{slug}`

## 8. SEO
- Listing: title "Produk - LeaderHub", meta description, canonical
- Detail: title "{product.name} - LeaderHub", meta dari product, og:image
- JSON-LD: Product schema (name, image, description, sku, brand, offers)
- Breadcrumb JSON-LD
- Sitemap: semua product URL

## 9. Acceptance Criteria
1. Listing tampil 12 produk per page
2. Filter kategori, brand, price berfungsi
3. Search by name/SKU berfungsi
4. Sort berfungsi (4 option)
5. Pagination berfungsi
6. Product detail: gallery, info, specs, related
7. "Tambah ke Daftar Minat" berfungsi (localStorage)
8. "Hubungi via WA" link benar
9. SEO meta + JSON-LD lengkap
10. Responsive mobile/desktop
11. Load < 2 detik (cached)