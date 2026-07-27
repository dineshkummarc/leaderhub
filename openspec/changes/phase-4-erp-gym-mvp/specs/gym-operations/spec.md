## ADDED Requirements

### Requirement: Check-In Validation
When recording a check-in, the system SHALL validate that the member has an active subscription (status=active AND end_date >= today). If not, reject with error.

#### Scenario: Check-in rejected for expired subscription
- **WHEN** a check-in is attempted for a member with no active subscription
- **THEN** the check-in MUST be rejected with error "Member tidak memiliki subscription aktif"

### Requirement: Capacity Tracking
The gym dashboard SHALL display current capacity (check-ins today where check_out_at is null) vs max capacity from tenant settings.

#### Scenario: Capacity warning when full
- **WHEN** current check-ins equal max capacity
- **THEN** the dashboard MUST display a capacity warning

### Requirement: All Models Use BelongsToTenant
All gym operation models (Service, MemberService, Enquiry, FollowUp, Expense, CheckIn) SHALL use the BelongsToTenant trait.

#### Scenario: Expense is tenant-scoped
- **WHEN** Tenant A creates an expense
- **THEN** the expense MUST only be visible when Tenant A is the current tenant
