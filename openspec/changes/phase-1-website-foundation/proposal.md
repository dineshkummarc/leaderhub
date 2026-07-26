## Why

Setelah monorepo siap (Phase 0), website perlu fondasi: layout Modern Premium, design system ter-apply, autentikasi admin, pengaturan situs, dan halaman statis. Tanpa fondasi ini, konten produk/gallery/blog (Phase 2-3) tidak punya tempat untuk ditampilkan. Phase 1 memastikan website bisa diakses publik dan admin bisa login untuk mengelola konten.

## What Changes

- Setup Tailwind 4 dengan design tokens LeaderHub (Modern Premium)
- Buat migrations di core: `users`, `settings`, `pages`, `translations`, `categories`, `brands`
- Buat Models di core: User, Setting, Page, Translation, Category, Brand
- Buat base layout: header (nav + dark/light toggle + lang toggle + inquiry count), footer, inquiry bar placeholder
- Implement dark mode toggle (session + localStorage) dan language toggle ID/EN (session)
- Setup Filament panel admin (`/admin`) dengan Shield roles
- Settings module (CRUD key-value grouped) via Filament
- Seeder: super_admin, content_admin, default settings
- Static pages (About, Contact, Terms, Privacy) — CMS managed dari DB

## Capabilities

### New Capabilities
- `website-layout`: Layout Modern Premium dengan header, footer, dark/light toggle, language toggle
- `admin-auth`: Filament panel admin dengan autentikasi dan role-based access (Shield)
- `settings-management`: CRUD settings key-value grouped via Filament
- `static-pages`: Halaman statis CMS-managed (About, Contact, Terms, Privacy)

### Modified Capabilities
<!-- None — phase awal, belum ada spec existing -->

## Impact

- **Code**: `apps/website/` (controllers, views, Filament resources), `core/` (migrations, models)
- **Database**: Tabel `users`, `settings`, `pages`, `translations`, `categories`, `brands`
- **Dependencies**: Tailwind 4, Filament 5, Shield

**Non-goals**:
- Tidak membangun homepage/catalog/gallery (Phase 2)
- Tidak membangun blog/inquiry (Phase 3)
- Tidak implement AI/SEO advanced (Phase 5)

**Detail step-by-step**: `docs/tasks/phase-1-website-foundation.md`