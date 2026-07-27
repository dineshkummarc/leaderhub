## ADDED Requirements

### Requirement: Pages Table
A `pages` table SHALL exist with: `id`, `slug` (unique), `title`, `content` (LONGTEXT), `title_en` (nullable), `content_en` (nullable), `seo_title`, `seo_description`, `seo_image`, `status` (draft/published), `sort_order`.

#### Scenario: Pages table exists
- **WHEN** developer runs `php artisan migrate`
- **THEN** a `pages` table with all specified columns MUST be created

### Requirement: Page Model Global Scope
The `Page` model SHALL have a global scope `published` that filters `status = 'published'`. The `resolveRouteBinding` method SHALL bypass the scope so admins can preview drafts.

#### Scenario: Draft page is hidden from frontend
- **WHEN** a page with status `draft` exists
- **THEN** `Page::all()` MUST NOT include that page (global scope filters it)

#### Scenario: Admin can access draft page
- **WHEN** admin navigates to `/{slug}` where the page has status `draft`
- **THEN** the page MUST be accessible (resolveRouteBinding bypasses the scope)

### Requirement: Page Controller
A `PageController@show($slug)` SHALL render `page/show.blade.php` with the page data. When locale is `en` and `title_en`/`content_en` are not null, the English version SHALL be displayed.

#### Scenario: English page content is displayed when locale is en
- **WHEN** session locale is `en` and a page has `title_en` and `content_en` set
- **THEN** the rendered page MUST display `title_en` as the heading and `content_en` as the body

### Requirement: Route Registration
A route `GET /{slug}` SHALL be registered at the BOTTOM of `routes/web.php` (after all other routes) to avoid overriding. It SHALL only match slugs matching `[a-z0-9-]+`.

#### Scenario: Blog route is not overridden by page route
- **WHEN** user navigates to `/blog/post-1`
- **THEN** the blog detail page MUST load (not the page controller with slug "blog")

### Requirement: Filament Resource
A `PageResource` SHALL exist with: form (slug, title, title_en, RichEditor content, RichEditor content_en, SEO section, status, sort_order), table (slug, title, status badge).

#### Scenario: Admin can edit a page
- **WHEN** admin navigates to `/admin/pages/{record}/edit`
- **THEN** the edit form MUST be displayed with all page fields

### Requirement: Seeder
A `PageSeeder` SHALL create 4 pages: about, contact, terms, privacy — all with status `published`.

#### Scenario: All 4 pages are seeded
- **WHEN** developer runs `php artisan db:seed`
- **THEN** pages with slugs `about`, `contact`, `terms`, `privacy` MUST exist with status `published`

### Requirement: Pages Accessible
After seeding, `/about`, `/contact`, `/terms`, `/privacy` SHALL return HTTP 200 with the page content rendered.

#### Scenario: About page is accessible
- **WHEN** user navigates to `/about`
- **THEN** the response MUST be HTTP 200 with the page title and content displayed
