## ADDED Requirements

### Requirement: Google Translate Widget
The website SHALL include Google Translate widget initialized with pageLanguage id and includedLanguages en.

#### Scenario: Google Translate widget is loaded
- **WHEN** user loads any website page
- **THEN** the Google Translate script MUST be loaded and the translate element initialized

### Requirement: Manual Override Active
When locale is en and a record has non-null English fields, the English version SHALL be displayed.

#### Scenario: English product name is displayed when available
- **WHEN** session locale is en and a product has name_en set
- **THEN** the product listing MUST display name_en instead of name
