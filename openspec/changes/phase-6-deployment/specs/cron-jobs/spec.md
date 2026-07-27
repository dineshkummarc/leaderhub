## ADDED Requirements

### Requirement: Scheduler Cron
Each app SHALL have a scheduler cron running every minute: `* * * * * cd /home/leaderhub/apps/{app} && php artisan schedule:run`.

#### Scenario: Scheduler runs every minute
- **WHEN** developer checks cPanel cron jobs
- **THEN** three scheduler crons (website, hub, gym) MUST be registered

### Requirement: Queue Worker Cron
Hub and gym SHALL have queue worker crons: `* * * * * cd /home/leaderhub/apps/{app} && php artisan queue:work --stop-when-empty --max-time=50`.

#### Scenario: Queue worker processes jobs
- **WHEN** a job is dispatched to the queue
- **THEN** the cron-based queue worker MUST process it within 1 minute

### Requirement: License Sync Cron
Gym SHALL have a daily license sync cron at 00:00: `0 0 * * * cd /home/leaderhub/apps/gym && php artisan gym:sync-licenses`.

#### Scenario: License sync runs daily
- **WHEN** developer checks the license_validations table after 24 hours
- **THEN** validation records from the daily sync MUST be present

### Requirement: Backup Cron
Database backup SHALL run daily at 03:00. File backup SHALL run weekly Sunday at 04:00. Cleanup SHALL delete backups older than 30 days.

#### Scenario: Database backup is created
- **WHEN** developer checks /home/leaderhub/backups/ after 03:00
- **THEN** a gzipped SQL dump file MUST be present
