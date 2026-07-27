## ADDED Requirements

### Requirement: PHP Version
PHP 8.2 or higher SHALL be configured for all subdomains via cPanel MultiPHP Manager.

#### Scenario: PHP version is 8.2+
- **WHEN** developer runs `php -v` via SSH
- **THEN** the version MUST be 8.2 or higher

### Requirement: MySQL Database
A MySQL 8.0 database named leaderhub_db SHALL exist with user leaderhub having ALL PRIVILEGES and charset utf8mb4_unicode_ci.

#### Scenario: Database is accessible
- **WHEN** developer connects to MySQL via `mysql -u leaderhub -p leaderhub_db`
- **THEN** the connection MUST succeed and the database MUST have utf8mb4_unicode_ci charset

### Requirement: Subdomains
Three subdomains SHALL be created: leaderhub.id, erp.leaderhub.id, gym.leaderhub.id, plus wildcard *.gym.leaderhub.id, each pointing to the correct app public directory.

#### Scenario: Subdomain resolves to correct app
- **WHEN** user navigates to https://erp.leaderhub.id
- **THEN** the LeaderP app MUST be served (not the website or gym app)

### Requirement: PHP Extensions
Extensions mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, curl, gd, fileinfo, bcmath, intl, zip, exif, opcache SHALL be enabled.

#### Scenario: All required extensions are enabled
- **WHEN** developer runs `php -m`
- **THEN** all listed extensions MUST appear in the output
