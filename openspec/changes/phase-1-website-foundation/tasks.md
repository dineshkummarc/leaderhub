# Tasks — Phase 1: Website Foundation

> **Detail step-by-step**: `docs/tasks/phase-1-website-foundation.md`
> **Estimasi**: 2 minggu | **Prasyarat**: Phase 0 selesai

## Tasks

### T01: Setup Tailwind 4 + design tokens
- [x] Install tailwindcss + @tailwindcss/vite, configure vite.config.js, import tokens.css + @theme block
- [x] **Validation**: `pnpm run build` berhasil, CSS variables ter-load
- **Effort**: 2h

### T02: Migrations — users, settings, pages, translations, categories, brands
- [x] Buat 6 migrations di core + 6 models (User, Setting, Page, Translation, Category, Brand)
- [x] **Validation**: `php artisan migrate` berhasil, FK rapi, models accessible via tinker
- **Effort**: 4h

### T03: Base layout & partials
- [x] `layouts/app.blade.php`, `partials/header.blade.php`, `partials/footer.blade.php`, `partials/inquiry-bar.blade.php`
- [x] **Validation**: Homepage (dummy) load dengan header & footer, tidak ada error Blade
- **Effort**: 4h

### T04: Dark mode & language toggle
- [x] Middleware `SetLocale`, `ThemeController` (toggleTheme, toggleLang), routes `/api/theme` & `/api/lang`, JS toggle
- [x] **Validation**: Toggle dark/light persist, toggle ID/EN reload page, no 419 CSRF error
- **Effort**: 3h

### T05: Admin auth (Filament panel website)
- [x] Configure AdminPanelProvider (theme, colors, font), install Shield, generate roles & policies
- [x] **Validation**: `/admin/login` accessible, login dengan seeder user berhasil, theme Modern Premium, save credentials di folder credentials
- **Effort**: 3h

### T06: Settings module (Filament resource)
- [x] `SettingResource` — form (group, key, type, value, is_translatable), table, filter by group
- [x] **Validation**: CRUD settings berfungsi, filter berfungsi
- **Effort**: 2h

### T07: Seeders (Users, Roles, Settings)
- [x] `UserSeeder` (super_admin, content_admin), `SettingSeeder` (11 default settings), register di DatabaseSeeder
- [x] **Validation**: `php artisan db:seed` berhasil, login admin berhasil, settings ada 11 record
- **Effort**: 2h

### T08: Static pages CMS
- [x] `PageController`, `page/show.blade.php`, `PageResource` (Filament), `PageSeeder`, route `/{slug}` di bawah
- [x] **Validation**: `/about`, `/contact`, `/terms`, `/privacy` load dari DB, admin bisa edit, route tidak override
- **Effort**: 4h

## Final Validation
- [x] Homepage load dengan layout Modern Premium
- [x] `/admin/login` berfungsi
- [x] Settings CRUD berfungsi
- [x] Static pages load dari DB
- [x] Dark mode toggle persist
- [x] Language toggle berfungsi
- [x] Migrations jalan tanpa error
- [x] Seeder berhasil
- [x] Tidak ada error PHP, FK rapi