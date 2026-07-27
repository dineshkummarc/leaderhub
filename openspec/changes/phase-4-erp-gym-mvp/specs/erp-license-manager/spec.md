## ADDED Requirements

### Requirement: License Key Auto-Generate
The license_key SHALL be auto-generated in format `LDR-GYM-{16 random uppercase alphanumeric characters}`. The form field SHALL be disabled (auto-generated).

#### Scenario: License key is unique and auto-generated
- **WHEN** admin creates a new license
- **THEN** a unique key in format LDR-GYM-XXXXXXXXXXXXXXXX MUST be auto-generated

### Requirement: Suspend Action
The LicenseResource SHALL have a "Suspend" action (visible when status=active) that requires a reason and updates status to suspended.

#### Scenario: Suspending a license requires reason
- **WHEN** admin suspends a license
- **THEN** a reason MUST be provided and the license status MUST change to suspended

### Requirement: Renew Action
The LicenseResource SHALL have a "Renew" action that sets a new expires_at and changes status back to active.

#### Scenario: Renewing an expired license
- **WHEN** admin renews an expired license with a new expires_at date
- **THEN** the license status MUST change to active and expires_at MUST be updated

### Requirement: Auto-Expire
A daily scheduled command SHALL check for licenses where status=active AND expires_at < today and update them to status=expired.

#### Scenario: License auto-expires when past expiry date
- **WHEN** the daily command runs and a license has expires_at in the past
- **THEN** the license status MUST be updated to expired
