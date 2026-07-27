## ADDED Requirements

### Requirement: Base Layout
A base layout file `layouts/app.blade.php` SHALL exist with: HTML lang attribute from session locale, dark class on `<html>` from session theme, CSRF meta tag, Google Fonts (Inter) link, Vite asset includes, and JSON-LD Organization schema.

#### Scenario: Layout renders with correct attributes
- **WHEN** a page using `layouts/app.blade.php` is rendered
- **THEN** the HTML MUST have `lang` attribute set from session locale, `class="dark"` if theme is dark, and a CSRF token meta tag

### Requirement: Header
The header SHALL contain: logo (LeaderHub wordmark with accent dot), navigation links (Produk, Gallery, Blog, Tentang, Kontak), dark/light toggle button, language toggle button (ID/EN), and inquiry cart icon with count badge.

#### Scenario: Header displays all elements
- **WHEN** the header is rendered
- **THEN** it MUST show the logo, 5 navigation links, theme toggle button, language toggle button, and inquiry cart icon

### Requirement: Footer
The footer SHALL contain 4 columns: brand with tagline, navigation links, company links, and contact info (email and phone from settings).

#### Scenario: Footer displays contact info from settings
- **WHEN** the footer is rendered
- **THEN** the contact column MUST show email and phone values from the `settings` table (group: contact)

### Requirement: Dark Mode Toggle
Clicking the theme toggle SHALL POST to `/api/theme` which toggles session `theme` between `light` and `dark`. The `<html>` element SHALL get class `dark` when theme is dark. The preference SHALL persist across page reloads via session.

#### Scenario: Dark mode persists after refresh
- **WHEN** user clicks the theme toggle button to switch to dark mode
- **AND** user refreshes the page
- **THEN** the page MUST still render in dark mode (html has class "dark")

### Requirement: Language Toggle
Clicking the language toggle SHALL POST to `/api/lang` which toggles session `locale` between `id` and `en`. The page SHALL reload after toggle. A middleware `SetLocale` SHALL set `app()->setLocale()` from session on every request.

#### Scenario: Language toggle switches to English
- **WHEN** user clicks the language toggle button (currently showing "EN" meaning current is ID)
- **THEN** the page MUST reload with session locale set to "en"

### Requirement: Modern Premium Design
The layout SHALL use the Modern Premium design system: neutral-first colors (slate-based), accent red `#DC2626` used sparingly, Inter font, radius 10-14px, soft shadows, no uppercase headings, no grayscale image filters.

#### Scenario: No uppercase headings in layout
- **WHEN** developer inspects the rendered HTML
- **THEN** heading elements MUST NOT have CSS `text-transform: uppercase` applied

### Requirement: Responsive
The layout SHALL be responsive: mobile (320px), tablet (768px), desktop (1024px+). Navigation SHALL collapse on mobile screens.

#### Scenario: Navigation collapses on mobile
- **WHEN** the page is viewed at 320px width
- **THEN** the navigation links SHALL be hidden or collapsed into a mobile menu
