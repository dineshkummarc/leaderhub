## ADDED Requirements

### Requirement: aria-label on Icon Buttons
All icon-only buttons SHALL have aria-label attributes.

#### Scenario: Theme toggle has aria-label
- **WHEN** developer inspects the theme toggle button
- **THEN** an aria-label attribute describing the function MUST be present

### Requirement: Keyboard Navigation
All interactive elements SHALL be reachable via Tab key with visible focus.

#### Scenario: Tab key reaches all buttons
- **WHEN** user presses Tab repeatedly
- **THEN** all buttons, links, and inputs MUST be reachable in logical order with visible focus ring

### Requirement: Reduced Motion
The website SHALL respect prefers-reduced-motion: reduce by disabling transitions.

#### Scenario: Reduced motion disables transitions
- **WHEN** the user's OS has reduced motion enabled
- **THEN** all CSS transitions and animations MUST be disabled
