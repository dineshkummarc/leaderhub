# Spec: seo-basics

## Requirements

### REQ-001: Sitemap
`GET /sitemap.xml` MUST return a valid XML sitemap containing URLs for: homepage, products index, gallery index, all published products, all published pages. Each URL MUST have `priority` and `lastmod`.

### REQ-002: Robots.txt
`public/robots.txt` MUST exist with `User-agent: *`, `Allow: /`, and `Sitemap: https://leaderhub.id/sitemap.xml`.

### REQ-003: JSON-LD Organization
The base layout MUST include JSON-LD Organization schema with: name (LeaderHub), url, logo, contactPoint (telephone, contactType sales).

### REQ-004: JSON-LD Product
Product detail pages MUST include JSON-LD Product schema (see product-catalog spec REQ-006).

### REQ-005: Meta Tags
Each page MUST have: `<title>` (page-specific or default), `<meta name="description">` (from page or settings seo_description). Product pages MUST use product-specific SEO fields if set.

### REQ-006: Canonical
Each page MUST have a canonical URL tag to prevent duplicate content issues.