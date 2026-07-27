## ADDED Requirements

### Requirement: Installation Complete Auto-Creates Warranty
The InstallationResource SHALL have a "Complete" action that updates installation status to completed and auto-creates Warranty records for each SO item with end_date = start_date + product.warranty_period_months.

#### Scenario: Completing installation creates warranties
- **WHEN** admin completes an installation for a SO with 3 items
- **THEN** 3 warranty records MUST be created with end_date = now + product warranty months

### Requirement: Warranty and Claim Resources
`WarrantyResource` (view, update status to claimed) and `WarrantyClaimResource` (CRUD) SHALL exist.

#### Scenario: Admin can create a warranty claim
- **WHEN** admin creates a warranty claim for a warranty
- **THEN** the claim MUST be saved with a unique claim_number and status open

### Requirement: Support Ticket Resource
A `SupportTicketResource` SHALL exist with priority (low/medium/high/urgent) and status (open/in_progress/resolved/closed).

#### Scenario: Support ticket is created with priority
- **WHEN** admin creates a support ticket with priority urgent
- **THEN** the ticket MUST be saved with priority urgent and status open
