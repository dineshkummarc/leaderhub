# LeaderHub — Feature Spec: Homepage

> **Priority**: A (MVP) | **App**: Website | **Status**: Draft

## 1. Tujuan
Menampilkan brand identity, produk unggulan, kategori, dan artikel terbaru
untuk menarik minat pengunjung dan mengarahkan ke katalog/inquiry.

## 2. Layout

```
┌─────────────────────────────────────────────┐
│ HEADER (sticky, 72px)                       │
│ [LOGO] [Nav: Products, Gallery, Blog, About]│
│          [Lang Toggle] [Dark Mode] [Inquiry]│
├─────────────────────────────────────────────┤
│ HERO BANNER (Swiper, 100vh)                 │
│ - Gradien gelap + radial glow aksen         │
│ - Display headline (48px, weight 700)       │
│ - Eyebrow aksen merah                       │
│ - CTA: "Jelajahi Katalog"                  │
│ - Stats dengan border-top subtle            │
├─────────────────────────────────────────────┤
│ SHOP BY CATEGORY (grid 4 col)               │
│ - Category card: image + name              │
│ - Hover: lift lembut, shadow halus         │
├─────────────────────────────────────────────┤
│ FEATURED PRODUCTS (grid 4 col, 8 items)     │
│ - Product card 3:4                           │
│ - Tag: Featured/New/Best Seller             │
│ - "Tambah ke Daftar Minat" button           │
├─────────────────────────────────────────────┤
│ LATEST STORIES (editorial grid 3 col)       │
│ - Blog card 16:10                           │
│ - Category badge, title, meta               │
├─────────────────────────────────────────────┤
│ CTA SECTION                                 │
│ - "Butuh konsultasi?" + WA button           │
├─────────────────────────────────────────────┤
│ FOOTER                                      │
│ - About, Links, Contact, Social             │
└─────────────────────────────────────────────┘
```

## 3. Komponen

### 3.1 Hero Banner
- **Swiper.js** carousel, 3-5 slide
- Tiap slide: gradien gelap (slate) + radial glow aksen lembut
- Eyebrow 13px weight 600 accent
- Display headline 48px weight 700 letter-spacing -0.035em, putih
- Subtitle 18px slate-300
- CTA: primary + secondary (translucent)
- Stats: border-top subtle, angka tabular-nums
- Auto-play 6s, pagination dots, prev/next arrows
- Mobile: headline 32px, stats compact

### 3.2 Shop by Category
- Grid 4 kolom (desktop), 2 (tablet), 1 (mobile)
- Category card: aspect 1:1, border halus, radius-lg, hover lift -2px + shadow-md
- Click → `/products?category={slug}`

### 3.3 Featured Products
- Grid 4 kolom, 8 produk (`is_featured = true`)
- Product card (lihat `04-design-system.md` §3.2)
- "Tambah ke Daftar Minat" icon button (cart+)
- Click card → `/products/{slug}`

### 3.4 Latest Stories
- Grid 3 kolom, 3 blog post terbaru (`status = published`)
- Editorial blog card (lihat `04-design-system.md` §3.5)
- Click → `/blog/{slug}`

### 3.5 CTA Section
- Background: accent-bg (red tint)
- Headline: "Butuh konsultasi alat fitness?"
- Subtitle: "Tim kami siap membantu..."
- Button: "Hubungi via WhatsApp" (wa.me link)

## 4. Data Source
- Banners: `banners` where `is_active = true`, order `sort_order`
- Categories: `categories` where `is_featured = true`
- Products: `products` where `is_featured = true` AND `status = published`, limit 8
- Blog: `blog_posts` where `status = published`, order `published_at desc`, limit 3

## 5. Caching
- Cache homepage data 60 menit, invalidate saat product/banner/blog update
- Key: `homepage:data`

## 6. SEO
- Title: dari settings (`seo.homepage_title`)
- Meta description: dari settings
- og:image: hero banner image
- JSON-LD: Organization schema

## 7. Acceptance Criteria
1. Hero banner Swiper berfungsi (auto-play, navigation)
2. Featured products tampil (max 8)
3. Shop by category tampil (featured categories)
4. Latest stories tampil (3 post)
5. CTA WhatsApp link benar
6. Dark/light mode toggle berfungsi
7. Language toggle ID/EN berfungsi
8. Load < 3 detik
9. Responsive mobile/desktop
10. SEO meta lengkap