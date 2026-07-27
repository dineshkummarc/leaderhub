## ADDED Requirements

### Requirement: BreadcrumbList JSON-LD
All pages except homepage SHALL include BreadcrumbList JSON-LD schema with at least Home and current page.

#### Scenario: Product detail has breadcrumb schema
- **WHEN** user views a product detail page
- **THEN** BreadcrumbList JSON-LD MUST include Home > Products > Category > Product

### Requirement: Canonical URL
Every page SHALL have a canonical link tag.

#### Scenario: Canonical tag is present
- **WHEN** developer inspects any page HTML
- **THEN** a `<link rel="canonical">` tag MUST be present in the head

### Requirement: Open Graph Tags
Every page SHALL have Open Graph meta tags: og:title, og:description, og:image, og:url, og:type.

#### Scenario: Product page has og:type product
- **WHEN** developer inspects a product detail page
- **THEN** the og:type meta tag MUST be set to product
