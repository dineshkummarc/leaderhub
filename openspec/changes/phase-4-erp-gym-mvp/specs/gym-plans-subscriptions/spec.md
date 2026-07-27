## ADDED Requirements

### Requirement: Auto-Calculate End Date
When creating a subscription, end_date SHALL be auto-calculated as start_date + plan.duration_days. The end_date field SHALL be disabled in the form.

#### Scenario: End date is calculated from plan duration
- **WHEN** a subscription is created with start_date 2026-01-01 and plan duration 30 days
- **THEN** end_date MUST be auto-calculated as 2026-01-31

### Requirement: Freeze Action
The SubscriptionResource SHALL have a "Freeze" action that sets status to frozen, records frozen_at, and extends end_date by frozen_days.

#### Scenario: Freezing extends end date
- **WHEN** a subscription with end_date 2026-02-01 is frozen for 7 days
- **THEN** the end_date MUST be extended to 2026-02-08 and status MUST be frozen

### Requirement: Auto-Expire Subscriptions
A daily command SHALL update subscriptions where status=active AND end_date < today to status=expired.

#### Scenario: Expired subscription is auto-updated
- **WHEN** the daily command runs and a subscription has end_date in the past
- **THEN** the subscription status MUST be updated to expired
