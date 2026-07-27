## ADDED Requirements

### Requirement: Filament Panel
A Filament panel with ID `admin` SHALL be accessible at path `/admin`. It SHALL have a login page at `/admin/login`.

#### Scenario: Admin login page is accessible
- **WHEN** user navigates to `/admin/login`
- **THEN** the Filament login form MUST be displayed

### Requirement: Theme
The admin panel SHALL use the Modern Premium theme: primary color `#DC2626`, Inter font, radius 10px, focus ring `rgba(220,38,38,0.12)`, no uppercase table headers.

#### Scenario: Admin panel uses accent red
- **WHEN** developer inspects the admin panel CSS
- **THEN** the primary color MUST be `#DC2626`

### Requirement: Roles
The system SHALL define roles via Filament Shield: `super_admin`, `content_admin`, `sales_admin`, `inventory_admin`, `accounting_admin`, `hr_admin`, `company_admin`, `license_admin`.

#### Scenario: All roles are defined
- **WHEN** developer runs `php artisan shield:generate --all`
- **THEN** all 8 roles MUST be created in the database

### Requirement: User Model
The `users` table SHALL have a `role` column (VARCHAR 50) storing the role name. The `User` model SHALL be in `core/src/Models/` and extend `Illuminate\Foundation\Auth\User`.

#### Scenario: User has role column
- **WHEN** developer inspects the users table schema
- **THEN** a `role` column of type VARCHAR(50) MUST exist

### Requirement: Seeder
A `UserSeeder` SHALL create: `admin@leaderhub.id` (super_admin) and `content@leaderhub.id` (content_admin), both with password `password` and `is_active = true`.

#### Scenario: Admin user is seeded
- **WHEN** developer runs `php artisan db:seed`
- **THEN** a user with email `admin@leaderhub.id`, role `super_admin`, and password `password` MUST exist in the database

### Requirement: Login Works
After seeding, logging in at `/admin/login` with `admin@leaderhub.id` / `password` SHALL succeed and show the Filament dashboard.

#### Scenario: Admin login succeeds
- **WHEN** user enters `admin@leaderhub.id` and `password` at `/admin/login`
- **THEN** the user MUST be redirected to the Filament dashboard
