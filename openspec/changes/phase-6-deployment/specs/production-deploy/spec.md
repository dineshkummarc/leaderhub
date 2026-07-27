## ADDED Requirements

### Requirement: Build Assets Locally
Assets SHALL be built locally before deployment. Built files in public/build/ and dist/ SHALL be committed to git.

#### Scenario: Build artifacts are in git
- **WHEN** developer runs `git pull` on the server
- **THEN** apps/*/public/build/ and packages/design-tokens/dist/ MUST be present

### Requirement: Composer Install
For each app and core, composer install --no-dev --optimize-autoloader SHALL be run.

#### Scenario: Composer install succeeds
- **WHEN** developer runs composer install --no-dev --optimize-autoloader in apps/website
- **THEN** the command MUST complete without errors

### Requirement: Migrations
php artisan migrate --path=../../core/database/migrations --force SHALL be run for each app, followed by php artisan migrate --force.

#### Scenario: All migrations run in production
- **WHEN** developer runs migrations on the server
- **THEN** all shared and app-specific tables MUST be created without errors

### Requirement: Three Apps Live
After deployment, https://leaderhub.id, https://erp.leaderhub.id/hub/login, and https://gym.leaderhub.id/gym/login SHALL all return HTTP 200.

#### Scenario: All three apps are live
- **WHEN** user accesses all three URLs
- **THEN** all three MUST return HTTP 200 with the correct app content
