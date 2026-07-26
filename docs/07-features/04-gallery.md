# LeaderHub — Feature Spec: Gallery

> **Priority**: B | **App**: Website | **Status**: Draft

## 1. Tujuan
Menampilkan gallery foto produk/installasi/event sebagai bukti kualitas dan
portofolio.

## 2. Halaman (`/gallery`)
```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
├─────────────────────────────────────────────┤
│ H1: "GALLERY"                                │
│ Breadcrumb: Home / Gallery                   │
├─────────────────────────────────────────────┤
│ ALBUM FILTER (pills)                         │
│ [All] [Instalasi] [Event] [Showroom] ...    │
├─────────────────────────────────────────────┤
│ MASONRY GRID                                 │
│ ┌──┐ ┌────┐ ┌──┐                            │
│ │  │ │    │ │  │                            │
│ │  │ └────┘ │  │                            │
│ └──┘ ┌──┐   └──┘                            │
│ ┌────┤ │  ┌────┐                            │
│ │    │ │  │    │                            │
│ └────┘ └──┘    │                            │
│              ──┘                             │
├─────────────────────────────────────────────┤
│ FOOTER                                       │
└─────────────────────────────────────────────┘
```

## 3. Komponen

### 3.1 Album Filter
- Pills (badge style), active = accent bg
- "All" + list `gallery_albums`
- Click → filter grid (client-side, no reload)
- URL param: `?album={slug}` (shareable)

### 3.2 Masonry Grid
- CSS columns (3 desktop, 2 tablet, 1 mobile)
- Items: `gallery_items` join `gallery_albums`
- Image: WebP, lazy load, responsive srcset
- Grayscale 20% default, 0% on hover
- Click → GLightbox lightbox (zoom, prev/next, caption)

### 3.3 Lightbox (GLightbox)
- Full screen, dark bg
- Zoom, prev/next, caption (alt/caption)
- Touch swipe (mobile)
- Keyboard navigation (arrows, esc)

## 4. Admin (Filament — website admin)
- GalleryAlbumResource: CRUD album (name, slug, description, cover, sort)
- GalleryItemResource: CRUD item (album, path, alt, caption, sort)
- Upload image → storage/app/public/gallery/{album_slug}/{file}
- Auto-generate WebP + thumbnail

## 5. Data Source
- `gallery_albums` + `gallery_items` eager load
- Cache 60 menit, invalidate saat gallery update

## 6. SEO
- Title: "Gallery - LeaderHub"
- og:image: first item
- Sitemap: gallery URL

## 7. Acceptance Criteria
1. Album filter berfungsi (pills, client-side)
2. Masonry grid responsif
3. Lightbox berfungsi (zoom, prev/next, caption)
4. Lazy load berfungsi
5. Grayscale→color hover
6. Admin CRUD album & item
7. Image upload → WebP + thumbnail
8. Responsive mobile/desktop
9. Load < 2 detik