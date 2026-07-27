## ADDED Requirements

### Requirement: Website Accessible
https://leaderhub.id SHALL return HTTP 200 with homepage content. Load time SHALL be under 3 seconds.

#### Scenario: Homepage loads in production
- **WHEN** user navigates to https://leaderhub.id
- **THEN** the homepage MUST load in under 3 seconds with all sections visible

### Requirement: Admin Login Works
Logging in at https://erp.leaderhub.id/hub/login with admin@leaderhub.id / password SHALL succeed.

#### Scenario: Admin can login in production
- **WHEN** admin enters credentials at the production hub login page
- **THEN** the dashboard MUST be displayed

### Requirement: Integration Flows Work
Product sync, inquiry to SO, license issue to LeadGym activation, and multi-tenant isolation SHALL all work in production.

#### Scenario: Product sync works in production
- **WHEN** admin creates a product in LeaderP production
- **THEN** the product MUST appear on the production website

### Requirement: Sitemap and Robots Accessible
https://leaderhub.id/sitemap.xml SHALL return valid XML. https://leaderhub.id/robots.txt SHALL be accessible.

#### Scenario: Sitemap is valid XML
- **WHEN** developer accesses /sitemap.xml
- **THEN** the response MUST be valid XML with a urlset root element

### Requirement: Backup Running
After 24 hours, /home/leaderhub/backups/ SHALL contain a database backup file.

#### Scenario: Backup file exists after 24 hours
- **WHEN** developer checks the backups folder 24 hours after deployment
- **THEN** at least one gzipped SQL dump file MUST be present
