# LeaderHub — Feature Spec: Blog

> **Priority**: B | **App**: Website | **Status**: Draft

## 1. Tujuan
Menampilkan artikel fitness/tips/review untuk engagement & SEO.

## 2. Halaman

### 2.1 Blog Listing (`/blog`)
```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
├─────────────────────────────────────────────┤
│ H1: "BLOG" / "STORIES"                       │
│ Breadcrumb: Home / Blog                      │
├─────────────────────────────────────────────┤
│ FILTER BAR                                   │
│ Category: [All] [Tips] [Review] [News]      │
│ Tag: [strength] [cardio] [nutrition]        │
│ Search: [_______________]                    │
├─────────────────────────────────────────────┤
│ EDITORIAL GRID (3 col)                       │
│ ┌──────┐ ┌──────┐ ┌──────┐                  │
│ │ img  │ │ img  │ │ img  │                  │
│ │ cat  │ │ cat  │ │ cat  │                  │
│ │ title│ │ title│ │ title│                  │
│ │ meta │ │ meta │ │ meta │                  │
│ └──────┘ └──────┘ └──────┘                  │
│ ...                                          │
│ PAGINATION                                   │
├─────────────────────────────────────────────┤
│ FOOTER                                       │
└─────────────────────────────────────────────┘
```

### 2.2 Blog Detail (`/blog/{slug}`)
```
┌─────────────────────────────────────────────┐
│ HEADER                                       │
├─────────────────────────────────────────────┤
│ Breadcrumb: Home / Blog / Category / Title   │
├─────────────────────────────────────────────┤
│ FEATURED IMAGE (full-width, 16:10)           │
├─────────────────────────────────────────────┤
│ CATEGORY BADGE                               │
│ H1: Title (weight 700)                       │
│ Meta: Author • Date • Reading time           │
├─────────────────────────────────────────────┤
│ CONTENT (rich text, max-width 896px)         │
│ - Headings, paragraphs, lists, images       │
│ - Code blocks (mono)                         │
│ - Blockquote                                 │
├─────────────────────────────────────────────┤
│ TAGS                                         │
│ [tag1] [tag2] [tag3]                         │
├─────────────────────────────────────────────┤
│ AUTHOR BOX                                   │
│ [avatar] Name — Bio                          │
├─────────────────────────────────────────────┤
│ RELATED POSTS (3, same category)             │
├─────────────────────────────────────────────┤
│ FOOTER                                       │
└─────────────────────────────────────────────┘
```

## 3. Komponen

### 3.1 Editorial Blog Card
- Image 16:10, radius-lg, hover lift -2px + shadow-md
- Category: label 13px muted
- Title: weight 600, 1.125rem, 2-line clamp, letter-spacing -0.015em
- Meta: author, date, reading time (weight 500, muted)
- Click → `/blog/{slug}`

### 3.2 Filter
- Category: pills (badge style)
- Tag: pills
- Search: input (title, content)
- URL param: `?category={slug}&tag={slug}&q={query}`

### 3.3 Detail
- Featured image full-width (max container)
- Content max-width 896px (readable)
- Reading time auto-calculate (words / 200)
- Tags clickable → filter
- Author box (avatar, name, bio)
- Related: same category, exclude current, limit 3

## 4. Admin (Filament — website admin)
- BlogCategoryResource: CRUD category
- BlogTagResource: CRUD tag
- BlogPostResource:
  - Title, slug (auto from title)
  - Category (select)
  - Tags (multi-select)
  - Excerpt (textarea)
  - Content (rich text editor — TipTap)
  - Featured image (upload)
  - Author (select user)
  - Status (draft, published, archived)
  - Published at (datetime)
  - SEO: title, description, image (override)

## 5. Rich Text Editor
- TipTap (Filament plugin) atau Trix
- Support: headings, bold, italic, lists, links, images, blockquote, code
- Image upload → storage/app/public/blog/{post_id}/
- Auto-generate WebP

## 6. Data Source
- Listing: `blog_posts` WHERE `status = published` AND `published_at <= now()` + filter + paginate (9 per page)
- Detail: `blog_posts` WHERE `slug = ?` AND `status = published` + category + tags + author
- Related: same category, exclude current, limit 3

## 7. Caching
- Listing: 30 menit, invalidate saat blog update
- Detail: 30 menit per slug
- Keys: `blog:list:{hash}`, `blog:detail:{slug}`

## 8. SEO
- Listing: title "Blog - LeaderHub", meta description, canonical
- Detail: title "{post.title} - LeaderHub", meta dari post (seo_title/description), og:image
- JSON-LD: Article schema (headline, author, datePublished, image)
- Breadcrumb JSON-LD
- Sitemap: semua published post URL

## 9. Acceptance Criteria
1. Listing tampil 9 post per page
2. Filter category & tag berfungsi
3. Search berfungsi
4. Detail: featured image, content, tags, author, related
5. Reading time tampil
6. Rich text editor di admin (TipTap)
7. Image upload di content
8. SEO meta + JSON-LD lengkap
9. Sitemap include blog URLs
10. Responsive mobile/desktop
11. Load < 2 detik (cached)