## ADDED Requirements

### Requirement: Folder Structure
The repository SHALL have the following top-level structure: `core/` (shared composer package), `packages/design-tokens/` (design tokens package), `apps/website/` (public website app), `apps/hub/` (LeaderP ERP app), `apps/gym/` (LeadGym app), `storage/` (shared storage folder).

#### Scenario: Verify folder structure exists
- **WHEN** developer runs `ls` in the repository root
- **THEN** the output MUST include directories: `core`, `packages`, `apps`, `storage`

### Requirement: Git Initialized
The repository SHALL be initialized with git and have a `.gitignore` file that excludes `node_modules/`, `vendor/`, `.env` files, and `storage/app/*` (except `.gitkeep` files).

#### Scenario: Git status is clean after setup
- **WHEN** developer runs `git status` after Phase 0 setup
- **THEN** the output MUST show a clean working tree (no untracked files that should be ignored)

#### Scenario: No secrets in git
- **WHEN** developer runs `git log --name-only`
- **THEN** no `.env` files MUST appear in any commit

### Requirement: Pnpm Workspace
A `pnpm-workspace.yaml` file SHALL exist at the repository root listing `packages/*` and `apps/*` as workspace members. A root `package.json` SHALL exist with scripts for `build:tokens`, `build:all`, and `dev:website`.

#### Scenario: Pnpm install succeeds
- **WHEN** developer runs `pnpm install` in the repository root
- **THEN** the command MUST complete without errors and install all workspace dependencies

### Requirement: Composer Path Repositories
The root `composer.json` SHALL define path repositories for `./core` and `./packages/design-tokens`.

#### Scenario: Composer resolves path repositories
- **WHEN** developer runs `composer update` in any app directory
- **THEN** the `leaderhub/core` and `leaderhub/design-tokens` packages MUST resolve from the local path repositories

### Requirement: Shared Storage Symlink
Each app's `storage/app/public` SHALL be a symlink to the root `storage/app/public/` folder. Files uploaded from one app MUST be accessible from all other apps.

#### Scenario: File uploaded in website is accessible in hub
- **WHEN** a file is uploaded via the website app to `storage/app/public/test.txt`
- **THEN** the hub app MUST be able to read the same file via `Storage::disk('public')->exists('test.txt')` returning true

### Requirement: Three Apps Running
Each app SHALL be servable via `php artisan serve` on separate ports (8000 for website, 8001 for hub, 8002 for gym) without errors.

#### Scenario: Website serves on port 8000
- **WHEN** developer runs `php artisan serve --port=8000` in `apps/website`
- **THEN** `http://localhost:8000` MUST return HTTP 200 with the Laravel welcome page

#### Scenario: Hub serves on port 8001
- **WHEN** developer runs `php artisan serve --port=8001` in `apps/hub`
- **THEN** `http://localhost:8001` MUST return HTTP 200

#### Scenario: Gym serves on port 8002
- **WHEN** developer runs `php artisan serve --port=8002` in `apps/gym`
- **THEN** `http://localhost:8002` MUST return HTTP 200

### Requirement: Core Model Accessible
A model defined in `core/src/Models/` SHALL be accessible from all three apps via `php artisan tinker`. Data inserted from one app SHALL be visible from all apps because they share the same MySQL database.

#### Scenario: Data inserted in website is visible in hub
- **WHEN** a record is created via `Leaderhub\Core\Models\Test::create(['name' => 'from-website'])` in the website app
- **THEN** running `Leaderhub\Core\Models\Test::all()` in the hub app MUST return a collection containing that record
