# Spec: admin-auth

## Requirements

### REQ-001: Filament Panel
A Filament panel with ID `admin` MUST be accessible at `/admin`. It MUST have a login page at `/admin/login`.

### REQ-002: Theme
The admin panel MUST use Modern Premium theme: primary color `#DC2626`, Inter font, radius 10px, focus ring `rgba(220,38,38,0.12)`, no uppercase table headers.

### REQ-003: Roles
The system MUST define roles via Filament Shield: `super_admin`, `content_admin`, `sales_admin`, `inventory_admin`, `accounting_admin`, `hr_admin`, `company_admin`, `license_admin`.

### REQ-004: User Model
The `users` table MUST have a `role` column (VARCHAR 50) storing the role name. The `User` model MUST be in `core/src/Models/` and extend `Illuminate\Foundation\Auth\User`.

### REQ-005: Seeder
A `UserSeeder` MUST create: `admin@leaderhub.id` (super_admin) and `content@leaderhub.id` (content_admin), both with password `password` and `is_active = true`.

### REQ-006: Login Works
After seeding, logging in at `/admin/login` with `admin@leaderhub.id` / `password` MUST succeed and show the Filament dashboard.