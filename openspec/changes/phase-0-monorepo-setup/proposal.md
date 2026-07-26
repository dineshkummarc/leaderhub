## Why

LeaderHub adalah platform terintegrasi untuk distributor alat fitness yang menyatukan website, ERP, dan software gym dalam satu monorepo. Tanpa struktur monorepo yang solid di awal, ketiga aplikasi akan sulit share code (models, migrations, design tokens) yang menyebabkan duplikasi dan inkonsistensi. Phase 0 adalah fondasi agar development phase berikutnya bisa berjalan paralel dan terorganisir.

## What Changes

- Buat struktur folder monorepo `leaderhub/` dengan git
- Setup `core/` package (Composer, PSR-4 `Leaderhub\Core\`) berisi Models, Services, Migrations shared
- Setup `packages/design-tokens/` (Style Dictionary) sebagai sumber tunggal design tokens untuk semua app
- Setup `apps/website/` (Laravel 12 + Filament 5 + Vue + Tailwind 4)
- Setup `apps/hub/` (Laravel 12 + Filament 5 + Shield) untuk LeaderP ERP
- Setup `apps/gym/` (fork laravel-gymie + stancl/tenancy 3.x) untuk LeadGym multi-tenant
- Configure pnpm workspace + composer path repositories
- Setup shared storage symlink untuk upload file
- Test: core model accessible dari ketiga app via shared MySQL database

## Capabilities

### New Capabilities
- `monorepo-structure`: Struktur folder monorepo dengan core package, design tokens, dan 3 aplikasi Laravel
- `shared-core`: Composer package berisi Models, Services, Migrations yang dipakai semua app
- `design-tokens`: Package design tokens (CSS/JS/PHP) sebagai sumber tunggal styling

### Modified Capabilities
<!-- None — ini adalah setup awal, belum ada spec existing -->

## Impact

- **Code**: Root `composer.json`, `package.json`, `pnpm-workspace.yaml`, `core/`, `packages/design-tokens/`, `apps/{website,hub,gym}/`
- **Dependencies**: Laravel 12, Filament 5, stancl/tenancy, Style Dictionary, pnpm
- **Database**: MySQL `leaderhub_db` shared, tabel `tests` (validasi)
- **Deployment**: Dev env lokal (Docker/native), belum production

**Non-goals**:
- Tidak membangun fitur bisnis (itu phase 1-6)
- Tidak setup production deployment (phase 6)
- Tidak kustomisasi UI (phase 1)

**Detail step-by-step**: `docs/tasks/phase-0-monorepo-setup.md`