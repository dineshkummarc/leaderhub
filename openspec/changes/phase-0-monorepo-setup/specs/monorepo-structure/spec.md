# Spec: monorepo-structure

## Requirements

### REQ-001: Folder Structure
The repository MUST have the following top-level structure:
- `core/` — shared composer package
- `packages/design-tokens/` — design tokens package
- `apps/website/` — public website app
- `apps/hub/` — LeaderP ERP app
- `apps/gym/` — LeadGym app
- `storage/` — shared storage folder

### REQ-002: Git Initialized
The repository MUST be initialized with git and have a `.gitignore` that excludes `node_modules/`, `vendor/`, `.env`, `storage/app/*` (except `.gitkeep`).

### REQ-003: Pnpm Workspace
A `pnpm-workspace.yaml` MUST exist at root listing `packages/*` and `apps/*` as workspace members. Root `package.json` MUST have scripts for `build:tokens`, `build:all`, and `dev:website`.

### REQ-004: Composer Path Repositories
Root `composer.json` MUST define path repositories for `./core` and `./packages/design-tokens`.

### REQ-005: Shared Storage Symlink
Each app's `storage/app/public` MUST be a symlink to the root `storage/app/public/` folder. Files uploaded from one app MUST be accessible from all other apps.

### REQ-006: Three Apps Running
Each app MUST be servable via `php artisan serve` on separate ports (8000, 8001, 8002) without errors.

### REQ-007: Core Model Accessible
A model defined in `core/src/Models/` MUST be accessible from all three apps via `php artisan tinker`. Data inserted from one app MUST be visible from all apps (shared database).