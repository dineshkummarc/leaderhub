## ADDED Requirements

### Requirement: Member Code Auto-Generate
The member_code SHALL be auto-generated in format `M-XXXX` (4-digit zero-padded sequential PER TENANT).

#### Scenario: Member code is per-tenant sequential
- **WHEN** Tenant A has 5 members and Tenant B has 2 members
- **THEN** Tenant A's next member code MUST be M-0006 and Tenant B's next MUST be M-0003

### Requirement: Member Resource
A `MemberResource` SHALL exist with form (member_code auto, name, photo, gender, dob, phone, email, address, emergency contact, joined_at, status) and table (member_code, name, phone, status badge).

#### Scenario: Member is tenant-scoped
- **WHEN** Tenant A creates a member
- **THEN** the member MUST only be visible when Tenant A is the current tenant
