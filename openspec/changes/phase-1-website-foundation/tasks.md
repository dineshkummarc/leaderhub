# Tasks — Phase 1: Website Foundation

> **Detail step-by-step**: `docs/tasks/phase-1-website-foundation.md`
> **Estimasi**: 2 minggu | **Prasyarat**: Phase 0 selesai

## Tasks

### T01: Setup Tailwind 4 + design tokens
- [ ] Install tailwindcss + @tailwindcss/vite, configure vite.config.js, import tokens.css + @theme block
- **Validation**: `pnpm run build` berhasil, CSS variables ter-load
- **Effort**: 2h

### T02: Migrations — users, settings, pages, translations, categories, brands
- [ ] Buat 6 migrations di core + 6 models (User, Setting, Page, Translation, Category, Brand)
- **Validation**: `php artisan migrate` berhasil, FK rapi, models accessible via tinker
- **Effort**: 4h

### T03: Base layout & partials
- [ ] `layouts/app.blade.php`, `partials/header.blade.php`, `partials/footer.blade.php`, `partials/inquiry-bar.blade.php`
- **Validation**: Homepage (dummy) load dengan header & footer, tidak ada error Blade
- **Effort**: 4h

### T04: Dark mode & language toggle
- [ ] Middleware `SetLocale`, `ThemeController` (toggleTheme, toggleLang), routes `/api/theme` & `/api/lang`, JS toggle
- **Validation**: Toggle dark/light persist, toggle ID/EN reload page, no 419 CSRF error
- **Effort**: 3h

### T05: Admin auth (Filament panel website)
- [ ] Configure AdminPanelProvider (theme, colors, font), install Shield, generate roles & policies
- **Validation**: `/admin/login` accessible, login dengan seeder user berhasil, theme Modern Premium
- **Effort**: 3h

### T06: Settings module (Filament resource)
- [ ] `SettingResource` — form (group, key, type, value, is_translatable), table, filter by group
- **Validation**: CRUD settings berfungsi, filter berfungsi
- **Effort**: 2h

### T07: Seeder (admin user, default settings)
- [ ] `UserSeeder` (super_admin, content_admin), `SettingSeeder` (11 default settings), register di DatabaseSeeder
- **Validation**: `php artisan db:seed` berhasil, login admin berhasil, settings ada 11 record
- **Effort**: 2h

### T08: Static pages (About, Contact, Terms, Privacy)
- [ ] `PageController`, `page/show.blade.php`, `PageResource` (Filament), `PageSeeder`, route `/{slug}` di bawah
- **Validation**: `/about`, `/contact`, `/terms`, `/privacy` load dari DB, admin bisa edit, route tidak override
- **Effort**: 4h

## Final Validation
- [ ] Homepage load dengan layout Modern Premium
- [ ] `/admin/login` berfungsi
- [ ] Settings CRUD berfungsi
- [ ] Static pages load dari DB
- [ ] Dark mode toggle persist
- [ ] Language toggle berfungsi
- [ ] Migrations jalan tanpa error
- [ ] Seeder berhasil
- [ ] Tidak ada error PHP, FK rapi