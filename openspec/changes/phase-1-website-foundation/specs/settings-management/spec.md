# Spec: settings-management

## Requirements

### REQ-001: Settings Table
A `settings` table MUST exist with columns: `id`, `group` (VARCHAR 50), `key` (VARCHAR 100), `value` (TEXT nullable), `type` (VARCHAR 20: text/textarea/boolean/number/json/image), `is_translatable` (BOOLEAN). Unique constraint on `(group, key)`.

### REQ-002: Setting Model
The `Setting` model MUST be in `core/src/Models/` with static methods `get(key, group?, default?)` and `set(key, value, group?)`.

### REQ-003: Filament Resource
A `SettingResource` MUST exist in the admin panel with: form (group select, key input, type select, value textarea, is_translatable toggle), table (group badge, key, type badge, value), filter by group.

### REQ-004: Seeder
A `SettingSeeder` MUST populate default settings: site_name, tagline, contact email/phone/whatsapp/address, SEO title/description, social instagram, currency default + exchange_rate.

### REQ-005: CRUD Works
Admin MUST be able to create, edit, and delete settings via the Filament resource without errors.