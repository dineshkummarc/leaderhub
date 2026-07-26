# Spec: shared-core

## Requirements

### REQ-001: Package Definition
`core/composer.json` MUST define package name `leaderhub/core` with PSR-4 autoload mapping `Leaderhub\Core\` to `src/`.

### REQ-002: CoreServiceProvider
`core/src/CoreServiceProvider.php` MUST exist and load migrations from `core/database/migrations/` via `loadMigrationsFrom()`.

### REQ-003: Required by All Apps
All three apps (`apps/website`, `apps/hub`, `apps/gym`) MUST require `leaderhub/core` in their `composer.json` and register `CoreServiceProvider` in their providers.

### REQ-004: Models Location
All shared Eloquent models MUST be defined in `core/src/Models/` under namespace `Leaderhub\Core\Models`.

### REQ-005: Migrations Location
All shared database migrations MUST be located in `core/database/migrations/` with timestamp-prefixed filenames. Apps run them via `php artisan migrate --path=../../core/database/migrations`.

### REQ-006: Services Location
Shared business logic services (e.g., InquiryService, LicenseService) MUST be defined in `core/src/Services/` under namespace `Leaderhub\Core\Services`.