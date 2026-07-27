## ADDED Requirements

### Requirement: API Endpoint
A POST route `/api/license/validate` SHALL exist in the hub app, accepting `license_key` (required string) and `tenant_id` (required integer).

#### Scenario: Valid license returns valid true
- **WHEN** a POST request is sent with a valid, active license key
- **THEN** the response MUST include `valid: true`, `status: active`, plan, features, expires_at, max_members

#### Scenario: Expired license returns valid false
- **WHEN** a POST request is sent with an expired license key
- **THEN** the response MUST include `valid: false` and `status: expired`

### Requirement: Rate Limiting
The endpoint SHALL rate limit to 60 requests per IP per minute. If exceeded, return 429.

#### Scenario: 61st request is rate limited
- **WHEN** 61 requests are sent from the same IP within 1 minute
- **THEN** the 61st request MUST return HTTP 429

### Requirement: Validation Logging
Every API call SHALL create a `LicenseValidation` record with license_id, tenant_id, valid, status, validated_at, ip_address.

#### Scenario: Validation is logged
- **WHEN** a license validation API call is made
- **THEN** a LicenseValidation record MUST be created in the database
