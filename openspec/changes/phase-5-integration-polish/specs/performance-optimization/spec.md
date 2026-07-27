## ADDED Requirements

### Requirement: Cache Configuration
In production, config:cache, route:cache, view:cache, and event:cache SHALL be run.

#### Scenario: Config is cached in production
- **WHEN** developer runs php artisan config:cache
- **THEN** the bootstrap/cache/config.php file MUST be generated

### Requirement: PageSpeed Target
Homepage SHALL score above 80 on Google PageSpeed Insights mobile.

#### Scenario: PageSpeed score meets target
- **WHEN** the homepage is tested with PageSpeed Insights
- **THEN** the mobile score MUST be above 80
