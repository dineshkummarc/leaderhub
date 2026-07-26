# Tasks — Phase 0: Monorepo Setup

> **Detail step-by-step**: `docs/tasks/phase-0-monorepo-setup.md`
> **Estimasi**: 2 hari | **Prasyarat**: PHP 8.2+, Composer, Node 20+, pnpm, MySQL

## Tasks

### T01: Inisialisasi struktur folder & git
- [x] Buat root folder, `git init`, `.gitignore`, root `composer.json` (path repos), `package.json` (pnpm scripts), `pnpm-workspace.yaml`, `README.md`
- [x] Buat folder structure: `core/src/{Models,Services,Enums,Traits,Contracts}`, `core/database/{migrations,seeders,factories}`, `packages/design-tokens/dist`, `apps/`, `storage/app/public`
- **Validation**: `git status` clean, folder structure sesuai
- **Effort**: 1h

### T02: Setup core package
- [x] `core/composer.json` (PSR-4 `Leaderhub\Core\`), `CoreServiceProvider.php`, test model `Test.php`, migration `create_tests_table.php`
- [x] `composer install` di core
- **Validation**: `php -r "require 'vendor/autoload.php'; echo Leaderhub\Core\Models\Test::class;"` output class name
- **Effort**: 1h

### T03: Setup design-tokens package
- [x] `package.json`, `tokens.json` (sumber), `build.js` (Style Dictionary), install & build
- **Validation**: `pnpm build` menghasilkan `dist/tokens.css`, `dist/tokens.js`, `dist/filament-theme.php`
- **Effort**: 2h

### T04: Setup apps/website (Laravel 12 + Filament 5)
- [x] `composer create-project laravel/laravel`, install Filament 5, require core, setup Tailwind 4 + design tokens, configure `.env`, migrate, serve
- **Validation**: `php artisan serve --port=8000` load, `Leaderhub\Core\Models\Test::all()` berfungsi
- **Effort**: 2h

### T05: Setup apps/hub (Laravel 12 + Filament 5 + Shield)
- [x] `composer create-project`, install Filament 5 + Shield, require core, configure `.env`, serve
- **Validation**: `php artisan serve --port=8001` load, `/hub/login` accessible
- **Effort**: 2h

### T06: Setup apps/gym (fork laravel-gymie + stancl/tenancy)
- [x] Copy laravel-gymie ke `apps/gym/`, install stancl/tenancy 3.x, require core, configure `.env`, serve
- **Validation**: `php artisan serve --port=8002` load, `stancl/tenancy` ter-install
- **Effort**: 3h

### T07: Configure pnpm workspace & root composer
- [x] Pastikan tiap app punya `package.json` dengan name unik, `pnpm install` di root, `pnpm build:tokens`
- **Validation**: `pnpm install` berhasil, `pnpm -r list` menampilkan semua workspace
- **Effort**: 1h

### T08: Test core model accessible dari ketiga app
- [x] Migrate core di tiap app, insert test record dari tiap app via tinker, verify data shared
- **Validation**: Insert dari website → terlihat di hub & gym (3 record di `tests`)
- **Effort**: 1h

### T09: Setup shared storage symlink
- [x] Buat root `storage/app/public`, symlink ke tiap app, test upload cross-app
- **Validation**: File upload dari website bisa dibaca di hub & gym
- **Effort**: 1h

### T10: Initial git commit
- [x] `git add -A`, review (no `.env`), commit
- **Validation**: `git log` ada 1 commit, `git status` clean
- **Effort**: 0.5h

## Final Validation
- [x] Website load di `http://localhost:8000`
- [x] Hub load di `http://localhost:8001/hub/login`
- [x] Gym load di `http://localhost:8002`
- [x] Core model accessible dari ketiga app
- [x] Design tokens built (`dist/` terisi)
- [x] `pnpm install` berhasil
- [x] Git clean