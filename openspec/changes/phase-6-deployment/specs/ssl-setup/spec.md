## ADDED Requirements

### Requirement: AutoSSL for Main Domains
cPanel AutoSSL SHALL be run for leaderhub.id, www.leaderhub.id, erp.leaderhub.id, gym.leaderhub.id. All SHALL have valid SSL.

#### Scenario: No SSL warnings on main domains
- **WHEN** user accesses https://leaderhub.id
- **THEN** the browser MUST NOT show any SSL certificate warning

### Requirement: Wildcard SSL for Tenant Subdomains
A wildcard SSL for *.gym.leaderhub.id SHALL be configured via AutoSSL DNS validation, purchased wildcard cert, or Cloudflare.

#### Scenario: Tenant subdomain has valid SSL
- **WHEN** user accesses https://demo.gym.leaderhub.id
- **THEN** the browser MUST NOT show any SSL certificate warning

### Requirement: HTTPS Redirect
All HTTP requests SHALL be redirected to HTTPS via .htaccess.

#### Scenario: HTTP redirects to HTTPS
- **WHEN** user accesses http://leaderhub.id
- **THEN** the browser MUST be redirected to https://leaderhub.id
