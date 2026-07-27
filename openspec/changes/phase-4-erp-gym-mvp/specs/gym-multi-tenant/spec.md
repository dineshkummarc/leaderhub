## ADDED Requirements

### Requirement: BelongsToTenant Trait
A `BelongsToTenant` trait SHALL exist in `core/src/Traits/`. It SHALL add a global scope filtering by `tenant_id` from `app('currentTenant')` and auto-set `tenant_id` on model creation.

#### Scenario: Tenant A cannot see Tenant B data
- **WHEN** Tenant A is the current tenant and Tenant B has members
- **THEN** `Member::all()` MUST only return Tenant A's members (not Tenant B's)

#### Scenario: New record auto-sets tenant_id
- **WHEN** a new Member is created while Tenant A is current
- **THEN** the member's tenant_id MUST be automatically set to Tenant A's id

### Requirement: Tenant Resolution Middleware
A `ResolveTenant` middleware SHALL extract the subdomain from the request host, find the tenant by slug, and bind it to the container as `currentTenant`. If not found, abort 404.

#### Scenario: Valid tenant subdomain resolves
- **WHEN** request host is `demo.gym.leaderhub.test`
- **THEN** the tenant with slug `demo` MUST be bound as currentTenant

#### Scenario: Invalid tenant subdomain returns 404
- **WHEN** request host is `nonexistent.gym.leaderhub.test`
- **THEN** the server MUST return HTTP 404

### Requirement: Gym Users Separate from Users
Gym users SHALL authenticate via `gym_users` table (separate from `users` table). Unique constraint on `(tenant_id, email)`.

#### Scenario: Same email in different tenants
- **WHEN** Tenant A has gym_user with email `owner@gym.com`
- **THEN** Tenant B MUST be able to create a gym_user with the same email `owner@gym.com` (unique is per-tenant)
