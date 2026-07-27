## ADDED Requirements

### Requirement: Settings Table
A `settings` table SHALL exist with columns: `id`, `group` (VARCHAR 50), `key` (VARCHAR 100), `value` (TEXT nullable), `type` (VARCHAR 20: text/textarea/boolean/number/json/image), `is_translatable` (BOOLEAN). A unique constraint SHALL be on `(group, key)`.

#### Scenario: Settings table has unique group-key
- **WHEN** developer attempts to insert two settings with the same group and key
- **THEN** the database MUST reject the duplicate with a unique constraint violation

### Requirement: Setting Model
The `Setting` model SHALL be in `core/src/Models/` with static methods `get(key, group?, default?)` and `set(key, value, group?)`.

#### Scenario: Setting get returns default when not found
- **WHEN** `Setting::get('nonexistent_key', 'general', 'default_value')` is called
- **THEN** the method MUST return `default_value`

#### Scenario: Setting set creates or updates
- **WHEN** `Setting::set('site_name', 'LeaderHub', 'general')` is called twice
- **THEN** only one record with key `site_name` in group `general` MUST exist, with value `LeaderHub`

### Requirement: Filament Resource
A `SettingResource` SHALL exist in the admin panel with: form (group select, key input, type select, value textarea, is_translatable toggle), table (group badge, key, type badge, value), and filter by group.

#### Scenario: Admin can create a setting
- **WHEN** admin navigates to `/admin/settings` and fills the create form
- **THEN** a new setting record MUST be created in the database

### Requirement: Seeder
A `SettingSeeder` SHALL populate default settings: site_name, tagline, contact email/phone/whatsapp/address, SEO title/description, social instagram, currency default + exchange_rate.

#### Scenario: Default settings are seeded
- **WHEN** developer runs `php artisan db:seed`
- **THEN** at least 11 settings records MUST exist including site_name, tagline, email, phone, whatsapp
