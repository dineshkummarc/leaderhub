# Design — Phase 1: Website Foundation

## Context
Lihat `docs/04-design-system.md` (design tokens) dan `docs/tasks/phase-1-website-foundation.md` (step-by-step).

## Approach

### Tailwind 4 + Design Tokens
- `vite.config.js` pakai `@tailwindcss/vite` plugin
- `resources/css/app.css` import `tokens.css` + `@import "tailwindcss"` + `@theme` block
- Google Fonts: Inter (400-800)

### Migrations (di core)
- `users` — extend default Laravel users + `role`, `is_active`, `last_login_at`
- `settings` — key-value grouped, unique `(group, key)`, `type` (text/textarea/boolean/number/json/image), `is_translatable`
- `pages` — slug, title, content, `title_en`/`content_en` (multi-bahasa), SEO fields, status, sort_order
- `translations` — locale, namespace, key, value; unique `(locale, namespace, key)`
- `categories` — self-referencing parent_id, slug, name, `name_en`, is_featured
- `brands` — slug, name, logo, description

### Models (di core)
- `User` — Authenticatable, role-based
- `Setting` — static `get(key, group, default)` dan `set(key, value, group)`
- `Page` — global scope `published`, `resolveRouteBinding` tanpa scope (admin bisa edit draft)
- `Translation` — static `t(locale, namespace, key, default)`
- `Category` — self-referencing (parent/children)
- `Brand`

### Layout
- `layouts/app.blade.php` — html class dark/light, meta, CSRF, Vite, JSON-LD Organization
- `partials/header.blade.php` — logo, nav, theme toggle, lang toggle, inquiry count
- `partials/footer.blade.php` — 4 kolom (brand, nav, company, contact)
- `partials/inquiry-bar.blade.php` — fixed bottom, hidden jika cart kosong

### Toggle Logic
- Dark mode: `POST /api/theme` → session `theme`, html class `dark`
- Language: `POST /api/lang` → session `locale` (id/en), reload page
- Middleware `SetLocale` set `app()->setLocale()` dari session

### Filament Admin
- Panel ID `admin`, path `/admin`
- Colors dari design tokens (primary `#DC2626`)
- Custom theme CSS (Inter, radius 10px, focus ring)
- Shield generate roles & policies

### Settings Resource
- Filament `SettingResource` — form (group, key, type, value, is_translatable), table (group badge, key, type, value)

### Static Pages
- `PageController@show($slug)` — render dari DB, support multi-bahasa (title_en/content_en)
- Route `/{slug}` paling bawah (tidak override route lain)
- `PageResource` Filament — CRUD dengan RichEditor, SEO section, status

## Models / Tables Affected
- `users`, `settings`, `pages`, `translations`, `categories`, `brands`

## Risks
- **Route `/{slug}` override**: pastikan ditaruh paling bawah di `routes/web.php`
- **Filament auth vs website auth**: pakai satu tabel `users`, Filament guard default

## Dependencies
- Phase 0 selesai (monorepo, core, design tokens)