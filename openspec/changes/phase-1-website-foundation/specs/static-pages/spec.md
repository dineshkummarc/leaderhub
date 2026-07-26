# Spec: static-pages

## Requirements

### REQ-001: Pages Table
A `pages` table MUST exist with: `id`, `slug` (unique), `title`, `content` (LONGTEXT), `title_en` (nullable), `content_en` (nullable), `seo_title`, `seo_description`, `seo_image`, `status` (draft/published), `sort_order`.

### REQ-002: Page Model
The `Page` model MUST be in `core/src/Models/` with a global scope `published` that filters `status = 'published'`. The `resolveRouteBinding` method MUST bypass the scope so admins can preview drafts.

### REQ-003: Page Controller
A `PageController@show($slug)` MUST render `page/show.blade.php` with the page data. When locale is `en` and `title_en`/`content_en` are not null, the English version MUST be displayed.

### REQ-004: Route
A route `GET /{slug}` MUST be registered at the BOTTOM of `routes/web.php` (after all other routes) to avoid overriding. It MUST only match slugs matching `[a-z0-9-]+`.

### REQ-005: Filament Resource
A `PageResource` MUST exist with: form (slug, title, title_en, RichEditor content, RichEditor content_en, SEO section, status, sort_order), table (slug, title, status badge).

### REQ-006: Seeder
A `PageSeeder` MUST create 4 pages: about, contact, terms, privacy — all with status `published`.

### REQ-007: Pages Accessible
After seeding, `/about`, `/contact`, `/terms`, `/privacy` MUST return 200 with the page content rendered.