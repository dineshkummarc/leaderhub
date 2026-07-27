## ADDED Requirements

### Requirement: Package Definition
The `core/composer.json` SHALL define package name `leaderhub/core` with PSR-4 autoload mapping `Leaderhub\Core\` to the `src/` directory.

#### Scenario: Composer install succeeds in core
- **WHEN** developer runs `composer install` in the `core/` directory
- **THEN** the command MUST complete without errors and generate a `vendor/autoload.php` file

### Requirement: CoreServiceProvider
A `CoreServiceProvider` class SHALL exist at `core/src/CoreServiceProvider.php`. It SHALL load migrations from `core/database/migrations/` via the `loadMigrationsFrom()` method in the `boot()` method.

#### Scenario: Migrations are loaded from core
- **WHEN** developer runs `php artisan migrate --path=../../core/database/migrations` in any app
- **THEN** all migrations in `core/database/migrations/` MUST be executed

### Requirement: Required by All Apps
All three apps (`apps/website`, `apps/hub`, `apps/gym`) SHALL require `leaderhub/core` in their `composer.json` and SHALL register `CoreServiceProvider` in their bootstrap providers configuration.

#### Scenario: Core model is accessible from website
- **WHEN** developer runs `php artisan tinker` in `apps/website`
- **THEN** `Leaderhub\Core\Models\Test::class` MUST return the fully qualified class name without autoloading errors

#### Scenario: Core model is accessible from hub
- **WHEN** developer runs `php artisan tinker` in `apps/hub`
- **THEN** `Leaderhub\Core\Models\Test::class` MUST return the fully qualified class name without autoloading errors

#### Scenario: Core model is accessible from gym
- **WHEN** developer runs `php artisan tinker` in `apps/gym`
- **THEN** `Leaderhub\Core\Models\Test::class` MUST return the fully qualified class name without autoloading errors

### Requirement: Models Location
All shared Eloquent models SHALL be defined in `core/src/Models/` under the namespace `Leaderhub\Core\Models`.

#### Scenario: Model namespace is correct
- **WHEN** developer inspects a model file in `core/src/Models/`
- **THEN** the file MUST declare `namespace Leaderhub\Core\Models;`

### Requirement: Migrations Location
All shared database migrations SHALL be located in `core/database/migrations/` with timestamp-prefixed filenames. Apps SHALL run them via `php artisan migrate --path=../../core/database/migrations`.

#### Scenario: Migrations run from core path
- **WHEN** developer runs `php artisan migrate --path=../../core/database/migrations --force` in any app
- **THEN** all shared tables MUST be created in the database

### Requirement: Services Location
Shared business logic services (e.g., InquiryService, LicenseService) SHALL be defined in `core/src/Services/` under the namespace `Leaderhub\Core\Services`.

#### Scenario: Service is accessible from app
- **WHEN** developer references `Leaderhub\Core\Services\InquiryService` in any app
- **THEN** the class MUST be autoloaded without errors
