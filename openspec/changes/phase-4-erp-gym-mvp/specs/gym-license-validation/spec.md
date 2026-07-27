## ADDED Requirements

### Requirement: LicenseValidatorService
A `LicenseValidatorService` SHALL exist that calls LeaderP API to validate the license key and updates the tenant's license_status and license_expires_at.

#### Scenario: Valid license sets tenant to active
- **WHEN** LicenseValidatorService validates a valid license key
- **THEN** the tenant's license_status MUST be updated to active

### Requirement: Daily Sync Command
A `gym:sync-licenses` command SHALL run daily, validating all tenants with license keys.

#### Scenario: Daily sync updates tenant status
- **WHEN** the daily sync command runs
- **THEN** all tenants with license keys MUST have their license_status updated based on the API response

### Requirement: Read-Only Mode
If tenant license_status is not active, the gym panel SHALL enter read-only mode: disable create/edit/delete, show banner, data still viewable.

#### Scenario: Expired license disables create
- **WHEN** a tenant's license_status is expired
- **THEN** all Filament resources MUST disable create, edit, and delete actions

### Requirement: Feature Tier Enforcement
The license plan's features JSON array determines which Filament resources are visible. Features not in the array SHALL hide the corresponding resource.

#### Scenario: Basic plan hides services
- **WHEN** a tenant's plan features do not include "services"
- **THEN** the ServiceResource MUST NOT be visible in the navigation
