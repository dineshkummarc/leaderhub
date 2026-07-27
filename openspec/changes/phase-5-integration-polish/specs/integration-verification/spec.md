## ADDED Requirements

### Requirement: Product Sync
A product created in LeaderP with status=published SHALL immediately appear on the website product listing (shared database).

#### Scenario: Product created in hub appears on website
- **WHEN** admin creates a product with status published in LeaderP
- **THEN** the product MUST appear on the website /products page without any delay

### Requirement: Inquiry to Sales Order Flow
An inquiry submitted on the website SHALL appear in LeaderP. The Convert Inquiry action SHALL create an SO and update inquiry status to converted.

#### Scenario: Full inquiry to SO flow works
- **WHEN** a visitor submits an inquiry on the website
- **AND** admin converts it to a sales order in LeaderP
- **THEN** the inquiry status MUST be converted and the SO MUST contain the inquiry items

### Requirement: License Flow
A license issued in LeaderP SHALL be validatable by LeadGym. Suspending in LeaderP SHALL result in read-only mode in LeadGym after sync.

#### Scenario: License suspend propagates to LeadGym
- **WHEN** admin suspends a license in LeaderP
- **AND** the daily sync command runs in LeadGym
- **THEN** the tenant's license_status MUST change to suspended and the panel MUST enter read-only mode

### Requirement: Multi-Tenant Isolation
Tenant A's data SHALL NOT be visible to Tenant B.

#### Scenario: Cross-tenant data is not accessible
- **WHEN** Tenant A creates a member
- **THEN** Tenant B MUST NOT see that member in their list
