# Design — Phase 0: Monorepo Setup

## Context
Lihat `docs/02-architecture.md` untuk arsitektur lengkap dan `docs/tasks/phase-0-monorepo-setup.md` untuk step-by-step detail.

## Approach

### Monorepo Structure
```
leaderhub/
├── core/                    ← shared package (leaderhub/core)
├── packages/design-tokens/ ← @leaderhub/design-tokens
├── apps/
│   ├── website/             ← Laravel 12 + Vue + Filament 5
│   ├── hub/                 ← Laravel 12 + Filament 5 (LeaderP)
│   └── gym/                ← Laravel 12 + Filament 5 + tenancy (LeadGym)
├── storage/                 ← shared storage (symlinked)
├── pnpm-workspace.yaml
└── composer.json            ← path repositories
```

### Core Package
- Composer package, PSR-4 `Leaderhub\Core\`
- Berisi: Models, Services, Enums, Traits, Contracts, Migrations, Seeders
- Di-require oleh ketiga app via path repository
- CoreServiceProvider load migrations dari `core/database/migrations/`

### Design Tokens
- Style Dictionary build dari `tokens.json` → `dist/tokens.css`, `tokens.js`, `filament-theme.php`
- Dipakai: website (Tailwind config), hub & gym (Filament theme + CSS variables)

### Shared Database
- Satu MySQL database `leaderhub_db` untuk ketiga app
- Migrations di core, dijalankan tiap app via `php artisan migrate --path=../../core/database/migrations`

### Shared Storage
- Root `storage/app/public/` di-symlink ke tiap app `storage/app/public/`
- Upload dari satu app bisa diakses app lain

## Models / Tables Affected
- `tests` (sementara, untuk validasi core accessible) — akan di-drop di phase 1

## Risks
- **Path repository symlink**: composer path repo pakai symlink, pastikan `composer update` resolve dengan benar
- **stancl/tenancy setup**: gymie fork perlu adaptasi ke tenancy single-DB mode

## Dependencies
- PHP 8.2+, Composer 2.x, Node 20+, pnpm 9.x, MySQL 8.0