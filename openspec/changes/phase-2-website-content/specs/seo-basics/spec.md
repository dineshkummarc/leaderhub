## ADDED Requirements

### Requirement: Sitemap
`GET /sitemap.xml` SHALL return a valid XML sitemap containing URLs for: homepage, products index, gallery index, all published products, all published pages. Each URL SHALL have `priority` and `lastmod`.

#### Scenario: Sitemap includes all published products
- **WHEN** developer accesses `/sitemap.xml`
- **THEN** the XML MUST contain a `<url>` entry for every published product

### Requirement: Robots.txt
A `public/robots.txt` file SHALL exist with `User-agent: *`, `Allow: /`, and `Sitemap: https://leaderhub.id/sitemap.xml`.

#### Scenario: Robots.txt is accessible
- **WHEN** user accesses `/robots.txt`
- **THEN** the file MUST be served with the correct content including the sitemap URL

### Requirement: JSON-LD Organization
The base layout SHALL include JSON-LD Organization schema with: name (LeaderHub), url, logo, contactPoint (telephone, contactType sales).

#### Scenario: Organization schema is on every page
- **WHEN** developer inspects any page's HTML source
- **THEN** a JSON-LD script tag with `@type: Organization` MUST be present

### Requirement: Meta Tags
Each page SHALL have: `<title>` (page-specific or default), `<meta name="description">` (from page or settings seo_description). Product pages SHALL use product-specific SEO fields if set.

#### Scenario: Product page has specific title
- **WHEN** user views a product detail page
- **THEN** the `<title>` tag MUST contain the product name
